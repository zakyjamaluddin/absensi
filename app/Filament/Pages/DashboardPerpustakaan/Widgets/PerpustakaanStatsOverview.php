<?php

namespace App\Filament\Pages\DashboardPerpustakaan\Widgets;

use App\Models\KunjunganPerpustakaan;
use App\Models\PeminjamanBuku;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PerpustakaanStatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '15s';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();

        // 1. SISWA MENGUNJUNGI PERPUSTAKAAN HARI INI
        $kunjunganHariIni = KunjunganPerpustakaan::where('tanggal', $today)->count();

        // 2. BUKU YANG TERPINJAM HARI INI
        $pinjamHariIni = PeminjamanBuku::where('tanggal_pinjam', $today)->count();

        // 3. TOTAL BUKU TERPINJAM (STATUS MASIH DIPINJAM / TERLAMBAT / BELUM KEMBALI)
        $totalBukuTerpinjam = PeminjamanBuku::whereIn('status', ['Dipinjam', 'Terlambat'])
            ->whereNull('tanggal_kembali')
            ->count();

        return [
            Stat::make('Kunjungan Siswa Hari Ini', number_format($kunjunganHariIni) . ' Siswa')
                ->description('Total santri/siswa yang hadir ke perpustakaan hari ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Buku Terpinjam Hari Ini', number_format($pinjamHariIni) . ' Eksemplar')
                ->description('Total buku yang dipinjam pada hari ini')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Total Buku Terpinjam', number_format($totalBukuTerpinjam) . ' Eksemplar')
                ->description('Buku yang sedang beredar / belum dikembalikan')
                ->descriptionIcon('heroicon-m-bookmark')
                ->color($totalBukuTerpinjam > 0 ? 'warning' : 'gray'),
        ];
    }
}
