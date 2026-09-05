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
    protected ?string $pollingInterval = '15s';

    // Urutan ke-2 (Tengah)
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Jalankan auto-process alpa harian (silent)
        // \App\Models\Absensi::autoProcessAlpa();

        $today = Carbon::today()->toDateString();

        // 1. HITUNG SISWA SUDAH MASUK HARI INI (Tepat Waktu + Terlambat)
        $sudahMasukCount = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->whereIn('status_masuk', ['Tepat Waktu', 'Terlambat'])
            ->count();

        // 2. HITUNG SISWA TERLAMBAT HARI INI
        $terlambatCount = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->where('status_masuk', 'Terlambat')
            ->count();

        // 3. HITUNG SISWA ALPA HARI INI
        $alpaCount = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->where('status_masuk', 'Alpa')
            ->count();

        return [
            Stat::make('Sudah Masuk Hari Ini', $sudahMasukCount . ' Siswa')
                ->description('Total siswa yang sudah hadir di kelas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Terlambat Hari Ini', $terlambatCount . ' Siswa')
                ->description('Siswa yang scan melewati jam masuk')
                ->descriptionIcon('heroicon-m-clock')
                ->color($terlambatCount > 0 ? 'warning' : 'gray'),

            Stat::make('Alpa Hari Ini', $alpaCount . ' Siswa')
                ->description('Siswa yang membolos/tidak hadir hari ini')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($alpaCount > 0 ? 'danger' : 'gray'),
        ];
    }
}
