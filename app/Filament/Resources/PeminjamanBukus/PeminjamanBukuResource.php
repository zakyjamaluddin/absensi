<?php

namespace App\Filament\Resources\PeminjamanBukus;

use App\Filament\Resources\PeminjamanBukus\Pages\ManagePeminjamanBukus;
use App\Models\Buku;
use App\Models\PeminjamanBuku;
use App\Models\PerpustakaanSetting;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PeminjamanBukuResource extends Resource
{
    protected static ?string $model = PeminjamanBuku::class;

    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Peminjaman Buku';
    protected static ?string $pluralModelLabel = 'Peminjaman Buku';
    protected static ?string $modelLabel = 'Peminjaman Buku';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Peminjaman')
                    ->schema([
                        Select::make('siswa_id')
                            ->label('Siswa Peminjam')
                            ->relationship('siswa', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Cari nama atau NIS siswa'),

                        Select::make('buku_id')
                            ->label('Buku yang Dipinjam')
                            ->relationship('buku', 'judul')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Cari judul atau kode buku')
                            ->helperText(function () {
                                return 'Hanya buku dengan stok tersedia yang dapat dipinjam.';
                            }),

                        DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->default(now()->toDateString())
                            ->required(),

                        DatePicker::make('tanggal_jatuh_tempo')
                            ->label('Batas Maksimal Pengembalian')
                            ->default(function () {
                                $setting = PerpustakaanSetting::getSetting();
                                return now()->addDays($setting->lama_pinjam_default ?? 7)->toDateString();
                            })
                            ->helperText('Batas maksimal pengembalian (default dapat disesuaikan di Pengaturan Perpustakaan).')
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Dipinjam' => 'Sedang Dipinjam',
                                'Dikembalikan' => 'Sudah Dikembalikan',
                                'Terlambat' => 'Terlambat',
                            ])
                            ->default('Dipinjam')
                            ->required(),

                        DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Dikembalikan')
                            ->nullable(),

                        Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Kondisi buku saat dipinjam, dll.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_peminjaman')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PeminjamanBuku $record) => ($record->siswa?->kelas?->nama_kelas ?? '-') . ' • NIS: ' . ($record->siswa?->nis ?? '-')),

                TextColumn::make('buku.judul')
                    ->label('Buku Dipinjam')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (PeminjamanBuku $record) => 'Kode: ' . ($record->buku?->kode_buku ?? '-')),

                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->description(function (PeminjamanBuku $record) {
                        if ($record->status === 'Dipinjam') {
                            $diff = (int) now()->startOfDay()->diffInDays(Carbon::parse($record->tanggal_jatuh_tempo), false);
                            if ($diff < 0) return abs($diff) . ' hari terlambat!';
                            if ($diff === 0) return 'Hari ini batas akhir!';
                            return $diff . ' hari lagi';
                        }
                        return null;
                    })
                    ->color(function (PeminjamanBuku $record) {
                        if ($record->status === 'Dipinjam') {
                            $diff = (int) now()->startOfDay()->diffInDays(Carbon::parse($record->tanggal_jatuh_tempo), false);
                            if ($diff < 0) return 'danger';
                            if ($diff === 0) return 'warning';
                        }
                        return null;
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Dipinjam' => 'warning',
                        'Dikembalikan' => 'success',
                        'Terlambat' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('tanggal_kembali')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Dipinjam' => 'Sedang Dipinjam',
                        'Dikembalikan' => 'Sudah Dikembalikan',
                        'Terlambat' => 'Terlambat',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    // Tombol cepat kembalikan buku
                    Action::make('kembalikan')
                        ->label('Kembalikan Buku')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Pengembalian Buku')
                        ->modalDescription(fn (PeminjamanBuku $record) => "Tandai bahwa buku '{$record->buku?->judul}' telah dikembalikan oleh {$record->siswa?->nama} hari ini?")
                        ->visible(fn (PeminjamanBuku $record) => $record->status === 'Dipinjam')
                        ->action(function (PeminjamanBuku $record) {
                            $record->kembalikan();
                            Notification::make()
                                ->title('Buku Berhasil Dikembalikan')
                                ->body("Buku '{$record->buku?->judul}' telah ditandai kembali dan stok buku otomatis dipulihkan.")
                                ->success()
                                ->send();
                        }),

                    EditAction::make()->modalWidth('xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePeminjamanBukus::route('/'),
        ];
    }
}
