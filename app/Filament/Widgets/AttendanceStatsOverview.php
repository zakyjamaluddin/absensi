<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Guru;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AttendanceStatsOverview extends BaseWidget
{
    // Fitur Live Polling: Dashboard akan otomatis refresh data setiap 15 detik secara real-time!
    protected  ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();

        // 1. Hitung jumlah Siswa yang sudah hadir hari ini
        $siswaHadirCount = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            //tambahkan status kehadiran yang tepat wakt
            ->whereIn('status_masuk', ['Tepat Waktu'])
            ->count();

        // 2. Hitung jumlah Guru yang sudah hadir hari ini
        $guruHadirCount = Absensi::where('tanggal', $today)
            ->where('absensable_type', Guru::class)
            ->count();

        // 3. Hitung jumlah keterlambatan hari ini (Gabungan Siswa & Guru)
        $terlambatCount = Absensi::where('tanggal', $today)
            ->whereIn('status_masuk', ['Alpa', 'Terlambat'])
            ->count();

        return [
            // Stat 1: Kehadiran Siswa (Warna Biru/Info)
            Stat::make('Siswa Hadir Hari Ini', $siswaHadirCount . ' Santri')
                ->description('Santri yang sudah melakukan tap masuk')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            // Stat 2: Kehadiran Guru/Ustadz (Warna Hijau/Success)
            Stat::make('Ustadz Hadir Hari Ini', $guruHadirCount . ' Orang')
                ->description('Asatidzah yang sudah tap masuk')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            // Stat 3: Total Terlambat Hari Ini (Warna Merah/Danger jika ada yang terlambat)
            Stat::make('Terlambat Hari Ini', $terlambatCount . ' Pelanggaran')
                ->description('Total siswa & guru terlambat hari ini')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($terlambatCount > 0 ? 'danger' : 'gray'),
        ];
    }
}
