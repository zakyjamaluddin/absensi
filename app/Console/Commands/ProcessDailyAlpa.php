<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\JamSetting;
use App\Models\User; // <--- Import Model User
use App\Models\Guru; // <--- Import Model Guru
use App\Services\WhatsAppService; // <--- Import Service WA
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProcessDailyAlpa extends Command
{
    protected $signature = 'app:process-daily-alpa';

    protected $description = 'Otomatis mencatat siswa Alpa dan mengirimkan rekap harian via WhatsApp ke Guru Admin';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        // 1. Cek libur pekanan
        if (Carbon::today()->isSunday()) {
            $this->info('Hari ini adalah hari Minggu. Proses Alpa diabaikan.');
            return Command::SUCCESS;
        }

        // 2. Cek kalender hari libur
        $isHoliday = HariLibur::where('tanggal', $today)->exists();
        if ($isHoliday) {
            $this->info('Hari ini adalah hari libur sekolah. Proses Alpa diabaikan.');
            return Command::SUCCESS;
        }

        // 3. Ambil waktu selesai_masuk dari database
        $settings = JamSetting::first();
        if (!$settings) {
            $this->error('Pengaturan jam belum dikonfigurasi. Proses Alpa dibatalkan.');
            return Command::FAILURE;
        }

        $now = Carbon::now();
        $timeNow = $now->toTimeString();

        // Cek apakah waktu sekarang sudah melewati jam selesai masuk
        if ($timeNow < $settings->selesai_masuk) {
            $this->info("Belum waktunya memproses Alpa. Jendela absen masuk masih dibuka s.d {$settings->selesai_masuk}.");
            return Command::SUCCESS;
        }

        // Cek apakah hari ini sudah pernah diproses Alpa-nya
        $cacheKey = 'alpa_processed_' . $today;
        if (Cache::has($cacheKey)) {
            $this->info('Absen Alpa untuk hari ini sudah pernah diproses sebelumnya.');
            return Command::SUCCESS;
        }

        // 4. Proses Alpa untuk siswa yang membolos hari ini
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

        // Kunci proses hari ini di Cache agar tidak berjalan berulang kali
        Cache::forever($cacheKey, true);

        $this->info("Sukses mencatat {$count} siswa sebagai Alpa hari ini.");

        // =======================================================
        // 5. SISTEM KIRIM WA REKAP KE GURU ADMIN OTOMATIS
        // =======================================================
        if ($count > 0) {
            $this->info('Menyusun teks laporan WhatsApp...');

            // Ambil data Alpa hari ini yang dikelompokkan berdasarkan Kelas
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

            // Susun isi pesan WhatsApp dengan format tebal markdown (*)
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
            $message .= "_Laporan dikirim otomatis oleh Sistem Absensi Digital PP._";

            // Cari semua USER yang memiliki ROLE 'admin' (Spatie Shield)
            $adminUsers = User::role('super_admin')->get();

            $waCount = 0;
            foreach ($adminUsers as $user) {
                // Pastikan user ber-role admin ini memiliki kaitan profil ke data GURU
                if ($user->userable instanceof Guru) {
                    $guru = $user->userable;

                    if (!empty($guru->no_hp)) {
                        // Kirim pesan rekap ke WA Guru Admin
                        WhatsAppService::send($guru->no_hp, $message);
                        $waCount++;
                    }
                }
            }

            $this->info("Laporan WhatsApp berhasil dikirim ke {$waCount} nomor Guru Admin.");
        } else {
            $this->info('Hari ini nihil alpa. Tidak ada laporan WhatsApp yang perlu dikirim.');
        }

        return Command::SUCCESS;
    }
}
