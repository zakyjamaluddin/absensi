<?php

namespace App\Filament\Resources\AjukanIzins;

use App\Filament\Resources\AjukanIzins\Pages\ManageAjukanIzins;
use App\Models\Guru;
use App\Models\PengajuanIzin;
use App\Models\Siswa;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AjukanIzinResource extends Resource
{
    protected static ?string $model = PengajuanIzin::class;

    protected static string | UnitEnum | null $navigationGroup = 'Layanan Mandiri & Izin';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentPlus;
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Ajukan Izin';
    protected static ?string $pluralModelLabel = 'Pengajuan Izin Saya';
    protected static ?string $modelLabel = 'Izin';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Staf perpustakaan murni tidak mengakses perizinan absensi
        if ($user->hasAnyRole(['admin_perpustakaan', 'staff_perpustakaan']) && !$user->hasAnyRole(['guru', 'wali_kelas', 'siswa'])) {
            return false;
        }

        // Khusus Guru, Wali Kelas, dan Siswa untuk permohonan mandiri
        return $user->hasAnyRole(['guru', 'wali_kelas', 'siswa']) || ($user->userable_id !== null);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $isUserWithProfile = $user && $user->userable;

        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Izin / Sakit Mandiri')
                    ->description('Silakan isi formulir pengajuan izin dengan data yang sebenarnya.')
                    ->schema([
                        Hidden::make('izinable_type')
                            ->default(fn () => $isUserWithProfile ? get_class($user->userable) : null),

                        Hidden::make('izinable_id')
                            ->default(fn () => $isUserWithProfile ? $user->userable_id : null),

                        Select::make('tipe_izin')
                            ->label('Jenis Pengajuan')
                            ->options([
                                'Izin'  => 'Izin (Keperluan Tertentu)',
                                'Sakit' => 'Sakit (Dilengkapi Surat Dokter / Keterangan)',
                            ])
                            ->default('Izin')
                            ->required(),

                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Izin')
                            ->default(now()->toDateString())
                            ->required(),

                        DatePicker::make('tanggal_selesai')
                            ->label('Sampai Tanggal')
                            ->default(now()->toDateString())
                            ->required(),

                        Textarea::make('alasan')
                            ->label('Alasan / Keterangan')
                            ->placeholder('Jelaskan keperluan izin atau gejala sakit secara jelas...')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('bukti_foto')
                            ->label('Foto Surat Dokter / Bukti Pendukung (Opsional)')
                            ->image()
                            ->directory('izin-bukti')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Format JPG/PNG/WEBP, maksimal 2MB. Diutamakan jika mengajukan izin sakit.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipe_izin')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Izin' => 'info',
                        'Sakit' => 'warning',
                        default => 'slate',
                    }),

                TextColumn::make('rentang_tanggal')
                    ->label('Rentang Waktu')
                    ->state(function (PengajuanIzin $record) {
                        $mulai = Carbon::parse($record->tanggal_mulai)->translatedFormat('d M Y');
                        $selesai = Carbon::parse($record->tanggal_selesai)->translatedFormat('d M Y');
                        if ($record->tanggal_mulai->equalTo($record->tanggal_selesai)) {
                            return $mulai;
                        }
                        return "{$mulai} s/d {$selesai}";
                    }),

                TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('bukti_foto')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(null)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'amber',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'slate',
                    }),

                TextColumn::make('catatan_petugas')
                    ->label('Catatan Petugas Piket')
                    ->placeholder('-')
                    ->limit(35),

                TextColumn::make('disetujuiOleh.name')
                    ->label('Diproses Oleh')
                    ->placeholder('Menunggu review piket')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Approval')
                    ->options([
                        'Menunggu'  => 'Menunggu Persetujuan',
                        'Disetujui' => 'Disetujui',
                        'Ditolak'   => 'Ditolak',
                    ]),

                SelectFilter::make('tipe_izin')
                    ->label('Jenis Izin')
                    ->options([
                        'Izin'  => 'Izin',
                        'Sakit' => 'Sakit',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()
                        ->visible(fn (PengajuanIzin $record) => $record->status === 'Menunggu' && $record->izinable_id === auth()->user()?->userable_id),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Super Admin bisa melihat semua data permohonan
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Jika Wali Kelas: tampilkan izin milik pribadinya DAN izin dari seluruh siswa kelasnya
        if ($user->hasRole('wali_kelas') && $user->userable instanceof Guru) {
            $guru = $user->userable;
            $kelasId = $guru->kelas?->id;
            $siswaIds = $kelasId ? Siswa::where('kelas_id', $kelasId)->pluck('id') : collect();

            return $query->where(function ($q) use ($guru, $siswaIds) {
                $q->where(function ($sub) use ($guru) {
                    $sub->where('izinable_type', Guru::class)
                        ->where('izinable_id', $guru->id);
                })
                ->orWhere(function ($sub) use ($siswaIds) {
                    $sub->where('izinable_type', Siswa::class)
                        ->whereIn('izinable_id', $siswaIds);
                });
            });
        }

        // Jika Guru biasa: hanya tampilkan izin milik pribadinya
        if ($user->hasRole('guru') && $user->userable instanceof Guru) {
            return $query->where('izinable_type', Guru::class)
                ->where('izinable_id', $user->userable_id);
        }

        // Jika Siswa: hanya tampilkan izin milik pribadinya
        if ($user->hasRole('siswa') && $user->userable instanceof Siswa) {
            return $query->where('izinable_type', Siswa::class)
                ->where('izinable_id', $user->userable_id);
        }

        // Default: jika user memiliki profile userable
        if ($user->userable_type && $user->userable_id) {
            return $query->where('izinable_type', $user->userable_type)
                ->where('izinable_id', $user->userable_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAjukanIzins::route('/'),
        ];
    }
}
