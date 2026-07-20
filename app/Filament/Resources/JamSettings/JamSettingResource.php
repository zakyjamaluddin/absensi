<?php

namespace App\Filament\Resources\JamSettings;

use App\Filament\Resources\JamSettings\Pages\ManageJamSettings;
use App\Models\JamSetting;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction; // Hanya import EditAction
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class JamSettingResource extends Resource
{
    protected static ?string $model = JamSetting::class;
    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan';


    // ICON: Menggunakan ikon Jam (Clock) agar relevan
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Pengaturan Jam';
    protected static ?string $pluralModelLabel = 'Pengaturan Jam';
    protected static ?string $modelLabel = 'Jam Absensi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bagian Jendela Waktu Masuk
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

                        // TAMBAHKAN INPUT INI DI DALAM SECTION MASUK
                        Forms\Components\TimePicker::make('batas_terlambat')
                            ->label('Batas Jam Terlambat')
                            ->helperText('Scan setelah jam ini akan otomatis dicatat sebagai "Terlambat".')
                            ->required()
                            ->seconds(false),
                    ])->columns(3), // Ubah kolom menjadi 3 agar muat berdampingan

                // Bagian Jendela Waktu Pulang
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mulai_masuk')
                    ->label('Mulai Masuk')
                    ->time('H:i') // Tampilkan format Jam:Menit saja
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('selesai_masuk')
                    ->label('Batas Akhir Masuk')
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
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
                // Kita HANYA menyediakan aksi Edit, tanpa Hapus
            ])
            ->bulkActions([
                // Kosongkan agar tidak ada fitur hapus massal
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageJamSettings::route('/'),
        ];
    }
}
