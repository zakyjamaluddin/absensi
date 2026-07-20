<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\JamSetting; // <--- Import model JamSetting
use Illuminate\Support\Facades\Cache; // <--- Import Cache
use Carbon\Carbon;

class ProcessDailyAlpa extends Command
{
    protected $signature = 'app:process-daily-alpa';

    protected $description = 'Otomatis mencatat siswa yang belum absen hari ini sebagai Alpa setelah jendela masuk ditutup';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        // 1. Cek libur pekanan (Minggu)
        if (Carbon::today()->isFriday()) {
            $this->info('Hari ini adalah hari Jumat. Proses Alpa diabaikan.');
            return Command::SUCCESS;
        }

        // 2. Cek kalender hari libur
        $isHoliday = HariLibur::where('tanggal', $today)->exists();
        if ($isHoliday) {
            $this->info('Hari ini adalah hari libur sekolah. Proses Alpa diabaikan.');
            return Command::SUCCESS;
        }

        // 3. Ambil waktu selesai_masuk dari database secara dinamis
        $settings = JamSetting::first();
        if (!$settings) {
            $this->error('Pengaturan jam belum dikonfigurasi. Proses Alpa dibatalkan.');
            return Command::FAILURE;
        }

        $now = Carbon::now();
        $timeNow = $now->toTimeString();

        // GERBANG 1: Cek apakah waktu sekarang sudah melewati jam selesai masuk
        if ($timeNow < $settings->selesai_masuk) {
            $this->info("Belum waktunya memproses Alpa. Jendela absen masuk masih dibuka s.d {$settings->selesai_masuk}.");
            return Command::SUCCESS;
        }

        // GERBANG 2: Cek apakah hari ini sudah pernah diproses Alpa-nya
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

        // 5. Kunci proses hari ini di Cache agar menit berikutnya tidak jalan lagi
        Cache::forever($cacheKey, true);

        $this->info("Sukses! Berhasil mencatat {$count} siswa sebagai 'Alpa' hari ini secara dinamis.");
        return Command::SUCCESS;
    }
}
