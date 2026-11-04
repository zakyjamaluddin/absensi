<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\ManageAbsensis;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use UnitEnum;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static string | UnitEnum | null $navigationGroup = 'Presensi & Kehadiran';
    protected static ?int $navigationSort = 1;


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
                    ->alignCenter()
                    ->description(function (Absensi $record) {
                        if (!empty($record->jam_pulang)) {
                            $jam = Carbon::parse($record->jam_pulang)->format('H:i');
                            return new HtmlString("<span style='color: #dc2626; font-weight: 700;'>Jam Pulang: {$jam}</span>");
                        }
                        return new HtmlString("<span style='color: #94a3b8; font-size: 0.75rem;'>Jam Pulang: --:--</span>");
                    }),

                Tables\Columns\TextColumn::make('status_masuk')
                    ->label('Status Masuk')
                    ->badge()
                    ->color(fn (string | null $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Sakit' => 'info',
                        'Izin' => 'gray',
                        'Alpa' => 'danger',
                        'Belum Absen' => 'slate',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_kehadiran')
                    ->label('Status Absensi')
                    ->options([
                        'sudah_absen' => 'Sudah Absen',
                        'belum_absen' => 'Belum Absen',
                        'Tepat Waktu' => 'Hadir Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Ijin',
                        'Alpa' => 'Alpa',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if (! $val) {
                            return $query;
                        }

                        if ($val === 'sudah_absen') {
                            return $query->where(function ($q) {
                                $q->whereNotNull('jam_masuk')
                                  ->orWhereIn('status_masuk', ['Tepat Waktu', 'Terlambat']);
                            });
                        }

                        if ($val === 'belum_absen') {
                            return $query->where(function ($q) {
                                $q->where('status_masuk', 'Belum Absen')
                                  ->orWhere(function ($sub) {
                                      $sub->whereNull('jam_masuk')
                                          ->whereNull('status_masuk');
                                  });
                            });
                        }

                        return $query->where('status_masuk', $val);
                    }),

                Tables\Filters\Filter::make('tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->toDateString()),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->toDateString()),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['sampai_tanggal'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators['dari_tanggal'] = 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->translatedFormat('d M Y');
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->translatedFormat('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('absensable_type')
                    ->label('Filter Tipe')
                    ->options([
                        Siswa::class => 'Siswa',
                        Guru::class => 'Guru',
                    ]),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Filter Kelas (Siswa)')
                    ->options(Kelas::pluck('nama_kelas', 'id'))
                    ->query(function ($query, array $data) {
                        if ($data['value'] ?? null) {
                            $query->whereHasMorph(
                                'absensable',
                                [Siswa::class],
                                fn ($q) => $q->where('kelas_id', $data['value'])
                            );
                        }
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('tandai_hadir')
                        ->label('Tandai Hadir')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('status_masuk')
                                ->label('Status Kehadiran')
                                ->options([
                                    'Tepat Waktu' => 'Hadir Tepat Waktu',
                                    'Terlambat' => 'Terlambat',
                                ])
                                ->default('Tepat Waktu')
                                ->required(),
                            Forms\Components\TimePicker::make('jam_masuk')
                                ->label('Jam Masuk')
                                ->default(now()->format('H:i'))
                                ->seconds(false)
                                ->required(),
                        ])
                        ->action(function (Absensi $record, array $data) {
                            $record->update([
                                'status_masuk' => $data['status_masuk'],
                                'jam_masuk' => $data['jam_masuk'],
                            ]);

                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body("{$record->absensable?->nama} berhasil ditandai {$data['status_masuk']}.")
                                ->success()
                                ->send();
                        }),

                    Action::make('tandai_sakit')
                        ->label('Tandai Sakit')
                        ->icon('heroicon-o-heart')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Sakit')
                        ->modalDescription('Ubah status presensi orang ini menjadi Sakit (S)? Jam masuk akan dikosongkan.')
                        ->action(function (Absensi $record) {
                            $record->update([
                                'status_masuk' => 'Sakit',
                                'jam_masuk' => null,
                            ]);

                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body("{$record->absensable?->nama} berhasil ditandai Sakit.")
                                ->info()
                                ->send();
                        }),

                    Action::make('tandai_ijin')
                        ->label('Tandai Izin')
                        ->icon('heroicon-o-envelope-open')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Izin')
                        ->modalDescription('Ubah status presensi orang ini menjadi Izin (I)? Jam masuk akan dikosongkan.')
                        ->action(function (Absensi $record) {
                            $record->update([
                                'status_masuk' => 'Izin',
                                'jam_masuk' => null,
                            ]);

                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body("{$record->absensable?->nama} berhasil ditandai Izin.")
                                ->warning()
                                ->send();
                        }),

                    Action::make('tandai_alpa')
                        ->label('Tandai Alpa')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Alpa')
                        ->modalDescription('Ubah status presensi orang ini menjadi Alpa (A)? Jam masuk akan dikosongkan.')
                        ->action(function (Absensi $record) {
                            $record->update([
                                'status_masuk' => 'Alpa',
                                'jam_masuk' => null,
                            ]);

                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body("{$record->absensable?->nama} berhasil ditandai Alpa.")
                                ->danger()
                                ->send();
                        }),

                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ->visible(fn () => !auth()->user()?->hasAnyRole(['guru', 'wali_kelas', 'Wali Kelas', 'siswa'])),
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

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('super_admin') || $user->hasRole('admin_absensi')) {
            return $query;
        }

        // 1. Siswa: hanya tampil data milik pribadi nya
        if ($user->hasRole('siswa') || ($user->userable instanceof Siswa && ! $user->hasAnyRole(['guru', 'wali_kelas', 'Wali Kelas', 'admin_absensi', 'super_admin']))) {
            return $query->where('absensable_type', Siswa::class)
                ->where('absensable_id', $user->userable_id);
        }

        // 2. Wali Kelas: tampil data milik pribadi nya dan data dari siswa kelasnya
        if ($user->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            $guruId = $user->userable_id;
            $kelasId = $user->userable instanceof Guru ? $user->userable->kelas?->id : null;

            return $query->where(function ($q) use ($guruId, $kelasId) {
                // Data pribadi guru
                $q->where(function ($sub) use ($guruId) {
                    $sub->where('absensable_type', Guru::class)
                        ->where('absensable_id', $guruId);
                });

                // Data siswa dalam kelas yang diampu
                if ($kelasId) {
                    $q->orWhere(function ($sub) use ($kelasId) {
                        $sub->whereHasMorph(
                            'absensable',
                            [Siswa::class],
                            fn ($sq) => $sq->where('kelas_id', $kelasId)
                        );
                    });
                }
            });
        }

        // 3. Guru: hanya tampil data milik pribadi nya
        if ($user->hasRole('guru') || $user->userable instanceof Guru) {
            return $query->where('absensable_type', Guru::class)
                ->where('absensable_id', $user->userable_id);
        }

        return $query;
    }
}
