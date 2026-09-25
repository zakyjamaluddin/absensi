<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;

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

            // 2. TOMBOL SAKTI BARU: Ekspor Semua Kelas Sekaligus Berbasis Multi-Sheet
            Action::make('ekspor_semua_kelas')
                ->label('Ekspor Semua Kelas')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                // Ambil daftar bulan & tahun dinamis dari database yang sudah kita buat sebelumnya
                ->form([
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options(function () {
                            $bulanQuery = Absensi::selectRaw('MONTH(tanggal) as month')->distinct()->orderBy('month', 'asc')->pluck('month')->toArray();
                            $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                            $bulanOptions = [];
                            foreach ($bulanQuery as $m) {
                                $bulanOptions[sprintf('%02d', $m)] = $monthNames[(int)$m];
                            }
                            return empty($bulanOptions) ? [now()->format('m') => now()->translatedFormat('F')] : $bulanOptions;
                        })
                        ->default(now()->format('m'))
                        ->required(),

                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options(function () {
                            $tahunOptions = Absensi::selectRaw('YEAR(tanggal) as year')->distinct()->orderBy('year', 'desc')->pluck('year', 'year')->toArray();
                            return empty($tahunOptions) ? [now()->year => now()->year] : $tahunOptions;
                        })
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('admin.kelas.ekspor-semua', [
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                    ]);
                }),

            // RE-AKTIFKAN TOMBOL PROSES ALPA SECARA DINAMIS
            Action::make('proses_alpa')
                ->label('Proses Alpa Hari Ini')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                // TOMBOL HANYA AKAN MUNCUL JIKA METODE DISETEL KE "MANUAL" OLEH ADMIN
                ->visible(fn () => \App\Models\WaSetting::first()?->tipe_proses_alpa === 'Manual')
                ->action(function () {
                    // Panggil perintah artisan secara programmatif dengan opsi --manual murni!
                    Artisan::call('app:process-daily-alpa', ['--manual' => true]);

                    \Filament\Notifications\Notification::make()
                        ->title('Proses Alpa Selesai')
                        ->body("Proses pendeteksian alpa masal dan pengiriman rekap WhatsApp sukses dijalankan!")
                        ->success()
                        ->send();
                }),
        ];
    }
}
