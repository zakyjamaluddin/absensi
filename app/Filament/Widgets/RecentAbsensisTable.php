<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Siswa;

class RecentAbsensisTable extends BaseWidget
{
    // Urutan ke-3 (Paling Bawah)
    protected static ?int $sort = 3;

    // Polling refresh otomatis setiap 15 detik agar tabel update terus
    protected static ?string $pollingInterval = '15s';

    // Paksa agar tabel melebar penuh melintang di layar
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        $user = auth()->user();
        $kelas = ($user && $user->hasAnyRole(['wali_kelas', 'Wali Kelas']) && $user->userable instanceof Guru)
            ? $user->userable->kelas
            : null;

        return $kelas
            ? "5 Aktivitas Presensi Terakhir Siswa: {$kelas->nama_kelas}"
            : '5 Aktivitas Presensi Terakhir Hari Ini';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $kelas = ($user && $user->hasAnyRole(['wali_kelas', 'Wali Kelas']) && $user->userable instanceof Guru)
            ? $user->userable->kelas
            : null;
        $siswaIds = $kelas ? $kelas->siswas()->pluck('id') : null;

        $query = Absensi::where('tanggal', today())
            ->where('absensable_type', Siswa::class)
            ->whereNotNull('jam_masuk');

        if ($siswaIds) {
            $query->whereIn('absensable_id', $siswaIds);
        }

        return $table
            ->query(
                $query->latest('updated_at')->limit(5)
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
