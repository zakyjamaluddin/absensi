<?php

namespace App\Filament\Resources\Gurus;

use App\Filament\Resources\Gurus\Pages\ManageGurus;
use App\Models\Guru;
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
use Filament\Schemas\Schema; // <--- Menggunakan Schema baru Filament v5
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use UnitEnum;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;
    protected static string | UnitEnum | null $navigationGroup = 'Data Akademik';
    protected static ?int $navigationSort = 2;


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Data Guru';
    protected static ?string $pluralModelLabel = 'Data Guru';
    protected static ?string $modelLabel = 'Guru';

    // Type-hint diubah ke Schema sesuai dokumentasi terbaru
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\FileUpload::make('foto')
                    ->label('Foto Guru / Ustadz')
                    ->image()
                    ->avatar()
                    ->directory('gurus')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->helperText('Format JPG/PNG/WEBP, maksimal 2MB. Disajikan berbentuk bulat/avatar.')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nip')
                    ->label('NIP / ID Card')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: 19810101')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required()
                    ->placeholder('Nama Lengkap beserta Gelar')
                    ->maxLength(255),

                Forms\Components\TextInput::make('no_hp')
                    ->label('No. HP / WhatsApp')
                    ->tel()
                    ->placeholder('Contoh: 081234567890')
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_notif_wa_aktif')
                    ->label('Notifikasi WhatsApp Aktif')
                    ->helperText('Aktifkan agar guru menerima pesan notifikasi WhatsApp saat melakukan scan absensi.')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(asset('images/default-avatar.svg')),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(function (Guru $record) {
                        return $record->nip ? "NIP: {$record->nip}" : "NIP Belum diisi";
                    }),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->visibleFrom('md')
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('is_notif_wa_aktif')
                    ->label('Notif WA')
                    ->sortable()
                    ->visibleFrom('md')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    static::getRekapAbsensiAction(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('unduh_qr')
                        ->label('Unduh QR')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Guru $record) {
                            $namaClean = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ' '], '_', $record->nama);

                            return response()->streamDownload(function () use ($record) {
                                echo QrCode::size(300)
                                    ->margin(1)
                                    ->generate($record->nip);
                            }, "Guru_{$namaClean}.svg", [
                                'Content-Type' => 'image/svg+xml',
                            ]);
                        }),
                ]),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRekapAbsensiAction(): Action
    {
        return Action::make('rekap_absensi')
            ->label('Rekap Absensi')
            ->icon('heroicon-o-chart-bar')
            ->color('success')
            ->modalHeading(fn (Guru $record) => "Rekap Presensi Guru: {$record->nama}")
            ->modalSubheading(fn (Guru $record) => "NIP: {$record->nip}" . ($record->kelas ? " • Wali Kelas: {$record->kelas->nama_kelas}" : ''))
            ->modalWidth('4xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->form([
                \Filament\Schemas\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('modal_periode')
                        ->label('Pilih Periode')
                        ->options([
                            'bulan_ini' => 'Bulan Ini (' . now()->translatedFormat('F Y') . ')',
                            'tahun_ini' => 'Tahun Ini (' . now()->year . ')',
                            'semua' => 'Semua Waktu',
                            'custom' => 'Custom (Pilih Rentang Tanggal)',
                        ])
                        ->default('bulan_ini')
                        ->live(),

                    Forms\Components\DatePicker::make('modal_dari')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth()->toDateString())
                        ->visible(fn ($get) => $get('modal_periode') === 'custom')
                        ->live(),

                    Forms\Components\DatePicker::make('modal_sampai')
                        ->label('Sampai Tanggal')
                        ->default(now()->toDateString())
                        ->visible(fn ($get) => $get('modal_periode') === 'custom')
                        ->live(),
                ]),

                Forms\Components\ViewField::make('rekap_view')
                    ->view('filament.components.modal-rekap-absensi'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGurus::route('/'),
        ];
    }
}
