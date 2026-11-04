<?php

namespace App\Filament\Resources\JadwalGuruPikets\Pages;

use App\Filament\Resources\JadwalGuruPikets\JadwalGuruPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJadwalGuruPikets extends ManageRecords
{
    protected static string $resource = JadwalGuruPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Guru Piket')
                ->modalHeading('Jadwalkan Guru Piket')
                ->modalWidth('lg'),
        ];
    }
}
