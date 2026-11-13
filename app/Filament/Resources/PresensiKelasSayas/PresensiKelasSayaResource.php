<?php

namespace App\Filament\Resources\PresensiKelasSayas;

use App\Filament\Resources\PresensiKelasSayas\Pages\ManagePresensiKelasSayas;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Siswa;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use UnitEnum;

class PresensiKelasSayaResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $slug = 'presensi-kelas-saya';

    protected static string | UnitEnum | null $navigationGroup = 'Presensi & Kehadiran';
    protected static ?int $navigationSort = 2;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Presensi Kelas Saya';
    protected static ?string $pluralModelLabel = 'Presensi Siswa Binaan';
    protected static ?string $modelLabel = 'Presensi Siswa';

    /**
     * Hak akses eksklusif: HANYA untuk role wali_kelas yang memiliki kelas binaan aktif.
     * Super Admin, Admin Absensi, dan Guru tanpa kelas binaan TIDAK memiliki akses ke menu ini.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user || ! $user->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return false;
        }

        return $user->userable instanceof Guru && $user->userable->kelas !== null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    /**
     * Badge navigasi: Menampilkan jumlah anak yang belum hadir / alpa hari ini
     */
    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (! $user || ! $user->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return null;
        }

        $kelas = $user->userable instanceof Guru ? $user->userable->kelas : null;
        if (! $kelas) {
            return null;
        }

        $siswaIds = $kelas->siswas()->pluck('id');
        $belumHadir = Absensi::where('tanggal', Carbon::today()->toDateString())
            ->where('absensable_type', Siswa::class)
            ->whereIn('absensable_id', $siswaIds)
            ->where(function ($q) {
                $q->where('status_masuk', 'Belum Absen')
                  ->orWhere('status_masuk', 'Alpa');
            })
            ->count();

        return $belumHadir > 0 ? (string) $belumHadir : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah siswa binaan yang belum hadir / alpa hari ini';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->default(now()),

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
                    'Belum Absen' => 'Belum Absen',
                ])
                ->required(),

            Forms\Components\TimePicker::make('jam_pulang')
                ->label('Jam Pulang')
                ->seconds(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('absensable.foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-avatar.svg'))
                    ->disk('public'),

                Tables\Columns\TextColumn::make('absensable.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Absensi $record): string => 'NIS: ' . ($record->absensable?->nis ?? '-')),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->alignCenter(),

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
                    ->label('Status Kehadiran')
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

                Tables\Columns\TextColumn::make('absensable.nomor_wa_wali')
                    ->label('No. WA Wali')
                    ->placeholder('Belum diisi')
                    ->alignCenter()
                    ->visibleFrom('md')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_masuk')
                    ->label('Status Kehadiran')
                    ->options([
                        'Tepat Waktu' => 'Hadir Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Sakit' => 'Sakit (S)',
                        'Izin' => 'Izin (I)',
                        'Alpa' => 'Alpa (A)',
                        'Belum Absen' => 'Belum Hadir (Belum Absen)',
                    ]),

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
                            $indicators['dari_tanggal'] = 'Dari: ' . Carbon::parse($data['dari_tanggal'])->translatedFormat('d M Y');
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai: ' . Carbon::parse($data['sampai_tanggal'])->translatedFormat('d M Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                // 1. Aksi Pintar: Chat WhatsApp Wali Murid
                Action::make('chat_wa')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->tooltip('Kirim pesan WhatsApp ke nomor wali murid')
                    ->visible(fn (Absensi $record) => !empty($record->absensable?->nomor_wa_wali))
                    ->url(function (Absensi $record) {
                        $rawPhone = $record->absensable?->nomor_wa_wali ?? '';
                        $phone = preg_replace('/[^0-9]/', '', $rawPhone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }

                        $namaSiswa = $record->absensable?->nama ?? 'Siswa';
                        $namaKelas = $record->absensable?->kelas?->nama_kelas ?? 'Kelas';
                        $tgl = Carbon::parse($record->tanggal)->translatedFormat('d F Y');
                        $status = $record->status_masuk ?? 'Belum Absen';
                        $jam = $record->jam_masuk ? " (Jam Masuk: {$record->jam_masuk})" : '';

                        $pesan = "Assalamu'alaikum Wr. Wb. Yth. Bapak/Ibu Wali dari *{$namaSiswa}*.\n\n"
                            . "Kami dari pihak sekolah menginfokan bahwa ananda tercatat pada presensi hari ini ({$tgl}) di *{$namaKelas}* dengan status: *{$status}*{$jam}.\n\n"
                            . "Jika ada kendala atau keterangan izin/sakit, mohon dapat mengonfirmasikan kepada kami.\n\n"
                            . "Salam hormat,\n*Wali Kelas {$namaKelas}*";

                        return 'https://api.whatsapp.com/send?phone=' . $phone . '&text=' . rawurlencode($pesan);
                    }, shouldOpenInNewTab: true),

                // 2. Aksi Tandai Izin / Sakit jika wali murid lapor langsung ke wali kelas
                ActionGroup::make([
                    Action::make('tandai_sakit')
                        ->label('Tandai Sakit')
                        ->icon('heroicon-o-heart')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Siswa Sakit')
                        ->modalDescription('Ubah status presensi siswa ini menjadi Sakit (S)?')
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

                    Action::make('tandai_izin')
                        ->label('Tandai Izin')
                        ->icon('heroicon-o-envelope-open')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Siswa Izin')
                        ->modalDescription('Ubah status presensi siswa ini menjadi Izin (I)?')
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

                    Action::make('tandai_hadir')
                        ->label('Tandai Hadir Manual')
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
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePresensiKelasSayas::route('/'),
        ];
    }

    /**
     * Scope Kueri: 100% Mengunci hanya siswa dari kelas yang diampu oleh Wali Kelas tersebut
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user || ! $user->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return $query->whereRaw('1 = 0');
        }

        $kelas = ($user->userable instanceof Guru) ? $user->userable->kelas : null;
        if (! $kelas) {
            return $query->whereRaw('1 = 0');
        }

        $siswaIds = $kelas->siswas()->pluck('id');

        return $query->where('absensable_type', Siswa::class)
            ->whereIn('absensable_id', $siswaIds);
    }
}
