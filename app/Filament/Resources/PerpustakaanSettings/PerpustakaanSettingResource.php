<?php

namespace App\Filament\Resources\PerpustakaanSettings;

use App\Filament\Resources\PerpustakaanSettings\Pages\ManagePerpustakaanSettings;
use App\Models\PerpustakaanSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PerpustakaanSettingResource extends Resource
{
    protected static ?string $model = PerpustakaanSetting::class;

    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Pengaturan Perpustakaan';
    protected static ?string $pluralModelLabel = 'Pengaturan Perpustakaan';
    protected static ?string $modelLabel = 'Pengaturan Perpustakaan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Batas Waktu & Scoring Literasi')
                    ->description('Sesuaikan batas hari peminjaman dan bobot skor minat literasi siswa.')
                    ->schema([
                        TextInput::make('lama_pinjam_default')
                            ->label('Batas Maksimal Pengembalian Default (Hari)')
                            ->numeric()
                            ->default(7)
                            ->minValue(1)
                            ->required()
                            ->helperText('Berapa hari batas waktu siswa meminjam buku secara default (bisa diatur custom saat meminjam).'),

                        TextInput::make('poin_kunjungan')
                            ->label('Poin Skor per Kunjungan')
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->required()
                            ->helperText('Poin literasi yang didapatkan siswa setiap kali berkunjung/membaca di perpustakaan.'),

                        TextInput::make('poin_peminjaman')
                            ->label('Poin Skor per Peminjaman Buku')
                            ->numeric()
                            ->default(15)
                            ->minValue(1)
                            ->required()
                            ->helperText('Poin literasi yang didapatkan siswa setiap kali meminjam koleksi buku.'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lama_pinjam_default')
                    ->label('Batas Pinjam Default')
                    ->formatStateUsing(fn ($state) => "{$state} Hari")
                    ->badge()
                    ->color('primary'),

                TextColumn::make('poin_kunjungan')
                    ->label('Poin Kunjungan')
                    ->formatStateUsing(fn ($state) => "+{$state} Poin")
                    ->badge()
                    ->color('info'),

                TextColumn::make('poin_peminjaman')
                    ->label('Poin Pinjam Buku')
                    ->formatStateUsing(fn ($state) => "+{$state} Poin")
                    ->badge()
                    ->color('success'),
            ])
            ->recordActions([
                EditAction::make()->modalWidth('xl'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePerpustakaanSettings::route('/'),
        ];
    }
}
