<?php

namespace App\Filament\Resources\KunjunganPerpustakaans;

use App\Filament\Resources\KunjunganPerpustakaans\Pages\ManageKunjunganPerpustakaans;
use App\Models\KunjunganPerpustakaan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class KunjunganPerpustakaanResource extends Resource
{
    protected static ?string $model = KunjunganPerpustakaan::class;

    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedIdentification;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Log Kunjungan';
    protected static ?string $pluralModelLabel = 'Log Kunjungan';
    protected static ?string $modelLabel = 'Kunjungan Perpustakaan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Catat Kehadiran Perpustakaan')
                    ->schema([
                        Select::make('siswa_id')
                            ->label('Siswa')
                            ->relationship('siswa', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih siswa yang berkunjung'),

                        DatePicker::make('tanggal')
                            ->label('Tanggal Kunjungan')
                            ->default(now()->toDateString())
                            ->required(),

                        TimePicker::make('waktu_masuk')
                            ->label('Jam Masuk')
                            ->default(now()->toTimeString())
                            ->seconds(false)
                            ->required(),

                        TextInput::make('keperluan')
                            ->label('Keperluan / Aktivitas')
                            ->default('Membaca / Belajar')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('waktu_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (KunjunganPerpustakaan $record) => ($record->siswa?->kelas?->nama_kelas ?? '-') . ' • NIS: ' . ($record->siswa?->nis ?? '-')),

                TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->badge()
                    ->color('info')
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('tanggal_hari_ini')
                    ->label('Hanya Hari Ini')
                    ->query(fn (Builder $query) => $query->where('tanggal', now()->toDateString())),

                SelectFilter::make('kelas')
                    ->label('Filter Kelas')
                    ->relationship('siswa.kelas', 'nama_kelas'),
            ])
            ->recordActions([
                EditAction::make()->modalWidth('md'),
                DeleteAction::make(),
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
            'index' => ManageKunjunganPerpustakaans::route('/'),
        ];
    }
}
