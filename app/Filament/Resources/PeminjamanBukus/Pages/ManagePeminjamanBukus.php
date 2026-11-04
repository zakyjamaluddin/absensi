<?php

namespace App\Filament\Resources\PeminjamanBukus\Pages;

use App\Filament\Resources\PeminjamanBukus\PeminjamanBukuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePeminjamanBukus extends ManageRecords
{
    protected static string $resource = PeminjamanBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Catat Peminjaman')
                ->icon('heroicon-o-plus')
                ->modalWidth('xl'),
        ];
    }
}
