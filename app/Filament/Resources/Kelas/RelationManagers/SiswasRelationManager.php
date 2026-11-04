<?php

namespace App\Filament\Resources\Kelas\RelationManagers;

use App\Models\Absensi;
use App\Models\Siswa;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema; // Gunakan Schema standar Filament v5
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class SiswasRelationManager extends RelationManager
{
    // Nama relasi yang didefinisikan di model Kelas (public function siswas())
    protected static string $relationship = 'siswas';

    protected static ?string $title = 'Daftar Siswa di Kelas Ini';

    // Form input ketika kita menambah atau mengedit siswa langsung dari dalam kelas
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nis')
                    ->label('NIS / No. Kartu')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap Siswa')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    // Tabel siswa yang berada di dalam kelas tersebut
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->description(function (Siswa $record) {
                        // Tampilkan kelas siswa saat ini di bawah nama siswa
                        return $record->nis ?? 'Belum ada NIS';
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->state(function (Siswa $record, $livewire) {
                        $rawDate = $livewire->getTableFilterState('tanggal_absen')['tanggal'] ?? null;
                        $selectedDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->toDateString() : now()->toDateString();
                        $absensi = Absensi::where('tanggal', $selectedDate)
                            ->where('absensable_type', Siswa::class)
                            ->where('absensable_id', $record->id)
                            ->first();
                        return $absensi?->jam_masuk;
                    })
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->alignCenter(),

                // KOLOM STATUS ABSENSI HARIAN DINAMIS (Diformat Tanggal Murni)
                Tables\Columns\TextColumn::make('status_absen')
                    ->label('Status Absensi')
                    ->state(function (Siswa $record, $livewire) {
                        // 1. Ambil tanggal mentah dari filter kalender
                        $rawDate = $livewire->getTableFilterState('tanggal_absen')['tanggal'] ?? null;

                        // 2. LOGIKA SAKTI: Paksa potong dan format menjadi YYYY-MM-DD menggunakan Carbon
                        // Ini akan membuang informasi Jam, Menit, Detik, dan Timezone secara total
                        $selectedDate = $rawDate
                            ? \Carbon\Carbon::parse($rawDate)->toDateString()
                            : now()->toDateString();

                        // 3. Cari catatan absensi siswa ini di tanggal terpilih (Format dijamin COCOK)
                        $absensi = Absensi::where('tanggal', $selectedDate)
                            ->where('absensable_type', Siswa::class)
                            ->where('absensable_id', $record->id)
                            ->first();

                        return $absensi ? $absensi->status_masuk : 'Belum Absen';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tepat Waktu' => 'success', // Hijau
                        'Terlambat' => 'warning',   // Kuning/Oranye
                        'Sakit' => 'info',          // Biru
                        'Izin' => 'gray',           // Abu-abu
                        'Alpa' => 'danger',         // Merah
                        default => 'gray',          // Belum Absen
                    })
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal_absen')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Pilih Tanggal Absen')
                            ->default(now()->toDateString()) // Default Hari Ini
                            ->native(false), // Menggunakan kalender interaktif Filament
                    ])
                    ->query(function ($query) {
                        // Kita kosongi kuerinya agar sistem tidak memfilter baris siswa
                        // Kita ingin seluruh siswa di kelas ini tetap tampil di tabel
                        return $query;
                    })
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                // Memungkinkan admin menambah siswa baru langsung ke dalam kelas ini
                CreateAction::make()
                    ->label('Tambah Siswa ke Kelas Ini')
                    ->visible(fn () => !auth()->user()?->hasAnyRole(['wali_kelas', 'Wali Kelas'])),
            ])
            ->actions([
                Tables\Actions\Action::make('chat_wa')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->tooltip('Kirim pesan WhatsApp ke nomor wali murid')
                    ->visible(fn (Siswa $record) => !empty($record->nomor_wa_wali))
                    ->url(function (Siswa $record, $livewire) {
                        $rawPhone = $record->nomor_wa_wali ?? '';
                        $phone = preg_replace('/[^0-9]/', '', $rawPhone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $rawDate = $livewire->getTableFilterState('tanggal_absen')['tanggal'] ?? null;
                        $selectedDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->toDateString() : now()->toDateString();
                        $absensi = Absensi::where('tanggal', $selectedDate)
                            ->where('absensable_type', Siswa::class)
                            ->where('absensable_id', $record->id)
                            ->first();
                        $tgl = \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y');
                        $status = $absensi?->status_masuk ?? 'Belum Absen';
                        $jam = $absensi?->jam_masuk ? " (Jam Masuk: {$absensi->jam_masuk})" : '';

                        $pesan = "Assalamu'alaikum Wr. Wb. Yth. Bapak/Ibu Wali dari *{$record->nama}*.\n\n"
                            . "Kami dari pihak sekolah menginfokan bahwa ananda pada hari {$tgl} tercatat dengan status presensi: *{$status}*{$jam}.\n\n"
                            . "Salam hormat,\nWali Kelas";

                        return 'https://api.whatsapp.com/send?phone=' . $phone . '&text=' . rawurlencode($pesan);
                    }, shouldOpenInNewTab: true),

                EditAction::make()
                    ->visible(fn () => !auth()->user()?->hasAnyRole(['wali_kelas', 'Wali Kelas'])),
                DeleteAction::make()
                    ->visible(fn () => !auth()->user()?->hasAnyRole(['wali_kelas', 'Wali Kelas'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => !auth()->user()?->hasAnyRole(['wali_kelas', 'Wali Kelas'])),
            ]);
    }
}
