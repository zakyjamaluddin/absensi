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

    protected static ?string $title = 'Buku Panduan';

    // PENGATURAN GRUP NAVIGASI (Sejajar dengan Users, Roles, dsb.)
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Sistem';
    protected static ?int $navigationSort = 4; // Berada di paling bawah
}
