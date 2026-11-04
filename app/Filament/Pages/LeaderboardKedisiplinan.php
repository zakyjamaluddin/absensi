<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LeaderboardKedisiplinan extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Presensi & Kehadiran';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['staff_absensi', 'staff_perpustakaan', 'admin_perpustakaan'])) {
            return false;
        }

        return $user->hasRole('admin_absensi') || $user->can('View:LeaderboardKedisiplinan');
    }

    protected static ?string $title = 'Rating Kedisiplinan Siswa & Guru';
    protected static ?string $navigationLabel = 'Rating Kedisiplinan';

    protected string $view = 'filament.pages.leaderboard-kedisiplinan';

    public $tab = 'siswa'; // 'siswa' | 'guru'
    public $periode = 'bulan_ini'; // 'bulan_ini' | 'tahun_ini' | 'all' | 'custom'
    public $startDate = null;
    public $endDate = null;
    public $kelasId = 'all';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function setPeriode(string $periode): void
    {
        $this->periode = $periode;
    }

    public function setKelas(string $kelasId): void
    {
        $this->kelasId = $kelasId;
    }

    public function getRatingData(): array
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();
        $endOfYear = now()->endOfYear()->toDateString();

        $items = [];

        if ($this->tab === 'siswa') {
            $siswaQuery = Siswa::with('kelas');
            if ($this->kelasId !== 'all') {
                $siswaQuery->where('kelas_id', $this->kelasId);
            }
            $subjects = $siswaQuery->get();
            $subjectType = Siswa::class;
        } else {
            $subjects = Guru::all();
            $subjectType = Guru::class;
        }

        foreach ($subjects as $subject) {
            $absensiQuery = Absensi::where('absensable_type', $subjectType)
                ->where('absensable_id', $subject->id);

            if ($this->periode === 'bulan_ini') {
                $absensiQuery->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
            } elseif ($this->periode === 'tahun_ini') {
                $absensiQuery->whereBetween('tanggal', [$startOfYear, $endOfYear]);
            } elseif ($this->periode === 'custom' && $this->startDate && $this->endDate) {
                $absensiQuery->whereBetween('tanggal', [$this->startDate, $this->endDate]);
            } elseif ($this->periode === 'custom' && $this->startDate) {
                $absensiQuery->whereDate('tanggal', '>=', $this->startDate);
            } elseif ($this->periode === 'custom' && $this->endDate) {
                $absensiQuery->whereDate('tanggal', '<=', $this->endDate);
            }

            $records = $absensiQuery->get();

            $jamSetting = \App\Models\JamSetting::getSetting();
            $batasTerlambatStr = $jamSetting?->batas_terlambat ?? '07:00:00';
            $batasAwalStr = Carbon::parse($batasTerlambatStr)->subMinutes(15)->format('H:i:s');

            $tepatWaktuRecords = $records->where('status_masuk', 'Tepat Waktu');
            $tepatWaktu = $tepatWaktuRecords->count();
            
            // Hitung siswa yang hadir 15 menit atau lebih awal sebelum batas terlambat (+12 Poin)
            $lebihAwal = 0;
            $tepatWaktuBiasa = 0;
            foreach ($tepatWaktuRecords as $tRec) {
                if ($tRec->jam_masuk && Carbon::parse($tRec->jam_masuk)->format('H:i:s') <= $batasAwalStr) {
                    $lebihAwal++;
                } else {
                    $tepatWaktuBiasa++;
                }
            }

            $terlambat = $records->where('status_masuk', 'Terlambat')->count();
            $sakit = $records->where('status_masuk', 'Sakit')->count();
            $izin = $records->where('status_masuk', 'Izin')->count();
            $alpa = $records->where('status_masuk', 'Alpa')->count();

            // RUMUS POIN KEDISIPLINAN:
            // Hadir Sangat Pagi (>= 15 Menit Sebelum Batas Terlambat): +12 Poin
            // Hadir Tepat Waktu Standar: +10 Poin
            // Terlambat: +3 Poin
            // Sakit: 0 Poin
            // Izin: 0 Poin
            // Alpa: -10 Poin
            $skor = ($lebihAwal * 12) + ($tepatWaktuBiasa * 10) + ($terlambat * 3) - ($alpa * 10);

            $totalHadir = $tepatWaktu + $terlambat;
            $totalHari = $tepatWaktu + $terlambat + $sakit + $izin + $alpa;
            $persentase = $totalHari > 0 ? round(($totalHadir / $totalHari) * 100, 1) : 0;

            // Predikat / Badge Kedisiplinan
            if ($totalHari === 0) {
                $badge = 'Belum Ada Data';
                $badgeColor = '#6b7280';
            } elseif ($skor >= 80 && $alpa === 0) {
                $badge = '🥇 Teladan Utama';
                $badgeColor = '#059669';
            } elseif ($skor >= 40 && $alpa <= 1) {
                $badge = '✨ Sangat Disiplin';
                $badgeColor = '#2563eb';
            } elseif ($skor >= 10 && $alpa <= 2) {
                $badge = '👍 Cukup Disiplin';
                $badgeColor = '#d97706';
            } else {
                $badge = '⚠️ Perlu Pembinaan';
                $badgeColor = '#dc2626';
            }

            $items[] = [
                'id' => $subject->id,
                'nama' => $subject->nama,
                'nomor_induk' => $subject instanceof Siswa ? ($subject->nis ?? '-') : ($subject->nip ?? '-'),
                'nomor_induk_label' => $subject instanceof Siswa ? 'NIS' : 'NIP',
                'kelas' => $subject instanceof Siswa ? ($subject->kelas?->nama_kelas ?? 'Tanpa Kelas') : 'Ustadz / Guru',
                'foto' => !empty($subject->foto_url) ? $subject->foto_url : null,
                'tepat_waktu' => $tepatWaktu,
                'lebih_awal' => $lebihAwal,
                'terlambat' => $terlambat,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'total_hadir' => $totalHadir,
                'total_hari' => $totalHari,
                'persentase' => $persentase,
                'skor' => $skor,
                'badge' => $badge,
                'badge_color' => $badgeColor,
            ];
        }

        // Urutkan dari skor tertinggi, lalu tepat waktu terbanyak, lalu alpa tersedikit
        usort($items, function ($a, $b) {
            if ($b['skor'] === $a['skor']) {
                if ($b['tepat_waktu'] === $a['tepat_waktu']) {
                    return $a['alpa'] <=> $b['alpa'];
                }
                return $b['tepat_waktu'] <=> $a['tepat_waktu'];
            }
            return $b['skor'] <=> $a['skor'];
        });

        // Beri nomor peringkat
        $rank = 1;
        foreach ($items as $idx => $item) {
            $items[$idx]['rank'] = $rank++;
        }

        // Ambil Top 3 Terbaik
        $top3 = array_slice($items, 0, 3);

        // Ambil 3 Terendah (Bottom 3) jika jumlah data > 3
        $bottom3 = [];
        if (count($items) > 3) {
            // Ambil 3 elemen terakhir
            $reversed = array_reverse(array_slice($items, -3));
            $bottom3 = $reversed;
        }

        return [
            'all' => $items,
            'top3' => $top3,
            'bottom3' => $bottom3,
        ];
    }

    public function getKelases(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }
}
