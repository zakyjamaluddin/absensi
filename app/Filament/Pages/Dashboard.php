<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // super_admin memiliki akses penuh
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // admin_perpustakaan & staff_perpustakaan tidak diberi akses ke dashboard utama
        if ($user->hasAnyRole(['admin_perpustakaan', 'staff_perpustakaan'])) {
            return false;
        }

        return $user->hasAnyRole(['admin_absensi', 'staff_absensi']) || $user->can('View:Dashboard');
    }
}
