<?php

namespace App\Filament\Resources\JadwalMengajars;

use App\Filament\Resources\JadwalMengajars\Pages\ManageJadwalMengajars;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class JadwalMengajarResource extends Resource
{
    protected static ?string $model = JadwalMengajar::class;

    protected static string | UnitEnum | null $navigationGroup = 'Presensi & Kehadiran';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Jadwal Mengajar';
    protected static ?string $pluralModelLabel = 'Jadwal Mengajar';
    protected static ?string $modelLabel = 'Jadwal Mengajar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Jadwal Mengajar Guru')
                    ->columnSpanFull()
                    ->description('Ubah rincian hari dan jam pelajaran untuk guru.')
                    ->schema([
                        Select::make('guru_id')
                            ->label('Guru / Pengajar')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih Guru')
                            ->columnSpanFull(),

                        Select::make('hari')
                            ->label('Hari Mengajar')
                            ->options(JadwalMengajar::$hariMap)
                            ->required()
                            ->placeholder('Pilih Hari'),

                        Select::make('mulai_jam_ke')
                            ->label('Mulai Dari (Jam Ke)')
                            ->options(fn () => JamPelajaran::getOptions())
                            ->required()
                            ->placeholder('Pilih Jam Mulai'),

                        Select::make('sampai_jam_ke')
                            ->label('Sampai (Jam Ke)')
                            ->options(fn () => JamPelajaran::getOptions())
                            ->required()
                            ->placeholder('Pilih Jam Selesai'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guru.nama')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->description(fn (JadwalMengajar $record) => $record->guru?->nip ? 'NIP: ' . $record->guru->nip : null),

                TextColumn::make('hari_indo')
                    ->label('Hari')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Senin' => 'primary',
                        'Selasa' => 'info',
                        'Rabu' => 'warning',
                        'Kamis' => 'success',
                        'Jumat' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('hari', $direction)),

                TextColumn::make('jam_ke')
                    ->label('Jam Pelajaran')
                    ->formatStateUsing(fn ($state) => $state ? "Jam ke-{$state}" : '-')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('jam_mulai')
                    ->label('Waktu Mengajar')
                    ->formatStateUsing(fn (JadwalMengajar $record) => Carbon::parse($record->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($record->jam_selesai)->format('H:i') . ' WIB')
                    ->sortable(),

                TextColumn::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->wrap()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('guru_id')
                    ->label('Filter Guru')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('hari')
                    ->label('Filter Hari')
                    ->options(JadwalMengajar::$hariMap),

                SelectFilter::make('kelas_id')
                    ->label('Filter Kelas')
                    ->relationship('kelas', 'nama_kelas'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('xl')
                        ->mutateRecordDataUsing(function (array $data): array {
                            if (empty($data['mulai_jam_ke']) && !empty($data['jam_ke'])) {
                                $parts = explode('-', (string)$data['jam_ke']);
                                $data['mulai_jam_ke'] = (int) trim($parts[0]);
                                $data['sampai_jam_ke'] = isset($parts[1]) ? (int) trim($parts[1]) : (int) trim($parts[0]);
                            }
                            return $data;
                        }),
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
            'index' => ManageJadwalMengajars::route('/'),
        ];
    }
}
