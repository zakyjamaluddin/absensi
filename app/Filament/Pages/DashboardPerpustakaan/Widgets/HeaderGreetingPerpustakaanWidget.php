<?php

namespace App\Filament\Pages\DashboardPerpustakaan\Widgets;

use Filament\Widgets\Widget;

class HeaderGreetingPerpustakaanWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.pages.dashboard-perpustakaan.widgets.header-greeting-perpustakaan-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';
}
