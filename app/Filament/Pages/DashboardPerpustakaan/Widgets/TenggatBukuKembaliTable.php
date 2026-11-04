<?php

namespace App\Filament\Pages\DashboardPerpustakaan\Widgets;

use App\Models\PeminjamanBuku;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TenggatBukuKembaliTable extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected static ?string $pollingInterval = '15s';

    protected static ?string $heading = 'Tenggat Buku yang Harus Segera Kembali';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PeminjamanBuku::query()
                    ->with(['siswa.kelas', 'buku'])
                    ->whereIn('status', ['Dipinjam', 'Terlambat'])
                    ->whereNull('tanggal_kembali')
                    ->orderBy('tanggal_jatuh_tempo', 'asc')
            )
            ->columns([
                TextColumn::make('buku.judul')
                    ->label('Buku yang Dipinjam')
                    ->weight('bold')
                    ->wrap()
                    ->description(fn (PeminjamanBuku $record): string => ($record->buku?->kode_buku ?? '-') . ($record->buku?->rak ? ' • Rak: ' . $record->buku->rak : ''))
                    ->searchable(),

                TextColumn::make('siswa.nama')
                    ->label('Peminjam')
                    ->weight('medium')
                    ->description(fn (PeminjamanBuku $record): string => ($record->siswa?->kelas?->nama_kelas ?? '-') . ' • NIS: ' . ($record->siswa?->nis ?? '-'))
                    ->searchable(),

                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->alignCenter(),

                TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Tanggal Tenggat (Jatuh Tempo)')
                    ->date('d M Y')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('sisa_waktu')
                    ->label('Status Tenggat')
                    ->state(function (PeminjamanBuku $record): string {
                        $dueDate = Carbon::parse($record->tanggal_jatuh_tempo)->startOfDay();
                        $today = Carbon::today();

                        if ($dueDate->isPast() && !$dueDate->isToday()) {
                            $diff = $dueDate->diffInDays($today);
                            return "Terlambat {$diff} Hari";
                        } elseif ($dueDate->isToday()) {
                            return 'Jatuh Tempo Hari Ini!';
                        } else {
                            $diff = $today->diffInDays($dueDate);
                            return "Sisa {$diff} Hari";
                        }
                    })
                    ->badge()
                    ->color(function (PeminjamanBuku $record): string {
                        $dueDate = Carbon::parse($record->tanggal_jatuh_tempo)->startOfDay();
                        if ($dueDate->isPast() && !$dueDate->isToday()) {
                            return 'danger';
                        } elseif ($dueDate->isToday()) {
                            return 'warning';
                        }
                        return 'info';
                    })
                    ->icon(function (PeminjamanBuku $record): string {
                        $dueDate = Carbon::parse($record->tanggal_jatuh_tempo)->startOfDay();
                        if ($dueDate->isPast() && !$dueDate->isToday()) {
                            return 'heroicon-m-exclamation-triangle';
                        } elseif ($dueDate->isToday()) {
                            return 'heroicon-m-clock';
                        }
                        return 'heroicon-m-calendar-days';
                    })
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status Pinjam')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Terlambat' => 'danger',
                        default => 'warning',
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('kategori_tenggat')
                    ->label('Filter Urgensi Tenggat')
                    ->options([
                        'terlambat' => 'Hanya yang Sudah Terlambat',
                        'hari_ini' => 'Jatuh Tempo Hari Ini',
                        'mendekati' => 'Jatuh Tempo 3 Hari ke Depan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        $today = Carbon::today()->toDateString();

                        if ($value === 'terlambat') {
                            return $query->where('tanggal_jatuh_tempo', '<', $today);
                        } elseif ($value === 'hari_ini') {
                            return $query->where('tanggal_jatuh_tempo', '=', $today);
                        } elseif ($value === 'mendekati') {
                            return $query->whereBetween('tanggal_jatuh_tempo', [
                                $today,
                                Carbon::today()->addDays(3)->toDateString(),
                            ]);
                        }

                        return $query;
                    }),
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
                    ->modalDescription(fn (PeminjamanBuku $record) => "Tandai buku \"{$record->buku?->judul}\" yang dipinjam oleh {$record->siswa?->nama} telah dikembalikan?")
                    ->action(function (PeminjamanBuku $record) {
                        $record->kembalikan();
                        Notification::make()
                            ->title('Buku Berhasil Dikembalikan')
                            ->body("Buku \"{$record->buku?->judul}\" telah ditandai dikembalikan.")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Semua Buku Terpantau Aman')
            ->emptyStateDescription('Tidak ada buku yang sedang menunggu pengembalian atau melewati batas tenggat saat ini.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
