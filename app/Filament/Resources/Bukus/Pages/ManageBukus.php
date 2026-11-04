<?php

namespace App\Filament\Resources\Bukus\Pages;

use App\Filament\Resources\Bukus\BukuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBukus extends ManageRecords
{
    protected static string $resource = BukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Buku Baru')
                ->icon('heroicon-o-plus')
                ->modalWidth('2xl'),
        ];
    }
}
