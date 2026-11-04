<?php

namespace App\Filament\Resources\JadwalGuruPikets;

use App\Filament\Resources\JadwalGuruPikets\Pages\ManageJadwalGuruPikets;
use App\Models\JadwalGuruPiket;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class JadwalGuruPiketResource extends Resource
{
    protected static ?string $model = JadwalGuruPiket::class;

    protected static string | UnitEnum | null $navigationGroup = 'Presensi & Kehadiran';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Jadwal Guru Piket';
    protected static ?string $pluralModelLabel = 'Jadwal Guru Piket';
    protected static ?string $modelLabel = 'Guru Piket';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Tugas Guru Piket')
                    ->description('Tentukan hari dan guru yang bertugas sebagai piket.')
                    ->schema([
                        Select::make('guru_id')
                            ->label('Guru / Ustadz')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih Guru yang bertugas piket'),

                        Select::make('hari')
                            ->label('Hari Piket')
                            ->options(JadwalGuruPiket::$hariList)
                            ->required()
                            ->placeholder('Pilih Hari'),

                        TextInput::make('keterangan')
                            ->label('Keterangan / Pos Tugas')
                            ->placeholder('Contoh: Piket Gerbang Utama & KBM')
                            ->maxLength(255),

                        Toggle::make('is_aktif')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Hanya jadwal aktif yang mendapatkan wewenang approval dan notifikasi piket.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Senin' => 'info',
                        'Selasa' => 'success',
                        'Rabu' => 'warning',
                        'Kamis' => 'primary',
                        'Jumat' => 'danger',
                        'Sabtu' => 'slate',
                        'Ahad' => 'gray',
                        default => 'slate',
                    })
                    ->sortable(),

                TextColumn::make('guru.nama')
                    ->label('Nama Guru Piket')
                    ->searchable()
                    ->sortable()
                    ->description(fn (JadwalGuruPiket $record): string => $record->guru ? "NIP: {$record->guru->nip}" : '-'),

                TextColumn::make('keterangan')
                    ->label('Keterangan / Pos')
                    ->placeholder('Tanpa keterangan pos')
                    ->searchable()
                    ->visibleFrom('md')
                    ->toggleable(),

                IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable()
                    ->visibleFrom('md')
                    ->toggleable(),
            ])
            ->defaultSort('hari', 'asc')
            ->filters([
                SelectFilter::make('hari')
                    ->label('Filter Hari')
                    ->options(JadwalGuruPiket::$hariList),

                SelectFilter::make('is_aktif')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Hanya Aktif',
                        '0' => 'Non-Aktif',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageJadwalGuruPikets::route('/'),
        ];
    }
}
