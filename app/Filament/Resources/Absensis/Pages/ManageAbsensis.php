<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\JamSetting;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class ManageAbsensis extends ManageRecords
{
    protected static string $resource = AbsensiResource::class;

    public function mount(): void
    {
        parent::mount();

        // Pastikan santri & guru memiliki record Belum Absen untuk hari ini jika belum absen
        $user = auth()->user();
        $kelasId = null;
        if ($user && $user->hasAnyRole(['Wali Kelas', 'wali_kelas']) && $user->userable instanceof \App\Models\Guru) {
            $kelasId = $user->userable->kelas?->id;
        }

        if (! $user?->hasRole('siswa')) {
            Absensi::ensureDailyRecordsForDate(now()->toDateString(), $kelasId);
        }
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'hari_ini';
    }

    public function getTabs(): array
    {
        return [
            'hari_ini' => Tab::make('Hari Ini')
                ->icon('heroicon-o-calendar-days')
                ->badge(fn () => Absensi::whereDate('tanggal', Carbon::today())->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('tanggal', Carbon::today())),

            'kemarin' => Tab::make('Kemarin')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('tanggal', Carbon::yesterday())),

            'minggu_ini' => Tab::make('Minggu Ini')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),

            'bulan_ini' => Tab::make('Bulan Ini')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('tanggal', Carbon::now()->month)->whereYear('tanggal', Carbon::now()->year)),

            'semua' => Tab::make('Semua Tanggal')
                ->icon('heroicon-o-archive-box'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['guru', 'wali_kelas', 'Wali Kelas', 'siswa'])) {
            return [];
        }

        return [
            Action::make('input_manual')
                ->label('Input Absen Manual')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form(AbsensiResource::getFormComponents()) // <--- Diubah memanggil helper getFormComponents()
                ->action(function (array $data) {
                    $exists = Absensi::where('tanggal', $data['tanggal'])
                        ->where('absensable_type', $data['absensable_type'])
                        ->where('absensable_id', $data['absensable_id'])
                        ->exists();

                    if ($exists) {
                        Notification::make()
                            ->title('Gagal Input Manual')
                            ->body('Data absensi orang tersebut pada tanggal yang dipilih sudah ada di sistem.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $absensi = Absensi::create($data);

                    if ($absensi->absensable instanceof Siswa) {
                        if (!empty($absensi->jam_masuk)) {
                            $settings = JamSetting::first();
                            WhatsAppService::sendNotifMasukWali($absensi->absensable, $absensi, $settings);
                        }
                        if (!empty($absensi->jam_pulang)) {
                            WhatsAppService::sendNotifPulangWali($absensi->absensable, $absensi);
                        }
                        if ($absensi->status_masuk === 'Alpa') {
                            $settings = JamSetting::first();
                            $batasAkhir = $settings?->selesai_masuk ?? '08:00';
                            WhatsAppService::sendNotifAlpaWali($absensi->absensable, $absensi->tanggal, $batasAkhir);
                        }
                    } elseif ($absensi->absensable instanceof \App\Models\Guru) {
                        $settings = JamSetting::first();
                        $batasTerlambatAcuan = null;
                        if ($settings) {
                            if ($settings->mode_absensi_guru === 'jadwal') {
                                if ($settings->aturan_terlambat_guru === 'jadwal_mengajar') {
                                    $earliest = $absensi->absensable->getEarliestJamMulai($absensi->tanggal);
                                    $batasTerlambatAcuan = $earliest ?: ($settings->batas_terlambat_guru ?: $settings->batas_terlambat);
                                } else {
                                    $batasTerlambatAcuan = $settings->batas_terlambat_guru ?: $settings->batas_terlambat;
                                }
                            } else {
                                $batasTerlambatAcuan = $settings->batas_terlambat_guru ?: $settings->batas_terlambat;
                            }
                        }

                        if (!empty($absensi->jam_masuk)) {
                            WhatsAppService::sendNotifMasukGuru($absensi->absensable, $absensi, $batasTerlambatAcuan);
                        }
                        if (!empty($absensi->jam_pulang)) {
                            WhatsAppService::sendNotifPulangGuru($absensi->absensable, $absensi);
                        }
                    }

                    Notification::make()
                        ->title('Berhasil')
                        ->body('Data absensi berhasil dicatat secara manual.')
                        ->success()
                        ->send();
                }),

            // 2. TOMBOL SAKTI BARU: Ekspor Semua Kelas Sekaligus Berbasis Multi-Sheet
            Action::make('ekspor_semua_kelas')
                ->label('Ekspor Semua Kelas')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                // Ambil daftar bulan & tahun dinamis dari database yang sudah kita buat sebelumnya
                ->form([
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options(function () {
                            $bulanQuery = Absensi::selectRaw('MONTH(tanggal) as month')->distinct()->orderBy('month', 'asc')->pluck('month')->toArray();
                            $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                            $bulanOptions = [];
                            foreach ($bulanQuery as $m) {
                                $bulanOptions[sprintf('%02d', $m)] = $monthNames[(int)$m];
                            }
                            return empty($bulanOptions) ? [now()->format('m') => now()->translatedFormat('F')] : $bulanOptions;
                        })
                        ->default(now()->format('m'))
                        ->required(),

                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options(function () {
                            $tahunOptions = Absensi::selectRaw('YEAR(tanggal) as year')->distinct()->orderBy('year', 'desc')->pluck('year', 'year')->toArray();
                            return empty($tahunOptions) ? [now()->year => now()->year] : $tahunOptions;
                        })
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('admin.kelas.ekspor-semua', [
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                    ]);
                }),

            // RE-AKTIFKAN TOMBOL PROSES ALPA SECARA DINAMIS
            Action::make('proses_alpa')
                ->label('Proses Alpa Hari Ini')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                // TOMBOL HANYA AKAN MUNCUL JIKA METODE DISETEL KE "MANUAL" OLEH ADMIN
                ->visible(fn () => \App\Models\WaSetting::first()?->tipe_proses_alpa === 'Manual')
                ->action(function () {
                    // Panggil perintah artisan secara programmatif dengan opsi --manual murni!
                    Artisan::call('app:process-daily-alpa', ['--manual' => true]);

                    \Filament\Notifications\Notification::make()
                        ->title('Proses Alpa Selesai')
                        ->body("Proses pendeteksian alpa masal dan pengiriman rekap WhatsApp sukses dijalankan!")
                        ->success()
                        ->send();
                }),
        ];
    }
}
