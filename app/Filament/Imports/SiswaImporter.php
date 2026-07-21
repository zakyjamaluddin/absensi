<?php

namespace App\Filament\Imports;

use App\Models\Siswa;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use App\Models\Kelas;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SiswaImporter extends Importer
{
    protected static ?string $model = Siswa::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nis')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            // 3. Kolom Kelas (Membaca Teks Nama Kelas dari Excel/CSV)
            ImportColumn::make('kelas')
                ->label('Nama Kelas') // Nama header kolom di file CSV Anda harus berupa "kelas" atau "Nama Kelas"
                ->requiredMapping()
                ->fillRecordUsing(function (Siswa $record, $state) {
                    // Bersihkan spasi di awal/akhir nama kelas dari Excel
                    $namaKelas = trim($state);

                    // Cari rekor kelas berdasarkan nama kelas di database
                    $kelas = Kelas::where('nama_kelas', $namaKelas)->first();

                    // Jika nama kelas tidak ditemukan di database, batalkan baris ini dan beri error
                    if (! $kelas) {
                        throw new \Exception("Kelas '{$namaKelas}' tidak terdaftar di database.");
                    }

                    // Jika cocok, masukkan ID kelas ke dalam rekor Siswa
                    $record->kelas_id = $kelas->id;
                }),
        ];
    }

    public function resolveRecord(): Siswa
    {
        return Siswa::firstOrNew([
            'nis' => $this->data['nis'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your siswa import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
