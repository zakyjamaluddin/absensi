<?php

namespace App\Filament\Resources\WaSettings;

use App\Filament\Resources\WaSettings\Pages\ManageWaSettings;
use App\Models\WaSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class WaSettingResource extends Resource
{
    protected static ?string $model = WaSetting::class;

    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan & Sistem';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Integrasi WhatsApp';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Koneksi WhatsApp Gateway')
                    ->description('Masukkan Token API Anda yang didapatkan dari dashboard Sidobe.com.')
                    ->schema([
                        TextInput::make('token')
                            ->label('API Token Sidobe')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                    ]),

                // SECTIONS BARU KHUSUS UNTUK KONFIGURASI ALPA
                Section::make('Konfigurasi Laporan Alpa Harian')
                    ->description('Tentukan bagaimana dan kapan sistem merekam data Alpa dan mengirimkannya ke WhatsApp.')
                    ->schema([
                        Select::make('tipe_proses_alpa')
                            ->label('Metode Proses Alpa')
                            ->options([
                                'Otomatis' => 'Otomatis (Jadwal Cron Job)',
                                'Manual' => 'Manual (Tombol di Rekap Absensi)',
                            ])
                            ->required()
                            ->reactive() // Membuat pilihan di bawahnya dinamis/reaktif
                            ->default('Otomatis'),

                        TimePicker::make('jam_proses_alpa')
                            ->label('Jam Eksekusi Otomatis')
                            ->helperText('Pilih jam berapa sistem akan memproses Alpa secara otomatis setiap hari.')
                            ->seconds(false)
                            ->required()
                            // Input jam hanya muncul jika admin memilih metode "Otomatis"
                            ->visible(fn (callable $get) => $get('tipe_proses_alpa') === 'Otomatis'),

                        TextInput::make('nomor_wa_rekap_alpa')
                            ->label('Nomor WhatsApp Penerima Rekap Alpa')
                            ->placeholder('Contoh: 08123456789 atau 08123456789, 08987654321')
                            ->helperText('Nomor WhatsApp tujuan untuk menerima rekap harian siswa & guru alpa (misal: Kepala Sekolah, Guru Piket, atau Admin). Pisahkan dengan tanda koma (,) jika lebih dari satu nomor. Jika dikosongkan, sistem akan otomatis mengirimkan ke nomor HP Guru yang ber-role Super Admin.')
                            ->maxLength(255)
                            ->nullable(),

                        Toggle::make('notif_wa_admin_rekap_alpa')
                            ->label('Kirim Rekap Siswa Alpa ke WhatsApp')
                            ->helperText('Kirim rekap harian daftar siswa yang membolos (alpa) per kelas ke nomor WhatsApp penerima rekap.')
                            ->default(true),
                    ])->columns(1),

                Section::make('Notifikasi WhatsApp ke Wali Murid')
                    ->description('Aktifkan atau nonaktifkan pengiriman notifikasi otomatis ke nomor WhatsApp wali santri/murid.')
                    ->schema([
                        Toggle::make('notif_wa_wali_masuk')
                            ->label('Notifikasi Absensi Datang')
                            ->helperText('Kirim pesan WA ke wali murid saat siswa berhasil scan absen masuk (disertai info waktu & status tepat waktu/terlambat).')
                            ->default(true),

                        Toggle::make('notif_wa_wali_pulang')
                            ->label('Notifikasi Absensi Pulang')
                            ->helperText('Kirim pesan WA ke wali murid saat siswa berhasil scan absen pulang.')
                            ->default(true),

                        Toggle::make('notif_wa_wali_alpa')
                            ->label('Notifikasi Tidak Hadir (Alpa)')
                            ->helperText('Kirim pesan WA peringatan ke wali murid jika siswa belum melakukan absensi setelah batas akhir jam masuk sekolah.')
                            ->default(true),
                    ])->columns(3),

                Section::make('Notifikasi WhatsApp & Laporan Guru')
                    ->description('Aktifkan atau nonaktifkan pengiriman notifikasi ke guru dan laporan rekapitulasi guru Alpa ke Admin.')
                    ->schema([
                        Toggle::make('notif_wa_guru_masuk')
                            ->label('Notifikasi Datang Guru')
                            ->helperText('Kirim pesan WA ke nomor guru saat berhasil scan absen datang (disertai status tepat waktu/terlambat).')
                            ->default(true),

                        Toggle::make('notif_wa_guru_pulang')
                            ->label('Notifikasi Pulang Guru')
                            ->helperText('Kirim pesan WA ke nomor guru saat berhasil scan absen pulang.')
                            ->default(true),

                        Toggle::make('report_guru_alpa')
                            ->label('Auto Report Guru Alpa')
                            ->helperText('Kirim laporan rekap guru Alpa harian ke WhatsApp Admin sekolah.')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token')
                    ->label('API Token Sidobe')
                    ->formatStateUsing(fn ($state) => '••••••••••••' . substr($state, -5)) // Sembunyikan sebagian token di tabel
                    ->placeholder('Belum dikonfigurasi'),

                ToggleColumn::make('notif_wa_wali_masuk')
                    ->label('Wali Masuk'),

                ToggleColumn::make('notif_wa_wali_pulang')
                    ->label('Wali Pulang'),

                ToggleColumn::make('notif_wa_wali_alpa')
                    ->label('Wali Alpa'),

                ToggleColumn::make('notif_wa_guru_masuk')
                    ->label('Guru Masuk'),

                ToggleColumn::make('notif_wa_guru_pulang')
                    ->label('Guru Pulang'),

                ToggleColumn::make('report_guru_alpa')
                    ->label('Report Guru Alpa'),

                ToggleColumn::make('notif_wa_admin_rekap_alpa')
                    ->label('Rekap Siswa'),

                TextColumn::make('nomor_wa_rekap_alpa')
                    ->label('Penerima Rekap Alpa')
                    ->placeholder('Guru Super Admin')
                    ->limit(25),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWaSettings::route('/'),
        ];
    }
}
