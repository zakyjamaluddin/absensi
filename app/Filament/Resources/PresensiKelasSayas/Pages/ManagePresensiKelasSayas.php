<?php

namespace App\Filament\Resources\PresensiKelasSayas\Pages;

use App\Filament\Resources\PresensiKelasSayas\PresensiKelasSayaResource;
use App\Filament\Resources\PresensiKelasSayas\Widgets\PresensiKelasSayaStatsWidget;
use App\Models\Absensi;
use App\Models\Guru;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ManagePresensiKelasSayas extends ManageRecords
{
    protected static string $resource = PresensiKelasSayaResource::class;

    public function mount(): void
    {
        parent::mount();

        // Pastikan seluruh siswa di kelas binaan sudah memiliki catatan record hari ini
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Wali Kelas', 'wali_kelas']) && $user->userable instanceof Guru) {
            $kelasId = $user->userable->kelas?->id;
            if ($kelasId) {
                Absensi::ensureDailyRecordsForDate(now()->toDateString(), $kelasId);
            }
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PresensiKelasSayaStatsWidget::class,
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'hari_ini';
    }

    public function getTabs(): array
    {
        $user = auth()->user();
        $kelas = ($user && $user->userable instanceof Guru) ? $user->userable->kelas : null;
        $siswaIds = $kelas ? $kelas->siswas()->pluck('id') : collect();

        return [
            'hari_ini' => Tab::make('Hari Ini')
                ->icon('heroicon-o-calendar-days')
                ->badge(fn () => Absensi::whereDate('tanggal', Carbon::today())
                    ->where('absensable_type', \App\Models\Siswa::class)
                    ->whereIn('absensable_id', $siswaIds)
                    ->count())
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
        return [
            Action::make('ekspor_kelas')
                ->label('Ekspor Rekap Kelas (Excel)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ])
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
                    $user = auth()->user();
                    $kelas = ($user && $user->userable instanceof Guru) ? $user->userable->kelas : null;

                    if (! $kelas) {
                        return;
                    }

                    return redirect()->route('admin.kelas.ekspor', [
                        'kelas' => $kelas->id,
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                    ]);
                }),
        ];
    }
}
