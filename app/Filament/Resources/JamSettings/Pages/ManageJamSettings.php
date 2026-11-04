<?php

namespace App\Filament\Resources\JamSettings\Pages;

use App\Filament\Resources\JamSettings\JamSettingResource;
use App\Models\JamSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageJamSettings extends ManageRecords
{
    protected static string $resource = JamSettingResource::class;

    protected function getHeaderActions(): array
    {
        $setting = JamSetting::first();

        return [
            Action::make('edit_setting')
                ->label('Ubah Pengaturan Jam')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->fillForm(fn () => $setting ? $setting->toArray() : [])
                ->form(JamSettingResource::getFormComponents())
                ->modalHeading('Ubah Pengaturan Jam Presensi')
                ->modalWidth('5xl')
                ->action(function (array $data) use ($setting) {
                    if ($setting) {
                        $setting->update($data);
                    } else {
                        JamSetting::create($data);
                    }

                    Notification::make()
                        ->title('Pengaturan Disimpan')
                        ->body('Konfigurasi jam presensi berhasil diperbarui.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
