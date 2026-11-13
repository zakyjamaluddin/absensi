<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\User;

use App\Models\Guru;
use App\Models\JamSetting;
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
        $jamSetting = JamSetting::first();

        // Dapatkan nama hari bahasa Inggris hari ini (misal: 'Friday', 'Sunday', dll.)
        $currentDayName = Carbon::today()->englishDayOfWeek;

        // Ambil array pilihan ganda dari database (jika kosong, default-kan ke array berisi Friday)
        $liburPekanan = $jamSetting?->libur_pekanan ?? ['Friday'];
        if (!is_array($liburPekanan)) {
            $decoded = is_string($liburPekanan) ? json_decode($liburPekanan, true) : null;
            $liburPekanan = is_array($decoded) ? $decoded : (array) $liburPekanan;
        }
        $normalizedLibur = array_map('strtolower', (array) $liburPekanan);

        $dayMap = [
            'Sunday'    => ['sunday', 'ahad', 'minggu'],
            'Monday'    => ['monday', 'senin'],
            'Tuesday'   => ['tuesday', 'selasa'],
            'Wednesday' => ['wednesday', 'rabu'],
            'Thursday'  => ['thursday', 'kamis'],
            'Friday'    => ['friday', 'jumat'],
            'Saturday'  => ['saturday', 'sabtu'],
        ];
        $aliases = $dayMap[$currentDayName] ?? [strtolower($currentDayName)];
        $isLiburPekananHariIni = (bool) array_intersect($aliases, $normalizedLibur);

        // CEK 1: Apakah hari ini (misal 'Friday' / 'Sunday') ada di dalam daftar array hari libur pilihan ganda?
        // (Kecuali jika dipicu secara MANUAL lewat tombol)
        if (!$this->option('manual') && $isLiburPekananHariIni) {
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

            // GERBANG 2: Cek apakah waktu sekarang sudah melewati waktu akhir absensi yang diset di database
            $now = Carbon::now();
            $timeNow = $now->toTimeString();
            $batasAkhir = $waSettings->jam_proses_alpa ?: ($jamSetting?->selesai_masuk ?? '08:00:00');

            if ($timeNow < $batasAkhir) {
                $this->info("Belum waktunya memproses Alpa. Batas waktu akhir absensi masuk dijadwalkan pukul {$batasAkhir}.");
                return Command::SUCCESS;
            }

            // GERBANG 3: Proteksi agar tidak dobel running dalam sehari
            $cacheKey = 'alpa_processed_' . $today;
            if (Cache::has($cacheKey)) {
                $this->info('Absen Alpa untuk hari ini sudah pernah diproses sebelumnya.');
                return Command::SUCCESS;
            }
        }

        // 4. Jalankan Proses Alpa Utama (Siswa)
        $siswaBelumAbsen = Siswa::whereDoesntHave('absensis', function ($query) use ($today) {
            $query->where('tanggal', $today)->where('status_masuk', '!=', 'Belum Absen');
        })->get();

        $batasAkhirNotif = $waSettings->jam_proses_alpa ?: ($jamSetting?->selesai_masuk ?? '08:00:00');
        $count = 0;
        $waWaliCount = 0;

        foreach ($siswaBelumAbsen as $siswa) {
            Absensi::updateOrCreate(
                [
                    'tanggal'         => $today,
                    'absensable_type' => Siswa::class,
                    'absensable_id'   => $siswa->id,
                ],
                [
                    'jam_masuk'       => null,
                    'status_masuk'    => 'Alpa',
                ]
            );
            $count++;

            // KIRIM NOTIFIKASI WA KE WALI MURID JIKA FITUR AKTIF
            if ($waSettings->notif_wa_wali_alpa && $siswa->is_notif_wa_aktif && !empty($siswa->nomor_wa_wali)) {
                $res = WhatsAppService::sendNotifAlpaWali($siswa, $today, $batasAkhirNotif);
                if ($res['success']) {
                    $waWaliCount++;
                }
            }
        }

        // 5. Jalankan Proses Alpa untuk Guru
        $hariIndo = Guru::getHariMapping()[$currentDayName] ?? 'Senin';
        $modeGuru = $jamSetting?->mode_absensi_guru ?? 'statis';

        if ($modeGuru === 'jadwal') {
            // Mode dinamis: Hanya guru yang memiliki jadwal mengajar pada hari ini yang wajib hadir
            $gurusBelumAbsen = Guru::whereHas('jadwalMengajars', function ($q) use ($hariIndo) {
                $q->where('hari', $hariIndo);
            })->whereDoesntHave('absensis', function ($query) use ($today) {
                $query->where('tanggal', $today)->where('status_masuk', '!=', 'Belum Absen');
            })->get();
        } else {
            // Mode statis: Semua guru wajib hadir pada hari aktif sekolah
            $gurusBelumAbsen = Guru::whereDoesntHave('absensis', function ($query) use ($today) {
                $query->where('tanggal', $today)->where('status_masuk', '!=', 'Belum Absen');
            })->get();
        }

        $guruAlpaCount = 0;
        $guruAlpaList = [];

        foreach ($gurusBelumAbsen as $guru) {
            Absensi::updateOrCreate(
                [
                    'tanggal'         => $today,
                    'absensable_type' => Guru::class,
                    'absensable_id'   => $guru->id,
                ],
                [
                    'jam_masuk'       => null,
                    'status_masuk'    => 'Alpa',
                ]
            );
            $guruAlpaCount++;

            $jadwalStr = '';
            if ($modeGuru === 'jadwal') {
                $jadwalHariIni = $guru->getJadwalHariIni($today);
                $jadwalStr = $jadwalHariIni->map(fn ($j) => "Jam ke-{$j->jam_ke} ({$j->mata_pelajaran})")->implode(', ');
            }

            $guruAlpaList[] = [
                'nama' => $guru->nama,
                'nip' => $guru->nip,
                'jadwal' => $jadwalStr,
            ];
        }

        // Jika jalan otomatis, kunci status hari ini di cache
        if (!$this->option('manual')) {
            Cache::forever('alpa_processed_' . $today, true);
        }

        $this->info("Sukses mencatat {$count} siswa sebagai Alpa hari ini. ({$waWaliCount} notifikasi WA terkirim ke wali murid).");
        $this->info("Sukses mencatat {$guruAlpaCount} guru sebagai Alpa hari ini (Mode Absensi Guru: {$modeGuru}).");

        // 6. Susun & Kirim Laporan WhatsApp Siswa ke Admin
        $alpaRecords = Absensi::where('tanggal', $today)
            ->where('status_masuk', 'Alpa')
            ->where('absensable_type', Siswa::class)
            ->with(['absensable.kelas'])
            ->get();

        $totalSiswaAlpa = $alpaRecords->count();
        $isNotifSiswaAlpaAktif = $waSettings ? ($waSettings->notif_wa_admin_rekap_alpa ?? true) : true;

        if ($totalSiswaAlpa > 0 && $isNotifSiswaAlpaAktif) {
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
            $message .= "📊 *Total Siswa Alpa Hari Ini: {$totalSiswaAlpa} Santri.*\n\n";
            $message .= "_Laporan dikirim " . ($this->option('manual') ? 'manual oleh Admin' : 'otomatis oleh Sistem') . " Absensi Digital PP._";

            // Kirim ke nomor tujuan rekap alpa (Admin/Kepsek/Piket)
            $targetPhones = WhatsAppService::getAdminRecapPhones($waSettings);

            $waCount = 0;
            foreach ($targetPhones as $phone) {
                WhatsAppService::send($phone, $message);
                $waCount++;
            }

            $this->info("Laporan WhatsApp siswa berhasil dikirim ke {$waCount} nomor tujuan.");
        } elseif (!$isNotifSiswaAlpaAktif) {
            $this->info("Fitur notifikasi rekap siswa alpa ke WhatsApp dinonaktifkan di pengaturan WA.");
        }

        // 7. Susun & Kirim Laporan WhatsApp Guru Alpa ke Admin
        if ($waSettings->report_guru_alpa) {
            // Jika dalam satu eksekusi belum terkumpul (misal saat manual re-run), ambil semua guru alpa hari ini
            if (empty($guruAlpaList)) {
                $guruAlpaRecords = Absensi::where('tanggal', $today)
                    ->where('status_masuk', 'Alpa')
                    ->where('absensable_type', Guru::class)
                    ->with('absensable')
                    ->get();

                foreach ($guruAlpaRecords as $rec) {
                    $g = $rec->absensable;
                    if ($g) {
                        $jadwalStr = '';
                        if ($modeGuru === 'jadwal') {
                            $jadwalHariIni = $g->getJadwalHariIni($today);
                            $jadwalStr = $jadwalHariIni->map(fn ($j) => "Jam ke-{$j->jam_ke} ({$j->mata_pelajaran})")->implode(', ');
                        }
                        $guruAlpaList[] = [
                            'nama' => $g->nama,
                            'nip' => $g->nip,
                            'jadwal' => $jadwalStr,
                        ];
                    }
                }
            }

            if (count($guruAlpaList) > 0) {
                $reportRes = WhatsAppService::sendReportGuruAlpa($guruAlpaList, $today);
                $this->info($reportRes['message'] ?? 'Laporan Guru Alpa diproses.');
            }
        } else {
            $this->info('Fitur auto report Guru Alpa dinonaktifkan di pengaturan WA.');
        }

        return Command::SUCCESS;
    }
}
