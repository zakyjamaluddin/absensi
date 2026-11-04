<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalGuruPiket;
use App\Models\JamSetting;
use App\Models\PeminjamanBuku;
use App\Models\PengajuanIzin;
use App\Models\Siswa;
use App\Models\WaSetting;
use Carbon\Carbon;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    /**
     * Memeriksa dan memicu notifikasi sesuai 5 skenario kebutuhan sistem
     */
    public function checkPendingNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['notifications' => []]);
        }

        $now = Carbon::now();
        $today = $now->toDateString();
        $jamSetting = JamSetting::getSetting();

        $notifications = [];

        // -------------------------------------------------------------
        // TRIGGER 1: GURU / SISWA (5 menit sebelum batas waktu tepat waktu)
        // -------------------------------------------------------------
        if ($user->userable_id && $jamSetting && $jamSetting->batas_terlambat) {
            $batasTepat = Carbon::parse($today . ' ' . $jamSetting->batas_terlambat);
            $warningStart = $batasTepat->copy()->subMinutes(5);

            // Jika waktu saat ini antara 5 menit sebelum batas s/d batas terlambat
            if ($now->between($warningStart, $batasTepat)) {
                $cacheKey = "notif_t1_{$user->id}_{$today}";
                if (!Cache::has($cacheKey)) {
                    $isSiswa = $user->userable instanceof Siswa;
                    $isGuru = $user->userable instanceof Guru;

                    if ($isSiswa || $isGuru) {
                        $modelClass = $isSiswa ? Siswa::class : Guru::class;

                        // Cek apakah sudah absen masuk
                        $sudahAbsen = Absensi::where('absensable_type', $modelClass)
                            ->where('absensable_id', $user->userable_id)
                            ->where('tanggal', $today)
                            ->whereNotNull('jam_masuk')
                            ->exists();

                        // Cek apakah ada pengajuan izin/sakit
                        $adaIzin = PengajuanIzin::where('izinable_type', $modelClass)
                            ->where('izinable_id', $user->userable_id)
                            ->whereDate('tanggal_mulai', '<=', $today)
                            ->whereDate('tanggal_selesai', '>=', $today)
                            ->whereIn('status', ['Menunggu', 'Disetujui'])
                            ->exists();

                        if (!$sudahAbsen && !$adaIzin) {
                            $diffMinutes = max(1, (int) $now->diffInMinutes($batasTepat, false));
                            $item = [
                                'id'    => 't1_' . time(),
                                'title' => '⏰ Peringatan Batas Absensi',
                                'body'  => "Batas waktu absensi tepat waktu berakhir dalam {$diffMinutes} menit (pukul " . substr($jamSetting->batas_terlambat, 0, 5) . "). Segera lakukan presensi!",
                                'url'   => url('/admin/scan-absensi'),
                            ];
                            $notifications[] = $item;
                            $this->sendFilamentDatabaseNotification($user, $item['title'], $item['body'], 'warning');
                            Cache::put($cacheKey, true, 3600); // Jangan kirim ulang dalam 1 jam
                        }
                    }
                }
            }
        }

        // -------------------------------------------------------------
        // TRIGGER 2: GURU PIKET (ketika batas akhir masuk berakhir)
        // -------------------------------------------------------------
        $isPiketHariIni = JadwalGuruPiket::isUserPiketToday($user);
        if ($isPiketHariIni && $jamSetting && $jamSetting->selesai_masuk) {
            $selesaiMasuk = Carbon::parse($today . ' ' . $jamSetting->selesai_masuk);

            if ($now->greaterThanOrEqualTo($selesaiMasuk)) {
                $cacheKey = "notif_t2_piket_{$user->id}_{$today}";
                if (!Cache::has($cacheKey)) {
                    // Pastikan record harian sudah di-generate
                    Absensi::ensureDailyRecordsForDate($today);

                    $belumAbsenCount = Absensi::where('absensable_type', Siswa::class)
                        ->where('tanggal', $today)
                        ->where(function ($q) {
                            $q->whereNull('status_masuk')
                              ->orWhere('status_masuk', 'Belum Absen');
                        })
                        ->count();

                    $waSetting = WaSetting::getSetting();
                    $isAutoAlpa = $waSetting && $waSetting->is_alpa_otomatis;

                    if ($isAutoAlpa) {
                        $alpaCount = Absensi::where('absensable_type', Siswa::class)
                            ->where('tanggal', $today)
                            ->where('status_masuk', 'Alpa')
                            ->count();

                        $item = [
                            'id'    => 't2_' . time(),
                            'title' => '🤖 Rekap Alpa Otomatis Hari Ini',
                            'body'  => "Batas waktu masuk telah selesai. Sebanyak {$alpaCount} siswa telah otomatis ditandai Alpa oleh sistem.",
                            'url'   => url('/admin/absensis'),
                        ];
                    } else {
                        $item = [
                            'id'    => 't2_' . time(),
                            'title' => '📋 Pengingat Guru Piket (Proses Alpa)',
                            'body'  => "Batas akhir masuk telah berakhir. Terdapat {$belumAbsenCount} siswa belum presensi. Silakan segera memproses status Alpa hari ini.",
                            'url'   => url('/admin/absensis'),
                        ];
                    }

                    $notifications[] = $item;
                    $this->sendFilamentDatabaseNotification($user, $item['title'], $item['body'], 'info');
                    Cache::put($cacheKey, true, 43200); // 12 jam
                }
            }
        }

        // -------------------------------------------------------------
        // TRIGGER 3: GURU PIKET & ADMIN (ketika ada guru/siswa mengajukan izin)
        // -------------------------------------------------------------
        $canReviewIzin = $isPiketHariIni || $user->hasAnyRole(['super_admin', 'admin_absensi', 'staff_absensi']);
        if ($canReviewIzin) {
            $pendingIzinCount = PengajuanIzin::where('status', 'Menunggu')->count();

            if ($pendingIzinCount > 0) {
                $cacheKey = "notif_t3_izin_count_{$user->id}_{$pendingIzinCount}";
                if (!Cache::has($cacheKey)) {
                    $item = [
                        'id'    => 't3_' . time(),
                        'title' => '📩 Pengajuan Izin Menunggu Persetujuan',
                        'body'  => "Terdapat {$pendingIzinCount} pengajuan izin siswa/guru yang memerlukan persetujuan hari ini.",
                        'url'   => url('/admin/pengajuan-izins'),
                    ];
                    $notifications[] = $item;
                    $this->sendFilamentDatabaseNotification($user, $item['title'], $item['body'], 'info');
                    Cache::put($cacheKey, true, 900); // 15 menit
                }
            }
        }

        // -------------------------------------------------------------
        // TRIGGER 4: ADMIN & STAFF PERPUSTAKAAN (buku jatuh tempo hari ini)
        // -------------------------------------------------------------
        if ($user->hasAnyRole(['admin_perpustakaan', 'staff_perpustakaan', 'super_admin'])) {
            $cacheKey = "notif_t4_perpus_{$user->id}_{$today}";
            if (!Cache::has($cacheKey)) {
                $bukuJatuhTempo = PeminjamanBuku::where('status', 'Dipinjam')
                    ->whereDate('tanggal_kembali', $today)
                    ->count();

                if ($bukuJatuhTempo > 0) {
                    $item = [
                        'id'    => 't4_' . time(),
                        'title' => '📚 Buku Jatuh Tempo Hari Ini',
                        'body'  => "Terdapat {$bukuJatuhTempo} buku pinjaman yang jatuh tempo pengembalian hari ini. Mohon lakukan pengecekan.",
                        'url'   => url('/admin/peminjaman-bukus'),
                    ];
                    $notifications[] = $item;
                    $this->sendFilamentDatabaseNotification($user, $item['title'], $item['body'], 'warning');
                    Cache::put($cacheKey, true, 43200);
                }
            }
        }

        // -------------------------------------------------------------
        // TRIGGER 5: WALI KELAS (5 menit setelah batas akhir masuk berakhir)
        // -------------------------------------------------------------
        if ($user->hasAnyRole(['wali_kelas', 'Wali Kelas']) && $user->userable instanceof Guru && $jamSetting && $jamSetting->selesai_masuk) {
            $selesaiMasuk = Carbon::parse($today . ' ' . $jamSetting->selesai_masuk);
            $fiveMinutesAfter = $selesaiMasuk->copy()->addMinutes(5);

            if ($now->greaterThanOrEqualTo($fiveMinutesAfter)) {
                $cacheKey = "notif_t5_walikelas_{$user->id}_{$today}";
                if (!Cache::has($cacheKey)) {
                    $guru = $user->userable;
                    $kelas = $guru->kelas;

                    if ($kelas) {
                        $siswaIds = Siswa::where('kelas_id', $kelas->id)->pluck('id');
                        $absensis = Absensi::where('absensable_type', Siswa::class)
                            ->whereIn('absensable_id', $siswaIds)
                            ->where('tanggal', $today)
                            ->get();

                        $tepat = $absensis->where('status_masuk', 'Tepat Waktu')->count();
                        $telat = $absensis->where('status_masuk', 'Terlambat')->count();
                        $sakit = $absensis->where('status_masuk', 'Sakit')->count();
                        $izin = $absensis->where('status_masuk', 'Izin')->count();
                        $alpa = $absensis->where('status_masuk', 'Alpa')->count();
                        $belum = $absensis->whereIn('status_masuk', ['Belum Absen', null])->count();

                        $item = [
                            'id'    => 't5_' . time(),
                            'title' => "📊 Rekap Kehadiran {$kelas->nama_kelas}",
                            'body'  => "Laporan: {$tepat} tepat waktu, {$telat} telat, {$sakit} sakit, {$izin} izin, {$alpa} alpa, {$belum} belum absen.",
                            'url'   => url('/admin/presensi-kelas-saya'),
                        ];
                        $notifications[] = $item;
                        $this->sendFilamentDatabaseNotification($user, $item['title'], $item['body'], 'info');
                        Cache::put($cacheKey, true, 43200);
                    }
                }
            }
        }

        // -------------------------------------------------------------
        // TRIGGER 6: SUPER ADMIN & ADMIN ABSENSI (H-3 Hari Libur Nasional)
        // -------------------------------------------------------------
        if ($user->hasAnyRole(['super_admin', 'admin_absensi'])) {
            $h3Date = Carbon::today()->addDays(3)->toDateString();
            $h3Holiday = \App\Models\HariLibur::whereDate('tanggal', $h3Date)->first();

            if ($h3Holiday) {
                $cacheKey = "notif_t6_libur_h3_{$user->id}_{$h3Holiday->id}_{$today}";
                if (!Cache::has($cacheKey)) {
                    $tglIndo = Carbon::parse($h3Date)->translatedFormat('d F Y');
                    $item = [
                        'id'    => 't6_' . time(),
                        'title' => '🗓️ Pengingat Hari Libur (H-3)',
                        'body'  => "Tanggal {$tglIndo} adalah {$h3Holiday->keterangan}. Sistem akan menetapkan tanggal tersebut sebagai hari libur sekolah. Klik untuk mengubah.",
                        'url'   => url('/admin/hari-liburs'),
                    ];
                    $notifications[] = $item;
                    $this->sendFilamentDatabaseNotification(
                        $user,
                        $item['title'],
                        $item['body'],
                        'info',
                        url('/admin/hari-liburs'),
                        'Ubah / Tinjau'
                    );
                    Cache::put($cacheKey, true, 43200); // 12 jam
                }
            }
        }

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    /**
     * Kirim notifikasi database Filament (lonceng topbar)
     */
    protected function sendFilamentDatabaseNotification($user, string $title, string $body, string $status = 'info', ?string $actionUrl = null, ?string $actionLabel = null): void
    {
        try {
            $notif = FilamentNotification::make()
                ->title($title)
                ->body($body);

            if ($actionUrl && $actionLabel) {
                $notif->actions([
                    \Filament\Notifications\Actions\Action::make('view_action')
                        ->label($actionLabel)
                        ->url($actionUrl),
                ]);
            }

            if ($status === 'warning') {
                $notif->warning();
            } elseif ($status === 'danger') {
                $notif->danger();
            } elseif ($status === 'success') {
                $notif->success();
            } else {
                $notif->info();
            }

            $notif->sendToDatabase($user);
        } catch (\Throwable $e) {
            // Ignore if notifications table not accessible
        }
    }
}
