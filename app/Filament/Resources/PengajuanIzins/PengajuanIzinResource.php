<?php

namespace App\Filament\Resources\PengajuanIzins;

use App\Filament\Resources\PengajuanIzins\Pages\ManagePengajuanIzins;
use App\Models\Guru;
use App\Models\JadwalGuruPiket;
use App\Models\PengajuanIzin;
use App\Models\Siswa;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PengajuanIzinResource extends Resource
{
    protected static ?string $model = PengajuanIzin::class;

    protected static string | UnitEnum | null $navigationGroup = 'Operasional Harian';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Pengajuan Izin Masuk';
    protected static ?string $pluralModelLabel = 'Pengajuan Izin Masuk';
    protected static ?string $modelLabel = 'Persetujuan Izin';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Super Admin, Admin Absensi, dan Staff Absensi berhak mengelola
        if ($user->hasAnyRole(['super_admin', 'admin_absensi', 'staff_absensi'])) {
            return true;
        }

        // Guru dan Wali Kelas HANYA JIKA sedang bertugas piket hari ini
        if ($user->hasAnyRole(['guru', 'wali_kelas']) && JadwalGuruPiket::isUserPiketToday($user)) {
            return true;
        }

        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return false; // Pengajuan baru diajukan melalui resource "Ajukan Izin"
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PengajuanIzin::where('status', 'Menunggu')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Izin / Sakit')
                    ->schema([
                        Select::make('tipe_izin')
                            ->label('Jenis Pengajuan')
                            ->options([
                                'Izin'  => 'Izin',
                                'Sakit' => 'Sakit',
                            ])
                            ->disabled(),

                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Izin')
                            ->disabled(),

                        DatePicker::make('tanggal_selesai')
                            ->label('Sampai Tanggal')
                            ->disabled(),

                        Textarea::make('alasan')
                            ->label('Alasan / Keterangan')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),

                        FileUpload::make('bukti_foto')
                            ->label('Foto Surat Dokter / Bukti Pendukung')
                            ->disk('public')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('catatan_petugas')
                            ->label('Catatan Petugas Piket')
                            ->placeholder('Belum ada catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isGuruPiketHariIni = JadwalGuruPiket::isUserPiketToday($user);
        $canApprove = $isGuruPiketHariIni || $user?->hasRole('super_admin') || $user?->hasRole('admin_absensi') || $user?->hasRole('staff_absensi');

        return $table
            ->columns([
                TextColumn::make('pemohon_info')
                    ->label('Nama Pemohon')
                    ->state(function (PengajuanIzin $record) {
                        return $record->izinable?->nama ?? 'Tidak Diketahui';
                    })
                    ->description(function (PengajuanIzin $record) {
                        if ($record->izinable instanceof Siswa) {
                            $kelas = $record->izinable->kelas?->nama_kelas ?? 'Tanpa Kelas';
                            return "Siswa • {$kelas} • NIS: {$record->izinable->nis}";
                        } elseif ($record->izinable instanceof Guru) {
                            return "Guru / Tenaga Pendidik • NIP: {$record->izinable->nip}";
                        }
                        return '-';
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHasMorph('izinable', [Siswa::class, Guru::class], function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('tipe_izin')
                    ->label('Jenis')
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
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'amber',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'slate',
                    }),

                TextColumn::make('disetujuiOleh.name')
                    ->label('Diproses Oleh')
                    ->placeholder('Belum diproses')
                    ->toggleable(),

                TextColumn::make('catatan_petugas')
                    ->label('Catatan Petugas')
                    ->placeholder('-')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                // Tombol Setujui (Hanya muncul untuk Guru Piket / Admin pada status Menunggu)
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'Menunggu' && $canApprove)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Izin')
                    ->modalDescription(fn (PengajuanIzin $record) => "Apakah Anda yakin menyetujui izin {$record->tipe_izin} atas nama " . ($record->izinable?->nama ?? 'pemohon') . "? Sistem akan otomatis mencatat status presensinya di tabel absensi.")
                    ->form([
                        Textarea::make('catatan_petugas')
                            ->label('Catatan Petugas Piket (Opsional)')
                            ->placeholder('Contoh: Disetujui berdasarkan surat keterangan terlampir.'),
                    ])
                    ->action(function (PengajuanIzin $record, array $data) {
                        $record->approve(auth()->id(), $data['catatan_petugas'] ?? null);

                        Notification::make()
                            ->title('Pengajuan Izin Disetujui')
                            ->body('Izin telah disetujui dan status presensi otomatis diperbarui di rekap harian.')
                            ->success()
                            ->send();
                    }),

                // Tombol Tolak (Hanya muncul untuk Guru Piket / Admin pada status Menunggu)
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'Menunggu' && $canApprove)
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan Izin')
                    ->form([
                        Textarea::make('catatan_petugas')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Jelaskan alasan penolakan izin secara jelas...'),
                    ])
                    ->action(function (PengajuanIzin $record, array $data) {
                        $record->reject(auth()->id(), $data['catatan_petugas'] ?? null);

                        Notification::make()
                            ->title('Pengajuan Izin Ditolak')
                            ->body('Permohonan izin berhasil ditolak dengan catatan penolakan.')
                            ->danger()
                            ->send();
                    }),

                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') ?? false),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // Semua user yang berhak mengakses (Admin atau Guru Piket Hari Ini) melihat semua data izin masuk
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePengajuanIzins::route('/'),
        ];
    }
}
