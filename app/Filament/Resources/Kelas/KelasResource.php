<?php

namespace App\Filament\Resources\Kelas;

use App\Filament\Resources\Kelas\Pages\ManageKelas;
use App\Filament\Resources\Kelas\Pages\ViewKelas; // <--- Import Page View baru
use App\Filament\Resources\Kelas\RelationManagers\SiswasRelationManager; // <---
use App\Models\Kelas;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static string | UnitEnum | null $navigationGroup = 'Data';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Data Kelas';
    protected static ?string $pluralModelLabel = 'Data Kelas';
    protected static ?string $modelLabel = 'Kelas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->required()
                    ->placeholder('Contoh: Kelas 10-A (Ula)')
                    ->maxLength(255),

                Forms\Components\Select::make('wali_kelas_id')
                    ->label('Wali Kelas')
                    ->relationship('waliKelas', 'nama')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder('Pilih Guru yang menjadi Wali Kelas'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->wrap()
                    ->sortable(),

                Tables\Columns\TextColumn::make('waliKelas.nama')
                    ->label('Wali Kelas')
                    ->placeholder('Belum ada wali kelas')
                    ->searchable()
                    ->wrap()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ViewAction::make(), // <--- Tambahkan tombol View
                ]),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageKelas::route('/'),
            'view' => ViewKelas::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kelas')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('nama_kelas')
                            ->label('Nama Kelas'),

                        \Filament\Infolists\Components\TextEntry::make('waliKelas.nama')
                            ->label('Wali Kelas')
                            ->placeholder('Belum ada wali kelas'),

                        // 1. PERSENTASE KEHADIRAN BULAN INI (Dinamis sesuai nama bulan)
                        \Filament\Infolists\Components\TextEntry::make('persentase_kehadiran')
                            ->label(fn () => 'Kehadiran (' . now()->translatedFormat('F Y') . ')') // Menghasilkan misal: "Kehadiran (Juli 2026)"
                            ->state(function (Kelas $record) {
                                $siswaIds = $record->siswas()->pluck('id');

                                // Ambil total seluruh hari sekolah KHUSUS BULAN INI
                                $totalExpected = \App\Models\Absensi::where('absensable_type', \App\Models\Siswa::class)
                                    ->whereIn('absensable_id', $siswaIds)
                                    ->whereMonth('tanggal', now()->month) // Filter Bulan Ini
                                    ->whereYear('tanggal', now()->year)   // Filter Tahun Ini
                                    ->count();

                                if ($totalExpected === 0) {
                                    return '0%';
                                }

                                // Hitung total hadir khusus bulan ini
                                $totalHadir = \App\Models\Absensi::where('absensable_type', \App\Models\Siswa::class)
                                    ->whereIn('absensable_id', $siswaIds)
                                    ->whereIn('status_masuk', ['Tepat Waktu', 'Terlambat'])
                                    ->whereMonth('tanggal', now()->month)
                                    ->whereYear('tanggal', now()->year)
                                    ->count();

                                $persentase = ($totalHadir / $totalExpected) * 100;
                                return number_format($persentase, 1) . '%';
                            })
                            ->badge()
                            ->color('success'),

                        // 2. PERSENTASE TEPAT WAKTU BULAN INI
                        \Filament\Infolists\Components\TextEntry::make('persentase_tepat_waktu')
                            ->label(fn () => 'Tepat Waktu (' . now()->translatedFormat('F Y') . ')') // Menghasilkan misal: "Tepat Waktu (Juli 2026)"
                            ->state(function (Kelas $record) {
                                $siswaIds = $record->siswas()->pluck('id');

                                // Hitung total siswa masuk khusus bulan ini
                                $totalHadir = \App\Models\Absensi::where('absensable_type', \App\Models\Siswa::class)
                                    ->whereIn('absensable_id', $siswaIds)
                                    ->whereIn('status_masuk', ['Tepat Waktu', 'Terlambat'])
                                    ->whereMonth('tanggal', now()->month)
                                    ->whereYear('tanggal', now()->year)
                                    ->count();

                                if ($totalHadir === 0) {
                                    return '0%';
                                }

                                // Hitung total siswa tepat waktu khusus bulan ini
                                $totalTepatWaktu = \App\Models\Absensi::where('absensable_type', \App\Models\Siswa::class)
                                    ->whereIn('absensable_id', $siswaIds)
                                    ->where('status_masuk', 'Tepat Waktu')
                                    ->whereMonth('tanggal', now()->month)
                                    ->whereYear('tanggal', now()->year)
                                    ->count();

                                $persentase = ($totalTepatWaktu / $totalHadir) * 100;
                                return number_format($persentase, 1) . '%';
                            })
                            ->badge()
                            ->color('info'),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    // 3. DAFTARKAN RELATION MANAGER SISWA DI SINI
    public static function getRelations(): array
    {
        return [
            SiswasRelationManager::class,
        ];
    }
}
