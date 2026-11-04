<?php

namespace App\Filament\Resources\KunjunganPerpustakaans\Pages;

use App\Filament\Resources\KunjunganPerpustakaans\KunjunganPerpustakaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKunjunganPerpustakaans extends ManageRecords
{
    protected static string $resource = KunjunganPerpustakaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Catat Kunjungan Manual')
                ->icon('heroicon-o-plus')
                ->modalWidth('md'),
        ];
    }
}
