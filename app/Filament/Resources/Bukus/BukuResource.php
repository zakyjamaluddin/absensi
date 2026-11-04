<?php

namespace App\Filament\Resources\Bukus;

use App\Filament\Resources\Bukus\Pages\ManageBukus;
use App\Models\Buku;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use UnitEnum;

class BukuResource extends Resource
{
    protected static ?string $model = Buku::class;

    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Katalog Buku';
    protected static ?string $pluralModelLabel = 'Katalog Buku';
    protected static ?string $modelLabel = 'Buku';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Buku')
                    ->columnSpanFull()
                    ->description('Masukkan data koleksi buku. Jika buku tidak memiliki kode/barcode, kosongkan input kode dan sistem akan otomatis membuatnya.')
                    ->schema([
                        TextInput::make('kode_buku')
                            ->label('Kode / Barcode Buku')
                            ->placeholder('Kosongkan untuk generate otomatis')
                            ->helperText('Bisa discan menggunakan scanner barcode fisik atau dikosongkan untuk kode unik otomatis.')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('judul')
                            ->label('Judul Buku')
                            ->required()
                            ->placeholder('Masukkan judul lengkap buku')
                            ->maxLength(255),

                        TextInput::make('penulis')
                            ->label('Penulis / Pengarang')
                            ->placeholder('Nama penulis atau pengarang')
                            ->maxLength(255),

                        TextInput::make('penerbit')
                            ->label('Penerbit')
                            ->placeholder('Nama penerbit buku')
                            ->maxLength(255),

                        TextInput::make('tahun_terbit')
                            ->label('Tahun Terbit')
                            ->placeholder('Contoh: 2024')
                            ->maxLength(10),

                        Select::make('kategori')
                            ->label('Kategori Buku')
                            ->options([
                                'Pelajaran' => 'Buku Pelajaran',
                                'Agama & Kitab' => 'Agama & Kitab',
                                'Fiksi & Sastra' => 'Fiksi & Sastra',
                                'Sains & Teknologi' => 'Sains & Teknologi',
                                'Sejarah & Biografi' => 'Sejarah & Biografi',
                                'Ensiklopedia & Kamus' => 'Ensiklopedia & Kamus',
                                'Keterampilan & Hobi' => 'Keterampilan & Hobi',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->searchable()
                            ->placeholder('Pilih Kategori'),

                        TextInput::make('rak')
                            ->label('Lokasi Rak / Lemari')
                            ->placeholder('Contoh: Rak B-03')
                            ->maxLength(50),

                        TextInput::make('stok')
                            ->label('Total Stok')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->helperText('Jumlah eksemplar buku yang dimiliki perpustakaan.'),

                        Textarea::make('deskripsi')
                            ->label('Ringkasan / Sinopsis')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Deskripsi singkat mengenai isi buku...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_buku')
                    ->label('Kode Buku')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode buku disalin!')
                    ->weight('bold')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('judul')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(function (Buku $record) {
                        $parts = [];
                        if ($record->penulis) $parts[] = 'Penulis: ' . $record->penulis;
                        if ($record->penerbit) $parts[] = 'Penerbit: ' . $record->penerbit;
                        if ($record->rak) $parts[] = '📍 Rak: ' . $record->rak;
                        return implode(' • ', $parts);
                    }),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('tersedia')
                    ->label('Tersedia / Stok')
                    ->formatStateUsing(fn (Buku $record) => "{$record->tersedia} / {$record->stok} eks")
                    ->badge()
                    ->color(fn (Buku $record) => $record->tersedia > 0 ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'Pelajaran' => 'Buku Pelajaran',
                        'Agama & Kitab' => 'Agama & Kitab',
                        'Fiksi & Sastra' => 'Fiksi & Sastra',
                        'Sains & Teknologi' => 'Sains & Teknologi',
                        'Sejarah & Biografi' => 'Sejarah & Biografi',
                        'Ensiklopedia & Kamus' => 'Ensiklopedia & Kamus',
                        'Keterampilan & Hobi' => 'Keterampilan & Hobi',
                        'Lainnya' => 'Lainnya',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->modalWidth('2xl'),

                    // 1. Aksi Unduh QR Code SVG
                    Action::make('unduh_qr')
                        ->label('Unduh QR Code')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Buku $record) {
                            $judulClean = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ' '], '_', $record->judul);
                            return response()->streamDownload(function () use ($record) {
                                echo QrCode::size(300)
                                    ->margin(1)
                                    ->generate($record->kode_buku);
                            }, "Buku_{$record->kode_buku}_{$judulClean}.svg", [
                                'Content-Type' => 'image/svg+xml',
                            ]);
                        }),

                    // 2. Aksi Cetak Label Barcode / QR Code
                    Action::make('cetak_label')
                        ->label('Cetak Label Buku')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->modalHeading(fn (Buku $record) => 'Cetak Label: ' . $record->judul)
                        ->modalContent(fn (Buku $record) => view('filament.pages.cetak-label-buku', ['buku' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),

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
            'index' => ManageBukus::route('/'),
        ];
    }
}
