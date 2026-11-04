<?php

namespace App\Filament\Resources\HariLiburs\Pages;

use App\Filament\Resources\HariLiburs\HariLiburResource;
use App\Services\HariLiburService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageHariLiburs extends ManageRecords
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_libur_nasional')
                ->label('Sinkronkan Libur Nasional')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Kalender Libur Nasional')
                ->modalDescription('Sistem akan mengambil dan memperbarui daftar hari libur nasional resmi Indonesia dari kalender resmi. Lanjutkan?')
                ->action(function () {
                    $count = HariLiburService::syncNationalHolidays();

                    Notification::make()
                        ->title('Sinkronisasi Berhasil')
                        ->body("Sebanyak {$count} hari libur nasional dan cuti bersama berhasil disinkronkan ke dalam sistem.")
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Tambah Hari Libur Manual'),
        ];
    }
}
