<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Absensi;
use App\Models\Siswa;

class RecentAbsensisTable extends BaseWidget
{
    // Urutan ke-3 (Paling Bawah)
    protected static ?int $sort = 3;

    // Polling refresh otomatis setiap 15 detik agar tabel update terus
    protected static ?string $pollingInterval = '15s';

    protected static ?string $heading = '5 Aktivitas Presensi Terakhir Hari Ini';

    // Paksa agar tabel melebar penuh melintang di layar
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Kueri: Ambil log absensi siswa hari ini yang sudah melakukan tap masuk
                Absensi::where('tanggal', today())
                    ->where('absensable_type', Siswa::class)
                    ->whereNotNull('jam_masuk')
                    ->latest('updated_at') // Urutkan dari tap paling baru
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('absensable.nama')
                    ->label('Nama Siswa')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('absensable.kelas.nama_kelas')
                    ->label('Kelas'),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Waktu Tap')
                    ->time('H:i:s')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status_masuk')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        default => 'gray',
                    })
                    ->alignCenter(),
            ])
            ->paginated(false); // Sembunyikan pagination karena hanya menampilkan 5 baris terakhir
    }
}
