<?php

namespace App\Filament\Resources\WaSettings\Pages;

use App\Filament\Resources\WaSettings\WaSettingResource;
use App\Services\WhatsAppService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageWaSettings extends ManageRecords
{
    protected static string $resource = WaSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Token Sidobe')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->hidden(fn () => WaSettingResource::getModel()::count() > 0), // Sembunyikan tombol jika sudah ada token
            // TOMBOL TES KONEKSI TER-UPGRADE (DENGAN REVELATOR ERROR)
            Action::make('tes_koneksi')
                ->label('Tes Koneksi WA')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->hidden(fn () => WaSettingResource::getModel()::count() == 0)
                ->form([
                    \Filament\Forms\Components\TextInput::make('nomor_tes')
                        ->label('Masukkan Nomor WA Anda')
                        ->placeholder('Contoh: 081234567890')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $message = "🔌 *TES KONEKSI ABSENSI DIGITAL*\n\nKoneksi sistem absensi ke platform *Sidobe.com* berhasil terhubung 100% aktif!\n\n_Waktu: " . now()->format('d-m-Y H:i:s') . "_";

                    // Ambil hasil analisis kiriman
                    $result = WhatsAppService::send($data['nomor_tes'], $message);

                    if ($result['success']) {
                        Notification::make()
                            ->title('Koneksi Berhasil!')
                            ->body('Pesan tes koneksi berhasil terkirim ke nomor WhatsApp Anda.')
                            ->success()
                            ->send();
                    } else {
                        // Tampilkan pesan error spesifik dari Sidobe di layar
                        Notification::make()
                            ->title('Koneksi Gagal')
                            ->body($result['message']) // <--- Menampilkan pesan error asli (misal: Token Invalid / Saldo Habis / Salah URL)
                            ->danger()
                            ->persistent() // Biarkan notifikasi tetap di layar sampai di-close agar mudah dibaca
                            ->send();
                    }
                }),
        ];
    }
}
