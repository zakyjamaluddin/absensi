<?php

namespace App\Filament\Resources\Kelas\Pages;

use App\Filament\Resources\Kelas\KelasResource;
use App\Models\Absensi;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewKelas extends ViewRecord
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        // 1. QUERY DINAMIS TAHUN: Mengambil tahun unik yang ada di log absensi database
        $tahunOptions = Absensi::selectRaw('YEAR(tanggal) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray();

        // Pengaman: Jika database baru kosong (belum ada scan), tampilkan tahun ini
        if (empty($tahunOptions)) {
            $tahunOptions = [now()->year => now()->year];
        }

        // 2. QUERY DINAMIS BULAN: Mengambil angka bulan unik yang ada di log absensi database
        $bulanQuery = Absensi::selectRaw('MONTH(tanggal) as month')
            ->distinct()
            ->orderBy('month', 'asc')
            ->pluck('month')
            ->toArray();

        // Peta nama bulan Indonesia
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Format angka bulan menjadi ber-awalan nol (01, 02, dst.) agar cocok dengan rute
        $bulanOptions = [];
        foreach ($bulanQuery as $m) {
            $bulanOptions[sprintf('%02d', $m)] = $monthNames[(int)$m];
        }

        // Pengaman: Jika database baru kosong, tampilkan bulan ini
        if (empty($bulanOptions)) {
            $bulanOptions = [now()->format('m') => now()->translatedFormat('F')];
        }

        return [
            Action::make('ekspor_absensi')
                ->label('Ekspor Absensi Bulanan')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options($bulanOptions) // <--- Dinamis mengambil isi database!
                        ->default(now()->format('m'))
                        ->required(),

                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options($tahunOptions) // <--- Dinamis mengambil isi database!
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $kelasId = $this->getRecord()->id;

                    return redirect()->route('admin.kelas.ekspor', [
                        'kelas' => $kelasId,
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                    ]);
                }),
        ];
    }
}
