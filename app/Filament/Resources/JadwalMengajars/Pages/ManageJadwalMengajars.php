<?php

namespace App\Filament\Resources\JadwalMengajars\Pages;

use App\Filament\Resources\JadwalMengajars\JadwalMengajarResource;
use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageJadwalMengajars extends ManageRecords
{
    protected static string $resource = JadwalMengajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('atur_jam_pelajaran')
                ->label('Atur Jam Pelajaran')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->modalHeading('Atur Jam Pelajaran')
                ->modalDescription('Tentukan pemetaan jam pelajaran sekolah (jam ke-, waktu mulai, dan waktu selesai). Jam ini digunakan sebagai acuan jadwal mengajar guru.')
                ->modalWidth('xl')
                ->fillForm(function (): array {
                    return [
                        'jam_pelajarans' => JamPelajaran::orderBy('jam_ke', 'asc')->get()->map(fn ($item) => [
                            'jam_ke' => $item->jam_ke,
                            'jam_mulai' => $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : null,
                            'jam_selesai' => $item->jam_selesai ? Carbon::parse($item->jam_selesai)->format('H:i') : null,
                        ])->toArray(),
                    ];
                })
                ->form([
                    Repeater::make('jam_pelajarans')
                        ->label('Daftar Jam Pelajaran')
                        ->schema([
                            TextInput::make('jam_ke')
                                ->label('Jam Ke')
                                ->numeric()
                                ->required()
                                ->placeholder('Misal: 1'),
                            TimePicker::make('jam_mulai')
                                ->label('Mulai')
                                ->seconds(false)
                                ->required(),
                            TimePicker::make('jam_selesai')
                                ->label('Selesai')
                                ->seconds(false)
                                ->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('+ Tambah Jam Pelajaran')
                        ->reorderableWithButtons(),
                ])
                ->action(function (array $data) {
                    $rows = $data['jam_pelajarans'] ?? [];
                    if (empty($rows)) {
                        Notification::make()
                            ->title('Daftar jam pelajaran tidak boleh kosong')
                            ->danger()
                            ->send();
                        return;
                    }

                    DB::transaction(function () use ($rows) {
                        JamPelajaran::truncate();
                        foreach ($rows as $row) {
                            JamPelajaran::create([
                                'jam_ke' => (int) $row['jam_ke'],
                                'jam_mulai' => $row['jam_mulai'],
                                'jam_selesai' => $row['jam_selesai'],
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Jam Pelajaran Berhasil Disimpan')
                        ->body('Data pemetaan jam pelajaran berhasil diperbarui.')
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Tambah Jadwal Mengajar')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah Jadwal Mengajar Guru')
                ->modalDescription('Pilih nama guru dan tentukan hari serta jam mengajarnya.')
                ->modalWidth('2xl')
                ->form([
                    Select::make('guru_id')
                        ->label('Nama Guru / Pengajar')
                        ->relationship('guru', 'nama')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder('Pilih Guru')
                        ->columnSpanFull(),

                    Repeater::make('jadwals')
                        ->label('Daftar Hari & Jam Mengajar')
                        ->schema([
                            Select::make('hari')
                                ->label('Hari')
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
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->defaultItems(1)
                        ->addActionLabel('+ Tambah Jam Mengajar Lainnya')
                        ->columnSpanFull(),
                ])
                ->using(function (array $data): JadwalMengajar {
                    $guruId = $data['guru_id'];
                    $jadwals = $data['jadwals'] ?? [];
                    $jamPelajarans = JamPelajaran::all()->keyBy('jam_ke');

                    $created = null;
                    foreach ($jadwals as $item) {
                        $mulai = (int) $item['mulai_jam_ke'];
                        $sampai = (int) ($item['sampai_jam_ke'] ?? $mulai);

                        $start = min($mulai, $sampai);
                        $end = max($mulai, $sampai);

                        $jpStart = $jamPelajarans->get($start);
                        $jpEnd = $jamPelajarans->get($end);

                        $jamMulai = $jpStart?->jam_mulai ?? '07:00:00';
                        $jamSelesai = $jpEnd?->jam_selesai ?? '08:00:00';

                        $jamKe = ($start === $end) ? (string) $start : "{$start} - {$end}";

                        $created = JadwalMengajar::create([
                            'guru_id' => $guruId,
                            'hari' => $item['hari'],
                            'kelas_id' => null,
                            'mata_pelajaran' => null,
                            'jam_ke' => $jamKe,
                            'mulai_jam_ke' => $start,
                            'sampai_jam_ke' => $end,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                        ]);
                    }

                    return $created ?? new JadwalMengajar();
                })
                ->successNotificationTitle('Jadwal mengajar berhasil disimpan'),
        ];
    }
}
