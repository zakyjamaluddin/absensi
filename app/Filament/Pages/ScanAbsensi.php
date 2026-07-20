<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Absensi;
use App\Models\JamSetting;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;


class ScanAbsensi extends Page
{


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    // UBAH BARIS INI: Sesuaikan tipenya agar sama persis dengan kelas induk Page

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.scan-absensi';

    protected static ?string $title = 'Scanner Absensi';

    public $scannedCode = ''; // Menampung NIS atau NIP
    public $message = '';
    public $status = 'info'; // 'success', 'danger', 'info'

    // public function processScan()
    // {
    //     if (empty($this->scannedCode)) {
    //         return;
    //     }

    //     $code = trim($this->scannedCode);
    //     $this->scannedCode = ''; // Kosongkan input agar siap scan berikutnya

    //     // 1. Cari subjek (Siswa atau Guru)
    //     $subject = Siswa::where('nis', $code)->first() ?? Guru::where('nip', $code)->first();

    //     if (!$subject) {
    //         $this->message = "ID/Nomor Kartu '{$code}' tidak terdaftar.";
    //         $this->status = 'danger';
    //         return;
    //     }

    //     $now = Carbon::now();
    //     $today = $now->toDateString();
    //     $timeNow = $now->toTimeString();

    //     // 2. Ambil Config Jam Aktif
    //     $settings = JamSetting::first();
    //     if (!$settings) {
    //         $this->message = "Pengaturan jam absensi belum dikonfigurasi oleh Admin.";
    //         $this->status = 'danger';
    //         return;
    //     }

    //     // 3. Tentukan tipe absensi berdasarkan jendela waktu
    //     $isMasukWindow = ($timeNow >= $settings->mulai_masuk && $timeNow <= $settings->selesai_masuk);
    //     $isPulangWindow = ($timeNow >= $settings->mulai_pulang && $timeNow <= $settings->selesai_pulang);

    //     if (!$isMasukWindow && !$isPulangWindow) {
    //         $this->message = "Tidak ada jadwal absen aktif saat ini (Pukul {$now->format('H:i')}).";
    //         $this->status = 'danger';
    //         return;
    //     }

    //     // Cari data absen hari ini untuk subjek tersebut
    //     $absensi = Absensi::where('tanggal', $today)
    //         ->where('absensable_type', get_class($subject))
    //         ->where('absensable_id', $subject->id)
    //         ->first();

    //     if ($isMasukWindow) {
    //         if ($absensi && $absensi->jam_masuk) {
    //             $this->message = "{$subject->nama} sudah melakukan absen MASUK hari ini.";
    //             $this->status = 'info';
    //             return;
    //         }

    //         // TENTUKAN STATUS KEHADIRAN (Tepat Waktu atau Terlambat)
    //         $statusKehadiran = ($timeNow <= $settings->batas_terlambat) ? 'Tepat Waktu' : 'Terlambat';
    //         // dd($statusKehadiran);

    //         Absensi::updateOrCreate(
    //             [
    //                 'tanggal' => $today,
    //                 'absensable_type' => get_class($subject),
    //                 'absensable_id' => $subject->id,
    //             ],
    //             [
    //                 'jam_masuk' => $timeNow,
    //                 'status_masuk' => $statusKehadiran, // <--- Simpan status ke DB
    //             ]
    //         );

    //         // Tampilkan pesan sukses dengan status keterlambatan di layar scan
    //         if ($statusKehadiran === 'Terlambat') {
    //             $this->message = "Berhasil Absen MASUK (TERLAMBAT): {$subject->nama} (Pukul {$now->format('H:i')}).";
    //             $this->status = 'danger'; // Ubah warna notifikasi menjadi Merah jika terlambat
    //         } else {
    //             $this->message = "Berhasil Absen MASUK (Tepat Waktu): {$subject->nama} (Pukul {$now->format('H:i')}).";
    //             $this->status = 'success'; // Warna Hijau jika tepat waktu
    //         }
    //     }

    //     elseif ($isPulangWindow) {
    //         if ($absensi && $absensi->jam_pulang) {
    //             $this->message = "{$subject->nama} sudah melakukan absen PULANG hari ini.";
    //             $this->status = 'info';
    //             return;
    //         }

    //         Absensi::updateOrCreate(
    //             [
    //                 'tanggal' => $today,
    //                 'absensable_type' => get_class($subject),
    //                 'absensable_id' => $subject->id,
    //             ],
    //             ['jam_pulang' => $timeNow]
    //         );

    //         $this->message = "Berhasil Absen PULANG: {$subject->nama} (Pukul {$now->format('H:i')}).";
    //         $this->status = 'success';
    //     }
    // }


    public function processScan()
    {
        if (empty($this->scannedCode)) {
            return;
        }

        $code = trim($this->scannedCode);
        $this->scannedCode = '';

        // 1. Cari subjek (Siswa atau Guru)
        $subject = Siswa::where('nis', $code)->first() ?? Guru::where('nip', $code)->first();

        if (!$subject) {
            $this->message = "ID/Nomor Kartu '{$code}' tidak terdaftar.";
            $this->status = 'danger';

            // TEMBAKKAN SUARA ERROR/GAGAL
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        $now = Carbon::now();
        $today = $now->toDateString();
        $timeNow = $now->toTimeString();

        // 2. Ambil Config Jam Aktif
        $settings = JamSetting::first();
        if (!$settings) {
            $this->message = "Pengaturan jam absensi belum dikonfigurasi oleh Admin.";
            $this->status = 'danger';

            // TEMBAKKAN SUARA ERROR
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        // 3. Tentukan tipe absensi berdasarkan jendela waktu
        $isMasukWindow = ($timeNow >= $settings->mulai_masuk && $timeNow <= $settings->selesai_masuk);
        $isPulangWindow = ($timeNow >= $settings->mulai_pulang && $timeNow <= $settings->selesai_pulang);

        if (!$isMasukWindow && !$isPulangWindow) {
            $this->message = "Tidak ada jadwal absen aktif saat ini (Pukul {$now->format('H:i')}).";
            $this->status = 'danger';

            // TEMBAKKAN SUARA ERROR
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        // Cari data absen hari ini untuk subjek tersebut
        $absensi = Absensi::where('tanggal', $today)
            ->where('absensable_type', get_class($subject))
            ->where('absensable_id', $subject->id)
            ->first();

        if ($isMasukWindow) {
            if ($absensi && $absensi->jam_masuk) {
                $this->message = "{$subject->nama} sudah melakukan absen MASUK hari ini.";
                $this->status = 'info';

                // TEMBAKKAN SUARA PERINGATAN (SUDAH ABSEN)
                $this->dispatch('play-sound', type: 'info');
                return;
            }

            // Tentukan apakah terlambat
            $statusKehadiran = ($timeNow <= $settings->batas_terlambat) ? 'Tepat Waktu' : 'Terlambat';

            Absensi::updateOrCreate(
                [
                    'tanggal' => $today,
                    'absensable_type' => get_class($subject),
                    'absensable_id' => $subject->id,
                ],
                [
                    'jam_masuk' => $timeNow,
                    'status_masuk' => $statusKehadiran
                ]
            );

            if ($statusKehadiran === 'Terlambat') {
                $this->message = "Berhasil Absen MASUK (TERLAMBAT): {$subject->nama} (Pukul {$now->format('H:i')}).";
                $this->status = 'danger'; // Merah di layar
            } else {
                $this->message = "Berhasil Absen MASUK (Tepat Waktu): {$subject->nama} (Pukul {$now->format('H:i')}).";
                $this->status = 'success'; // Hijau di layar
            }

            // KEDUA STATUS MASUK INI ADALAH BERHASIL (TEMBAKKAN SUARA SUKSES)
            $this->dispatch('play-sound', type: 'success');
        }

        elseif ($isPulangWindow) {
            if ($absensi && $absensi->jam_pulang) {
                $this->message = "{$subject->nama} sudah melakukan absen PULANG hari ini.";
                $this->status = 'info';

                // TEMBAKKAN SUARA PERINGATAN (SUDAH ABSEN)
                $this->dispatch('play-sound', type: 'info');
                return;
            }

            Absensi::updateOrCreate(
                [
                    'tanggal' => $today,
                    'absensable_type' => get_class($subject),
                    'absensable_id' => $subject->id,
                ],
                ['jam_pulang' => $timeNow]
            );

            $this->message = "Berhasil Absen PULANG: {$subject->nama} (Pukul {$now->format('H:i')}).";
            $this->status = 'success';

            // TEMBAKKAN SUARA SUKSES
            $this->dispatch('play-sound', type: 'success');
        }
    }
}
