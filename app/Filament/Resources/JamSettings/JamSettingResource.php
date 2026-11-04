<?php

namespace App\Filament\Resources\JamSettings;

use App\Filament\Resources\JamSettings\Pages\ManageJamSettings;
use App\Models\JamSetting;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction; // Hanya import EditAction
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class JamSettingResource extends Resource
{
    protected static ?string $model = JamSetting::class;
    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan & Sistem';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?string $navigationLabel = 'Pengaturan Jam Presensi';
    protected static ?string $pluralModelLabel = 'Pengaturan Jam';
    protected static ?string $modelLabel = 'Jam Absensi';

    public static function getFormComponents(): array
    {
        return [
            Grid::make(['default' => 1, 'md' => 2])
                ->schema([
                    // Bagian Jendela Waktu Masuk (50% lebar kolom)
                    Section::make('Jendela Absen Masuk')
                        ->description('Tentukan batas awal, batas akhir, dan batas toleransi terlambat.')
                        ->schema([
                            Forms\Components\TimePicker::make('mulai_masuk')
                                ->label('Mulai Boleh Tap Masuk')
                                ->required()
                                ->seconds(false),

                            Forms\Components\TimePicker::make('selesai_masuk')
                                ->label('Batas Akhir Tap Masuk')
                                ->required()
                                ->seconds(false),

                            Forms\Components\TimePicker::make('batas_terlambat')
                                ->label('Batas Jam Terlambat')
                                ->helperText('Scan setelah jam ini akan otomatis dicatat sebagai "Terlambat".')
                                ->required()
                                ->seconds(false)
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    // Bagian Jendela Waktu Pulang (50% lebar kolom)
                    Section::make('Jendela Absen Pulang')
                        ->description('Tentukan batas awal dan akhir siswa/guru diperbolehkan scan PULANG.')
                        ->schema([
                            Forms\Components\TimePicker::make('mulai_pulang')
                                ->label('Mulai Boleh Tap Pulang')
                                ->required()
                                ->seconds(false),

                            Forms\Components\TimePicker::make('selesai_pulang')
                                ->label('Batas Akhir Tap Pulang')
                                ->required()
                                ->seconds(false),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),

            Section::make('Libur Mingguan')
                ->description('Tentukan hari libur mingguan di Madrasah/Pondok Pesantren.')
                ->schema([
                    Forms\Components\Select::make('libur_pekanan')
                        ->label('Hari Libur Mingguan')
                        ->helperText('Pilih satu atau beberapa hari libur pekanan sekolah/pondok.')
                        ->options([
                            'Monday'    => 'Hari Senin',
                            'Tuesday'   => 'Hari Selasa',
                            'Wednesday' => 'Hari Rabu',
                            'Thursday'  => 'Hari Kamis',
                            'Friday'    => 'Hari Jumat',
                            'Saturday'  => 'Hari Sabtu',
                            'Sunday'    => 'Hari Ahad / Minggu',
                        ])
                        ->multiple()
                        ->required()
                        ->default(['Friday']),
                ])
                ->columns(1)
                ->columnSpanFull(),

            Section::make('Aturan Kehadiran & Keterlambatan Guru')
                ->description('Tentukan apakah absensi dan batas terlambat guru disamakan dengan jam sekolah atau dinamis per guru berdasarkan jadwal mengajar.')
                ->schema([
                    Forms\Components\Radio::make('mode_absensi_guru')
                        ->label('Mode Kehadiran Guru')
                        ->options([
                            'statis' => 'Sama Setiap Hari (Semua Guru Wajib Hadir Setiap Hari Kerja)',
                            'jadwal' => 'Dinamis per Guru Berdasarkan Jadwal Mengajar',
                        ])
                        ->default('statis')
                        ->reactive()
                        ->required()
                        ->helperText('Jika mode Dinamis dipilih, guru hanya wajib hadir dan dihitung alpa pada hari di mana ia memiliki jadwal mengajar.'),

                    Forms\Components\Radio::make('aturan_terlambat_guru')
                        ->label('Aturan Jam Keterlambatan Guru')
                        ->options([
                            'jadwal_mengajar' => 'Berdasarkan Jam Mulai Mengajar Pertama di Hari Tersebut (Contoh: Jadwal mulai jam ke-2 pukul 07:20, maka terlambat jika lewat 07:20)',
                            'jam_sekolah' => 'Mengikuti Batas Jam Terlambat Umum Sekolah',
                        ])
                        ->default('jadwal_mengajar')
                        ->visible(fn (callable $get) => $get('mode_absensi_guru') === 'jadwal')
                        ->required(fn (callable $get) => $get('mode_absensi_guru') === 'jadwal'),

                    Forms\Components\TimePicker::make('batas_terlambat_guru')
                        ->label('Batas Jam Terlambat Khusus Guru (Opsional)')
                        ->helperText('Kosongkan jika ingin sama persis dengan batas terlambat umum sekolah.')
                        ->seconds(false)
                        ->visible(fn (callable $get) => $get('mode_absensi_guru') === 'statis'),
                ])
                ->columns(1)
                ->columnSpanFull(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::getFormComponents())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->content(fn () => view('filament.resources.jam-settings.infolist'))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('mulai_masuk')
                    ->label('Mulai Masuk')
                    ->time('H:i')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('selesai_masuk')
                    ->label('Batas Akhir Masuk')
                    ->time('H:i')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('batas_terlambat')
                    ->label('Batas Terlambat')
                    ->time('H:i')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('mulai_pulang')
                    ->label('Mulai Pulang')
                    ->time('H:i')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('selesai_pulang')
                    ->label('Batas Akhir Pulang')
                    ->time('H:i')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('libur_pekanan')
                    ->label('Libur Mingguan')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                        'Sunday' => 'Ahad', default => $state,
                    })
                    ->placeholder('-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('mode_absensi_guru')
                    ->label('Mode Guru')
                    ->formatStateUsing(fn ($state) => $state === 'jadwal' ? 'Dinamis (Jadwal)' : 'Sama (Statis)')
                    ->badge()
                    ->color(fn ($state) => $state === 'jadwal' ? 'info' : 'gray')
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageJamSettings::route('/'),
        ];
    }
}
