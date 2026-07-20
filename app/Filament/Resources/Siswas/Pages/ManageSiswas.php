<?php

namespace App\Filament\Resources\Siswas\Pages;

use App\Filament\Imports\SiswaImporter;
use App\Filament\Resources\Siswas\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSiswas extends ManageRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
{
    return [
        Actions\CreateAction::make(),
        Actions\ImportAction::make()
            ->importer(SiswaImporter::class)
    ];
}
}
