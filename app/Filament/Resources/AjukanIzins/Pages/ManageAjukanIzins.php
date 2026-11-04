<?php

namespace App\Filament\Resources\AjukanIzins\Pages;

use App\Filament\Resources\AjukanIzins\AjukanIzinResource;
use App\Models\Guru;
use App\Models\Siswa;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageAjukanIzins extends ManageRecords
{
    protected static string $resource = AjukanIzinResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Ajukan Izin Baru')
                ->icon('heroicon-o-paper-airplane')
                ->modalHeading('Formulir Pengajuan Izin / Sakit')
                ->modalWidth('xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $user = auth()->user();
                    if ($user && $user->userable) {
                        $data['izinable_type'] = get_class($user->userable);
                        $data['izinable_id'] = $user->userable_id;
                    } elseif ($user && $user->hasRole('super_admin')) {
                        if (empty($data['izinable_type'])) {
                            $firstSiswa = Siswa::first();
                            $data['izinable_type'] = Siswa::class;
                            $data['izinable_id'] = $firstSiswa?->id;
                        }
                    }
                    $data['status'] = 'Menunggu';
                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Permohonan Terkirim')
                        ->body('Pengajuan izin Anda berhasil dikirim dan sedang menunggu persetujuan Guru Piket.')
                ),
        ];
    }
}
