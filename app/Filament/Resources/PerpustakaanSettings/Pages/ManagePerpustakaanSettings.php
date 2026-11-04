<?php

namespace App\Filament\Resources\PerpustakaanSettings\Pages;

use App\Filament\Resources\PerpustakaanSettings\PerpustakaanSettingResource;
use App\Models\PerpustakaanSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePerpustakaanSettings extends ManageRecords
{
    protected static string $resource = PerpustakaanSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Inisialisasi Pengaturan')
                ->hidden(fn () => PerpustakaanSetting::count() > 0),
        ];
    }
}
