<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class HeaderGreetingWidget extends Widget
{
    // Mengarahkan ke file blade kustom
    protected  string $view = 'filament.widgets.header-greeting-widget';

    // Urutan ke-1 (Paling Atas)
    protected static ?int $sort = 1;

    // Paksa agar melebar penuh 100% melintang di atas layar
    protected int | string | array $columnSpan = 'full';
}
