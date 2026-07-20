<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Schema;

class ManageAbsensis extends ManageRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('input_manual')
                ->label('Input Absen Manual')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form(AbsensiResource::getFormComponents()) // <--- Diubah memanggil helper getFormComponents()
                ->action(function (array $data) {
                    $exists = Absensi::where('tanggal', $data['tanggal'])
                        ->where('absensable_type', $data['absensable_type'])
                        ->where('absensable_id', $data['absensable_id'])
                        ->exists();

                    if ($exists) {
                        Notification::make()
                            ->title('Gagal Input Manual')
                            ->body('Data absensi orang tersebut pada tanggal yang dipilih sudah ada di sistem.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Absensi::create($data);

                    Notification::make()
                        ->title('Berhasil')
                        ->body('Data absensi berhasil dicatat secara manual.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
