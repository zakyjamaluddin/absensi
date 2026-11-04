<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Absensi;
use App\Models\JamSetting;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;


class ScanAbsensi extends Page
{


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;
    protected static ?string $navigationLabel = 'Scanner Presensi';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['admin_perpustakaan', 'staff_perpustakaan'])) {
            return false;
        }

        return $user->hasAnyRole(['admin_absensi', 'staff_absensi']) || $user->can('View:ScanAbsensi');
    }

    protected string $view = 'filament.pages.scan-absensi';

    protected static ?string $title = 'Scanner Absensi';

    public $scannedCode = ''; // Menampung NIS atau NIP
    public $message = '';
    public $status = 'info'; // 'success', 'danger', 'info'
    public ?array $lastScanned = null;

    protected function setLastScanned($subject, string $tipeAbsen, string $statusKehadiran, string $statusBadge, ?string $jam = null): void
    {
        $now = Carbon::now();
        if ($subject instanceof Siswa) {
            $subject->loadMissing('kelas');
            $this->lastScanned = [
                'nama' => $subject->nama,
                'tipe_label' => 'Siswa',
                'nomor_induk_label' => 'NIS',
                'nomor_induk' => $subject->nis,
                'kelas' => $subject->kelas?->nama_kelas ?? 'Tanpa Kelas',
                'foto' => $subject->foto_url,
                'tipe_absen' => $tipeAbsen,
                'status_kehadiran' => $statusKehadiran,
                'status_badge' => $statusBadge,
                'jam' => $jam ?: $now->format('H:i:s'),
                'tanggal' => $now->translatedFormat('d F Y'),
            ];
        } elseif ($subject instanceof Guru) {
            $this->lastScanned = [
                'nama' => $subject->nama,
                'tipe_label' => 'Guru / Pendidik',
                'nomor_induk_label' => 'NIP',
                'nomor_induk' => $subject->nip ?: '-',
                'kelas' => 'Tenaga Pengajar',
                'foto' => $subject->foto_url,
                'tipe_absen' => $tipeAbsen,
                'status_kehadiran' => $statusKehadiran,
                'status_badge' => $statusBadge,
                'jam' => $jam ?: $now->format('H:i:s'),
                'tanggal' => $now->translatedFormat('d F Y'),
            ];
        }
    }

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


    public function processScan(?string $inputCode = null)
    {
        $code = trim($inputCode ?: $this->scannedCode);
        $this->scannedCode = '';

        if (empty($code)) {
            return;
        }

        // 1. Cari subjek secara cepat dengan eager-loading relasi kelas
        $subject = Siswa::with('kelas')->where('nis', $code)->first() ?? Guru::where('nip', $code)->first();

        if (!$subject) {
            $this->lastScanned = null;
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
            $this->lastScanned = null;
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
            $this->setLastScanned($subject, 'DILUAR JADWAL', 'Diluar Jadwal', 'danger', $now->format('H:i:s'));
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
                $this->setLastScanned($subject, 'MASUK', 'Sudah Absen Masuk', 'info', $absensi->jam_masuk);
                $this->message = "{$subject->nama} sudah melakukan absen MASUK hari ini.";
                $this->status = 'info';

                // TEMBAKKAN SUARA PERINGATAN (SUDAH ABSEN)
                $this->dispatch('play-sound', type: 'info');
                return;
            }

            // Tentukan batas waktu acuan keterlambatan & status kehadiran
            $batasTerlambatAcuan = null;
            if ($subject instanceof Siswa) {
                $batasTerlambatAcuan = $settings->batas_terlambat;
                $statusKehadiran = ($timeNow <= Carbon::parse($batasTerlambatAcuan)->format('H:i:s')) ? 'Tepat Waktu' : 'Terlambat';
            } elseif ($subject instanceof Guru) {
                if ($settings->mode_absensi_guru === 'jadwal') {
                    // Mode dinamis: cek jadwal mengajar hari ini
                    $jadwalHariIni = $subject->getJadwalHariIni($today);
                    if ($jadwalHariIni->isNotEmpty()) {
                        if ($settings->aturan_terlambat_guru === 'jadwal_mengajar') {
                            $earliestJamMulai = $subject->getEarliestJamMulai($today);
                            $batasTerlambatAcuan = $earliestJamMulai ?: ($settings->batas_terlambat_guru ?: $settings->batas_terlambat);
                            $statusKehadiran = ($timeNow <= Carbon::parse($batasTerlambatAcuan)->format('H:i:s')) ? 'Tepat Waktu' : 'Terlambat';
                        } else {
                            $batasTerlambatAcuan = $settings->batas_terlambat_guru ?: $settings->batas_terlambat;
                            $statusKehadiran = ($timeNow <= Carbon::parse($batasTerlambatAcuan)->format('H:i:s')) ? 'Tepat Waktu' : 'Terlambat';
                        }
                    } else {
                        // Guru tidak ada jadwal mengajar hari ini: jika hadir tetap dihitung Hadir (Tepat Waktu)
                        $statusKehadiran = 'Tepat Waktu';
                        $batasTerlambatAcuan = null;
                    }
                } else {
                    // Mode statis
                    $batasTerlambatAcuan = $settings->batas_terlambat_guru ?: $settings->batas_terlambat;
                    $statusKehadiran = ($timeNow <= Carbon::parse($batasTerlambatAcuan)->format('H:i:s')) ? 'Tepat Waktu' : 'Terlambat';
                }
            } else {
                $statusKehadiran = ($timeNow <= Carbon::parse($settings->batas_terlambat)->format('H:i:s')) ? 'Tepat Waktu' : 'Terlambat';
            }

            $absensiRecord = Absensi::updateOrCreate(
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

            // JIKA SUBJEK ADALAH SISWA / GURU: Kirim notifikasi WA secara deferred (asynchronous tanpa memblokir latensi scanner)
            if ($subject instanceof Siswa) {
                defer(fn () => WhatsAppService::sendNotifMasukWali($subject, $absensiRecord, $settings));
            } elseif ($subject instanceof Guru) {
                defer(fn () => WhatsAppService::sendNotifMasukGuru($subject, $absensiRecord, $batasTerlambatAcuan));
            }

            $statusBadge = ($statusKehadiran === 'Terlambat') ? 'danger' : 'success';
            $this->setLastScanned($subject, 'MASUK', $statusKehadiran, $statusBadge, $timeNow);

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
                $this->setLastScanned($subject, 'PULANG', 'Sudah Absen Pulang', 'info', $absensi->jam_pulang);
                $this->message = "{$subject->nama} sudah melakukan absen PULANG hari ini.";
                $this->status = 'info';

                // TEMBAKKAN SUARA PERINGATAN (SUDAH ABSEN)
                $this->dispatch('play-sound', type: 'info');
                return;
            }

            $absensiRecord = Absensi::updateOrCreate(
                [
                    'tanggal' => $today,
                    'absensable_type' => get_class($subject),
                    'absensable_id' => $subject->id,
                ],
                [
                    'jam_pulang' => $timeNow,
                    'status_pulang' => 'Pulang',
                ]
            );

            // JIKA SUBJEK ADALAH SISWA / GURU: Kirim notifikasi WA secara deferred (non-blocking)
            if ($subject instanceof Siswa) {
                defer(fn () => WhatsAppService::sendNotifPulangWali($subject, $absensiRecord));
            } elseif ($subject instanceof Guru) {
                defer(fn () => WhatsAppService::sendNotifPulangGuru($subject, $absensiRecord));
            }

            $this->setLastScanned($subject, 'PULANG', 'Pulang', 'success', $timeNow);
            $this->message = "Berhasil Absen PULANG: {$subject->nama} (Pukul {$now->format('H:i')}).";
            $this->status = 'success';

            // TEMBAKKAN SUARA SUKSES
            $this->dispatch('play-sound', type: 'success');
        }
    }
}
