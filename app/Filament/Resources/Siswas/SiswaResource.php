<?php
namespace App\Filament\Resources\Siswas;

use App\Filament\Resources\Siswas\Pages\ManageSiswas;
use App\Models\Siswa;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use UnitEnum;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static string | UnitEnum | null $navigationGroup = 'Data';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Data Siswa';
    protected static ?string $pluralModelLabel = 'Data Siswa';
    protected static ?string $modelLabel = 'Siswa';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nis')
                    ->label('NIS / No. Kartu')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: 20260001')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap Siswa')
                    ->required()
                    ->placeholder('Nama Lengkap Siswa')
                    ->maxLength(255),

                Forms\Components\Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih Kelas Siswa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(function (Siswa $record) {
                        return $record->nis ?? 'Belum ada NIS';
                    }),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Filter Kelas')
                    ->relationship('kelas', 'nama_kelas'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('unduh_qr')
                        ->label('Unduh QR')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Siswa $record) {
                            $namaClean = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ' '], '_', $record->nama);

                            return response()->streamDownload(function () use ($record) {
                                // Cukup hapus ->format('png'), library akan otomatis menghasilkan SVG berkualitas tinggi
                                echo QrCode::size(300)
                                    ->margin(1)
                                    ->generate($record->nis);
                            }, "Siswa_{$namaClean}.svg", [
                                'Content-Type' => 'image/svg+xml',
                            ]);
                        }),
                ]),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSiswas::route('/'),
        ];
    }

    // Di dalam class SiswaResource...
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // JIKA yang login memiliki role 'Wali Kelas' (Filament Shield) dan memiliki profil Guru
        if ($user && $user->hasRole('Wali Kelas') && $user->userable instanceof \App\Models\Guru) {
            // Cari ID Kelas yang diampu oleh ustadz tersebut
            $kelasId = $user->userable->kelas?->id;

            // Batasi kueri agar siswa yang tampil HANYA siswa dari kelas ustadz tersebut
            return $query->where('kelas_id', $kelasId);
        }

        return $query;
    }
}
