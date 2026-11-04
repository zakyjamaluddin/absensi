<?php

namespace App\Filament\Pages;

use App\Models\Kelas;
use App\Models\PerpustakaanSetting;
use App\Models\Siswa;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LeaderboardLiterasi extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTrophy;
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['staff_perpustakaan', 'staff_absensi', 'admin_absensi'])) {
            return false;
        }

        return $user->hasRole('admin_perpustakaan') || $user->can('View:LeaderboardLiterasi');
    }

    protected static ?string $title = 'Rating Minat Literasi Siswa';
    protected static ?string $navigationLabel = 'Rating Minat Literasi';

    protected string $view = 'filament.pages.leaderboard-literasi';

    public $periode = 'bulan_ini'; // 'bulan_ini', 'tahun_ini', 'all', 'custom'
    public $startDate = null;
    public $endDate = null;
    public $kelasId = 'all';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function setPeriode(string $periode): void
    {
        $this->periode = $periode;
    }

    public function setKelas(string $kelasId): void
    {
        $this->kelasId = $kelasId;
    }

    public function getLeaderboardData(): array
    {
        $setting = PerpustakaanSetting::getSetting();
        $poinKunjungan = $setting->poin_kunjungan ?? 5;
        $poinPinjam = $setting->poin_peminjaman ?? 15;

        $siswaQuery = Siswa::with('kelas');

        if ($this->kelasId !== 'all') {
            $siswaQuery->where('kelas_id', $this->kelasId);
        }

        $allSiswa = $siswaQuery->get();
        $leaderboard = [];

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();
        $endOfYear = now()->endOfYear()->toDateString();

        foreach ($allSiswa as $siswa) {
            $kunjunganQuery = $siswa->kunjunganPerpustakaans();
            $pinjamQuery = $siswa->peminjamanBukus();

            if ($this->periode === 'bulan_ini') {
                $kunjunganQuery->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
                $pinjamQuery->whereBetween('tanggal_pinjam', [$startOfMonth, $endOfMonth]);
            } elseif ($this->periode === 'tahun_ini') {
                $kunjunganQuery->whereBetween('tanggal', [$startOfYear, $endOfYear]);
                $pinjamQuery->whereBetween('tanggal_pinjam', [$startOfYear, $endOfYear]);
            } elseif ($this->periode === 'custom' && $this->startDate && $this->endDate) {
                $kunjunganQuery->whereBetween('tanggal', [$this->startDate, $this->endDate]);
                $pinjamQuery->whereBetween('tanggal_pinjam', [$this->startDate, $this->endDate]);
            } elseif ($this->periode === 'custom' && $this->startDate) {
                $kunjunganQuery->whereDate('tanggal', '>=', $this->startDate);
                $pinjamQuery->whereDate('tanggal_pinjam', '>=', $this->startDate);
            } elseif ($this->periode === 'custom' && $this->endDate) {
                $kunjunganQuery->whereDate('tanggal', '<=', $this->endDate);
                $pinjamQuery->whereDate('tanggal_pinjam', '<=', $this->endDate);
            }

            $totalKunjungan = $kunjunganQuery->count();
            $totalPinjam = $pinjamQuery->count();
            $totalSkor = ($totalKunjungan * $poinKunjungan) + ($totalPinjam * $poinPinjam);

            // Tentukan lencana / predikat literasi
            $badge = 'Pembaca Aktif';
            $badgeColor = '#6b7280';
            if ($totalSkor >= 100) {
                $badge = '🌟 Duta Literasi';
                $badgeColor = '#d97706';
            } elseif ($totalSkor >= 50) {
                $badge = '📚 Pembaca Teladan';
                $badgeColor = '#2563eb';
            } elseif ($totalSkor >= 20) {
                $badge = '📖 Kutu Buku';
                $badgeColor = '#059669';
            }

            $leaderboard[] = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas?->nama_kelas ?? 'Tanpa Kelas',
                'total_kunjungan' => $totalKunjungan,
                'total_pinjam' => $totalPinjam,
                'total_skor' => $totalSkor,
                'badge' => $badge,
                'badge_color' => $badgeColor,
            ];
        }

        // Urutkan dari skor tertinggi
        usort($leaderboard, function ($a, $b) {
            if ($b['total_skor'] === $a['total_skor']) {
                return $b['total_pinjam'] <=> $a['total_pinjam'];
            }
            return $b['total_skor'] <=> $a['total_skor'];
        });

        // Tambahkan peringkat nomor urut
        $rank = 1;
        foreach ($leaderboard as $idx => $item) {
            $leaderboard[$idx]['rank'] = $rank++;
        }

        return $leaderboard;
    }

    public function getKelases(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }
}
