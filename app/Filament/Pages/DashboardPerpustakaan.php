<?php

namespace App\Filament\Pages;

use App\Filament\Pages\DashboardPerpustakaan\Widgets\BukuTerpinjamHariIniTable;
use App\Filament\Pages\DashboardPerpustakaan\Widgets\HeaderGreetingPerpustakaanWidget;
use App\Filament\Pages\DashboardPerpustakaan\Widgets\PerpustakaanStatsOverview;
use App\Filament\Pages\DashboardPerpustakaan\Widgets\TenggatBukuKembaliTable;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DashboardPerpustakaan extends BaseDashboard
{
    protected static string $routePath = 'dasbor-perpustakaan';
    protected static ?string $slug = 'dasbor-perpustakaan';

    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBuildingLibrary;
    protected static ?string $title = 'Dashboard Perpustakaan';
    protected static ?string $navigationLabel = 'Dashboard Perpustakaan';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        // admin_absensi & staff_absensi tidak memiliki akses ke dasbor perpustakaan
        if ($user->hasAnyRole(['admin_absensi', 'staff_absensi'])) {
            return false;
        }

        return $user->hasAnyRole(['admin_perpustakaan', 'staff_perpustakaan']) || $user->can('View:DashboardPerpustakaan');
    }

    public function getWidgets(): array
    {
        return [
            HeaderGreetingPerpustakaanWidget::class,
            PerpustakaanStatsOverview::class,
            BukuTerpinjamHariIniTable::class,
            TenggatBukuKembaliTable::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 1;
    }
}
