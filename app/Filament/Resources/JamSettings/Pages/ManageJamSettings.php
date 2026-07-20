<?php

namespace App\Filament\Resources\JamSettings\Pages;

use App\Filament\Resources\JamSettings\JamSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageJamSettings extends ManageRecords
{
    protected static string $resource = JamSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
