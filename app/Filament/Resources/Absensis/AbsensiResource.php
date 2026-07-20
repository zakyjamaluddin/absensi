<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\ManageAbsensis;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static string | UnitEnum | null $navigationGroup = 'Data';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Rekap Absensi';
    protected static ?string $pluralModelLabel = 'Rekap Absensi';
    protected static ?string $modelLabel = 'Absensi';

    // 1. MEKANISME FORM COMPONENTS (Murni Array untuk digunakan bersama)
    public static function getFormComponents(): array
    {
        return [
            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal Absensi')
                ->required()
                ->default(now()),

            Forms\Components\Select::make('absensable_type')
                ->label('Tipe Absensi')
                ->options([
                    Siswa::class => 'Siswa',
                    Guru::class => 'Guru',
                ])
                ->required()
                ->reactive(),

            Forms\Components\Select::make('absensable_id')
                ->label('Nama Siswa / Guru')
                ->options(function (callable $get) {
                    $type = $get('absensable_type');
                    if ($type === Siswa::class) {
                        return Siswa::pluck('nama', 'id');
                    }
                    if ($type === Guru::class) {
                        return Guru::pluck('nama', 'id');
                    }
                    return [];
                })
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TimePicker::make('jam_masuk')
                ->label('Jam Masuk')
                ->seconds(false),

            Forms\Components\Select::make('status_masuk')
                ->label('Status Kehadiran')
                ->options([
                    'Tepat Waktu' => 'Tepat Waktu',
                    'Terlambat' => 'Terlambat',
                    'Sakit' => 'Sakit (S)',
                    'Izin' => 'Izin (I)',
                    'Alpa' => 'Alpa (A)',
                ])
                ->required(),

            Forms\Components\TimePicker::make('jam_pulang')
                ->label('Jam Pulang')
                ->seconds(false),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),



                Tables\Columns\TextColumn::make('absensable.nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->color(fn (Absensi $record): string => $record->absensable instanceof Siswa ? 'success' : 'info')
                    // perbaiki ini
                    ->description(function (Absensi $record) {
                        return $record->absensable instanceof Siswa
                            ? ($record->absensable->nis ?? null) // cek apakah null
                            : ($record->absensable->nip ?? null);
                    }),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status_masuk')
                    ->label('Status Masuk')
                    ->badge()
                    ->color(fn (string | null $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Sakit' => 'info',
                        'Izin' => 'gray',
                        'Alpa' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->alignCenter(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('absensable_type')
                    ->label('Filter Tipe')
                    ->options([
                        Siswa::class => 'Siswa',
                        Guru::class => 'Guru',
                    ]),

                Tables\Filters\SelectFilter::make('status_masuk')
                    ->label('Filter Kehadiran')
                    ->options([
                        'Tepat Waktu' => 'Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alpa' => 'Alpa',
                    ]),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Filter Kelas (Siswa)')
                    ->options(Kelas::pluck('nama_kelas', 'id'))
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            $query->whereHasMorph(
                                'absensable',
                                [Siswa::class],
                                fn ($q) => $q->where('kelas_id', $data['value'])
                            );
                        }
                    }),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($q) => $q->whereDate('tanggal', '>=', $data['dari_tanggal']))
                            ->when($data['sampai_tanggal'], fn ($q) => $q->whereDate('tanggal', '<=', $data['sampai_tanggal']));
                    })
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAbsensis::route('/'),
        ];
    }


    // Di dalam class AbsensiResource...
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user && $user->hasRole('Wali Kelas') && $user->userable instanceof \App\Models\Guru) {
        $kelasId = $user->userable->kelas?->id;

        // Batasi log absensi yang tampil hanya untuk siswa dari kelas ustadz tersebut
        return $query->whereHasMorph(
            'absensable',
            [\App\Models\Siswa::class],
            fn ($q) => $q->where('kelas_id', $kelasId)
        );
    }

    return $query;
}
}
