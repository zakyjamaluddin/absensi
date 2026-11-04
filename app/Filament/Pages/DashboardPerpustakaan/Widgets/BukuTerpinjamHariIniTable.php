<?php

namespace App\Filament\Pages\DashboardPerpustakaan\Widgets;

use App\Models\PeminjamanBuku;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BukuTerpinjamHariIniTable extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected static ?string $pollingInterval = '15s';

    protected static ?string $heading = 'Buku yang Terpinjam Hari Ini';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PeminjamanBuku::query()
                    ->with(['siswa.kelas', 'buku'])
                    ->where('tanggal_pinjam', Carbon::today()->toDateString())
                    ->latest('id')
            )
            ->columns([
                TextColumn::make('kode_peminjaman')
                    ->label('Kode Pinjam')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode pinjam berhasil disalin'),

                TextColumn::make('siswa.nama')
                    ->label('Peminjam (Siswa)')
                    ->weight('bold')
                    ->description(fn (PeminjamanBuku $record): string => ($record->siswa?->kelas?->nama_kelas ?? '-') . ' • NIS: ' . ($record->siswa?->nis ?? '-'))
                    ->searchable(),

                TextColumn::make('buku.judul')
                    ->label('Buku yang Dipinjam')
                    ->wrap()
                    ->description(fn (PeminjamanBuku $record): string => ($record->buku?->kode_buku ?? '-') . ($record->buku?->kategori ? ' • ' . $record->buku->kategori : ''))
                    ->searchable(),

                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->alignCenter(),

                TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Tanggal Tenggat')
                    ->date('d M Y')
                    ->weight('bold')
                    ->color(fn (PeminjamanBuku $record): string => match (true) {
                        $record->status === 'Dikembalikan' => 'gray',
                        Carbon::parse($record->tanggal_jatuh_tempo)->isPast() && !Carbon::parse($record->tanggal_jatuh_tempo)->isToday() => 'danger',
                        Carbon::parse($record->tanggal_jatuh_tempo)->isToday() => 'warning',
                        default => 'primary',
                    })
                    ->description(fn (PeminjamanBuku $record): string => match (true) {
                        $record->status === 'Dikembalikan' => 'Sudah kembali',
                        Carbon::parse($record->tanggal_jatuh_tempo)->isPast() && !Carbon::parse($record->tanggal_jatuh_tempo)->isToday() => 'Terlambat ' . Carbon::parse($record->tanggal_jatuh_tempo)->diffInDays(now()) . ' hari',
                        Carbon::parse($record->tanggal_jatuh_tempo)->isToday() => 'Jatuh tempo hari ini',
                        default => Carbon::parse($record->tanggal_jatuh_tempo)->diffInDays(now()) . ' hari lagi',
                    })
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Dikembalikan' => 'success',
                        'Terlambat' => 'danger',
                        default => 'warning',
                    })
                    ->alignCenter(),
            ])
            ->recordActions([
                Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('success')
                    ->button()
                    ->size('xs')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian Buku')
                    ->modalDescription(fn (PeminjamanBuku $record) => "Tandai buku \"{$record->buku?->judul}\" yang dipinjam oleh {$record->siswa?->nama} telah dikembalikan hari ini?")
                    ->action(function (PeminjamanBuku $record) {
                        $record->kembalikan();
                        Notification::make()
                            ->title('Buku Berhasil Dikembalikan')
                            ->body("Buku \"{$record->buku?->judul}\" telah ditandai dikembalikan.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PeminjamanBuku $record) => $record->status !== 'Dikembalikan'),
            ])
            ->emptyStateHeading('Belum Ada Buku Dipinjam Hari Ini')
            ->emptyStateDescription('Belum ada transaksi peminjaman buku yang dicatat pada hari ini.')
            ->emptyStateIcon('heroicon-o-book-open')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
