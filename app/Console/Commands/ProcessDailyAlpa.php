<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\User;

use App\Models\Guru;
use App\Models\WaSetting; // <--- Import WaSetting
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProcessDailyAlpa extends Command
{
    // Tambahkan opsi --manual ke dalam signature perintah
    protected $signature = 'app:process-daily-alpa {--manual}';

    protected $description = 'Mencatat siswa Alpa dan mengirimkan rekap harian via WhatsApp';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        // Dapatkan nama hari bahasa Inggris hari ini (misal: 'Friday', 'Sunday', dll.)
        $currentDayName = Carbon::today()->englishDayOfWeek;

        // Ambil array pilihan ganda dari database (jika kosong, default-kan ke array berisi Friday)
        $liburPekanan = $settings->libur_pekanan ?? ['Friday'];

        // CEK 1: Apakah hari ini (misal 'Friday') ada di dalam daftar array hari libur pilihan ganda?
        // (Kecuali jika dipicu secara MANUAL lewat tombol)
        if (!$this->option('manual') && is_array($liburPekanan) && in_array($currentDayName, $liburPekanan)) {
            $this->info("Hari ini adalah hari {$currentDayName} (Libur Pekanan). Proses Alpa diabaikan.");
            return Command::SUCCESS;
        }

        // 2. Cek kalender hari libur (Kecuali jika dipicu MANUAL dari tombol)
        if (!$this->option('manual') && HariLibur::where('tanggal', $today)->exists()) {
            $this->info('Hari ini adalah hari libur sekolah. Proses Alpa diabaikan.');
            return Command::SUCCESS;
        }

        // 3. Ambil konfigurasi WA Setting
        $waSettings = WaSetting::first();
        if (!$waSettings) {
            $this->error('Konfigurasi WA belum diatur. Proses dibatalkan.');
            return Command::FAILURE;
        }

        // JIKA JALAN SECARA OTOMATIS (CRON JOB): Lakukan penyaringan ganda
        if (!$this->option('manual')) {
            // GERBANG 1: Jika admin memilih metode MANUAL, maka Cron Job otomatis dilarang memproses!
            if ($waSettings->tipe_proses_alpa === 'Manual') {
                $this->info('Metode diatur ke Manual. Proses otomatis dibatalkan.');
                return Command::SUCCESS;
            }

            // GERBANG 2: Cek apakah waktu sekarang sudah melewati jam proses alpa kustom
            $now = Carbon::now();
            $timeNow = $now->toTimeString();
            if ($timeNow < $waSettings->jam_proses_alpa) {
                $this->info("Belum waktunya memproses Alpa. Jendela otomatis dijadwalkan pukul {$waSettings->jam_proses_alpa}.");
                return Command::SUCCESS;
            }

            // GERBANG 3: Proteksi agar tidak dobel running dalam sehari
            $cacheKey = 'alpa_processed_' . $today;
            if (Cache::has($cacheKey)) {
                $this->info('Absen Alpa untuk hari ini sudah pernah diproses sebelumnya.');
                return Command::SUCCESS;
            }
        }

        // 4. Jalankan Proses Alpa Utama
        $siswaBelumAbsen = Siswa::whereDoesntHave('absensis', function ($query) use ($today) {
            $query->where('tanggal', $today);
        })->get();

        $count = 0;
        foreach ($siswaBelumAbsen as $siswa) {
            Absensi::create([
                'tanggal'         => $today,
                'absensable_type' => Siswa::class,
                'absensable_id'   => $siswa->id,
                'jam_masuk'       => null,
                'status_masuk'    => 'Alpa',
            ]);
            $count++;
        }

        // Jika jalan otomatis, kunci status hari ini di cache
        if (!$this->option('manual')) {
            Cache::forever('alpa_processed_' . $today, true);
        }

        $this->info("Sukses mencatat {$count} siswa sebagai Alpa hari ini.");

        // 5. Susun & Kirim Laporan WhatsApp
        if ($count > 0) {
            $alpaRecords = Absensi::where('tanggal', $today)
                ->where('status_masuk', 'Alpa')
                ->where('absensable_type', Siswa::class)
                ->get();

            $rekapByKelas = [];
            foreach ($alpaRecords as $record) {
                $siswa = $record->absensable;
                if ($siswa) {
                    $namaKelas = $siswa->kelas->nama_kelas ?? 'Tanpa Kelas';
                    $rekapByKelas[$namaKelas][] = $siswa->nama;
                }
            }

            $formattedDate = Carbon::parse($today)->translatedFormat('l, d F Y');
            $message = "📢 *LAPORAN HARIAN SISWA ALPA (MEMBOLOS)*\n";
            $message .= "🗓️ Hari/Tanggal: *{$formattedDate}*\n";
            $message .= "--------------------------------------------\n\n";

            foreach ($rekapByKelas as $kelas => $siswas) {
                $message .= "🏫 *{$kelas}*:\n";
                foreach ($siswas as $idx => $namaSiswa) {
                    $message .= "  " . ($idx + 1) . ". {$namaSiswa}\n";
                }
                $message .= "\n";
            }

            $message .= "--------------------------------------------\n";
            $message .= "📊 *Total Siswa Alpa Hari Ini: {$count} Santri.*\n\n";
            $message .= "_Laporan dikirim " . ($this->option('manual') ? 'manual oleh Admin' : 'otomatis oleh Sistem') . " Absensi Digital PP._";

            // Kirim ke semua Guru ber-role 'super_admin'
            $adminUsers = User::role('super_admin')->get();

            $waCount = 0;
            foreach ($adminUsers as $user) {
                if ($user->userable instanceof Guru) {
                    $guru = $user->userable;
                    if (!empty($guru->no_hp)) {
                        WhatsAppService::send($guru->no_hp, $message);
                        $waCount++;
                    }
                }
            }

            $this->info("Laporan WhatsApp berhasil dikirim ke {$waCount} nomor Guru Admin.");
        }

        return Command::SUCCESS;
    }
}
