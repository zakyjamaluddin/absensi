<?php

namespace App\Filament\Resources\PresensiKelasSayas\Widgets;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PresensiKelasSayaStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $user = auth()->user();
        $kelas = ($user && $user->userable instanceof Guru) ? $user->userable->kelas : null;

        if (! $kelas) {
            return [];
        }

        $today = Carbon::today()->toDateString();
        $siswaIds = $kelas->siswas()->pluck('id');
        $totalSiswa = $siswaIds->count();

        // Ambil data absensi hari ini khusus siswa kelas ini
        $absensiHariIni = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->whereIn('absensable_id', $siswaIds)
            ->get();

        $tepatWaktu = $absensiHariIni->where('status_masuk', 'Tepat Waktu')->count();
        $terlambat = $absensiHariIni->where('status_masuk', 'Terlambat')->count();
        $sakitIzin = $absensiHariIni->whereIn('status_masuk', ['Sakit', 'Izin'])->count();
        
        // Siswa yang sudah tap (hadir)
        $totalHadir = $tepatWaktu + $terlambat;
        
        // Siswa yang belum hadir / alpa
        $belumHadir = $totalSiswa - ($totalHadir + $sakitIzin);
        if ($belumHadir < 0) {
            $belumHadir = 0;
        }

        $persenHadir = $totalSiswa > 0 ? round(($totalHadir / $totalSiswa) * 100, 1) : 0;

        return [
            Stat::make('Hadir Tepat Waktu', "{$tepatWaktu} / {$totalSiswa} Siswa")
                ->description("Kehadiran tepat waktu ({$persenHadir}%)")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Terlambat Hari Ini', "{$terlambat} Siswa")
                ->description('Siswa yang scan melewati jam masuk')
                ->descriptionIcon('heroicon-m-clock')
                ->color($terlambat > 0 ? 'warning' : 'gray'),

            Stat::make('Izin / Sakit', "{$sakitIzin} Siswa")
                ->description('Permohonan izin atau sakit')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($sakitIzin > 0 ? 'info' : 'gray'),

            Stat::make('Belum Hadir / Alpa', "{$belumHadir} Siswa")
                ->description($belumHadir > 0 ? 'Perlu dipantau / hubungi wali' : 'Semua siswa sudah ada kabar')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($belumHadir > 0 ? 'danger' : 'success'),
        ];
    }
}
