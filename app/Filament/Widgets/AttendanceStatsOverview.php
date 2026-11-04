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
        $user = auth()->user();

        // Cek apakah user adalah Wali Kelas dengan kelas binaan aktif
        $isWaliKelas = $user && $user->hasAnyRole(['wali_kelas', 'Wali Kelas']);
        $kelas = ($isWaliKelas && $user->userable instanceof Guru) ? $user->userable->kelas : null;
        $siswaIds = $kelas ? $kelas->siswas()->pluck('id') : null;

        // 1. HITUNG SISWA SUDAH MASUK HARI INI (Tepat Waktu + Terlambat)
        $sudahMasukQuery = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->whereIn('status_masuk', ['Tepat Waktu', 'Terlambat']);
        if ($siswaIds) {
            $sudahMasukQuery->whereIn('absensable_id', $siswaIds);
        }
        $sudahMasukCount = $sudahMasukQuery->count();

        // 2. HITUNG SISWA TERLAMBAT HARI INI
        $terlambatQuery = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->where('status_masuk', 'Terlambat');
        if ($siswaIds) {
            $terlambatQuery->whereIn('absensable_id', $siswaIds);
        }
        $terlambatCount = $terlambatQuery->count();

        // 3. HITUNG SISWA ALPA HARI INI
        $alpaQuery = Absensi::where('tanggal', $today)
            ->where('absensable_type', Siswa::class)
            ->where('status_masuk', 'Alpa');
        if ($siswaIds) {
            $alpaQuery->whereIn('absensable_id', $siswaIds);
        }
        $alpaCount = $alpaQuery->count();

        $descMasuk = $kelas ? "Siswa {$kelas->nama_kelas} yang sudah hadir" : 'Total siswa yang sudah hadir di kelas';
        $descTelat = $kelas ? "Siswa {$kelas->nama_kelas} scan melewati jam masuk" : 'Siswa yang scan melewati jam masuk';
        $descAlpa = $kelas ? "Siswa {$kelas->nama_kelas} membolos/alpa hari ini" : 'Siswa yang membolos/tidak hadir hari ini';

        return [
            Stat::make('Sudah Masuk Hari Ini', $sudahMasukCount . ' Siswa')
                ->description($descMasuk)
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Terlambat Hari Ini', $terlambatCount . ' Siswa')
                ->description($descTelat)
                ->descriptionIcon('heroicon-m-clock')
                ->color($terlambatCount > 0 ? 'warning' : 'gray'),

            Stat::make('Alpa Hari Ini', $alpaCount . ' Siswa')
                ->description($descAlpa)
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($alpaCount > 0 ? 'danger' : 'gray'),
        ];
    }
}
