<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;

class Dokumentasi extends Page
{
    // ICON: Menggunakan ikon Buku Terbuka yang anggun
    protected static string | BackedEnum | null $navigationIcon = \Filament\Support\Icons\Heroicon::OutlinedBookOpen;

    protected string $view = 'filament.pages.dokumentasi';

    protected static ?string $title = 'Buku Panduan & Dokumentasi Absaka';

    protected static ?string $navigationLabel = 'Buku Panduan';

    // PENGATURAN GRUP NAVIGASI (Sejajar dengan Users, Roles, dsb.)
    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan & Sistem';
    protected static ?int $navigationSort = 5; // Berada di paling bawah

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['staff_absensi', 'staff_perpustakaan'])) {
            return false;
        }

        return $user->hasAnyRole(['admin_absensi', 'admin_perpustakaan']) || $user->can('View:Dokumentasi');
    }
}
