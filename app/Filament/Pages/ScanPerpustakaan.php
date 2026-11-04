<?php

namespace App\Filament\Pages;

use App\Models\Buku;
use App\Models\KunjunganPerpustakaan;
use App\Models\PeminjamanBuku;
use App\Models\PerpustakaanSetting;
use App\Models\Siswa;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ScanPerpustakaan extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedQrCode;
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

        // admin_absensi & staff_absensi tidak memiliki akses ke scanner perpustakaan
        if ($user->hasAnyRole(['admin_absensi', 'staff_absensi'])) {
            return false;
        }

        return $user->hasAnyRole(['admin_perpustakaan', 'staff_perpustakaan']) || $user->can('View:ScanPerpustakaan');
    }

    protected static ?string $title = 'Scanner Perpustakaan';
    protected static ?string $navigationLabel = 'Scanner Perpustakaan';

    protected string $view = 'filament.pages.scan-perpustakaan';

    // State Scanner
    public $activeTab = 'kunjungan'; // 'kunjungan' | 'pinjam' | 'kembali'
    public $scannedCode = '';
    public $message = '';
    public $status = 'info'; // 'success' | 'danger' | 'info'

    // Data kartu siswa terakhir discan
    public $lastSiswa = null;
    public ?array $lastScanned = null;

    // State Peminjaman (Pinjam Buku)
    public $pinjamSiswa = null;
    public $pinjamBuku = null;
    public $lamaPinjamHari = 7;
    public $tanggalJatuhTempo = '';

    public function mount(): void
    {
        $setting = PerpustakaanSetting::getSetting();
        $this->lamaPinjamHari = $setting->lama_pinjam_default ?? 7;
        $this->tanggalJatuhTempo = now()->addDays($this->lamaPinjamHari)->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetScannerState();
    }

    public function resetScannerState(): void
    {
        $this->scannedCode = '';
        $this->message = '';
        $this->status = 'info';
        $this->pinjamSiswa = null;
        $this->pinjamBuku = null;
        $setting = PerpustakaanSetting::getSetting();
        $this->lamaPinjamHari = $setting->lama_pinjam_default ?? 7;
        $this->tanggalJatuhTempo = now()->addDays($this->lamaPinjamHari)->format('Y-m-d');
    }

    public function updatedLamaPinjamHari(): void
    {
        $days = (int) $this->lamaPinjamHari;
        if ($days < 1) $days = 1;
        $this->lamaPinjamHari = $days;
        $this->tanggalJatuhTempo = now()->addDays($days)->format('Y-m-d');
    }

    public function processScan(?string $inputCode = null): void
    {
        $code = trim($inputCode ?: $this->scannedCode);
        $this->scannedCode = '';

        if (empty($code)) {
            return;
        }

        if ($this->activeTab === 'kunjungan') {
            $this->handleScanKunjungan($code);
        } elseif ($this->activeTab === 'pinjam') {
            $this->handleScanPinjam($code);
        } elseif ($this->activeTab === 'kembali') {
            $this->handleScanKembali($code);
        }
    }

    /**
     * 1. Presensi Kunjungan Masuk Perpustakaan
     */
    protected function handleScanKunjungan(string $code): void
    {
        $siswa = Siswa::with('kelas')->where('nis', $code)->first();

        if (!$siswa) {
            $this->lastScanned = null;
            $this->message = "Kartu RFID / NIS '{$code}' tidak terdaftar.";
            $this->status = 'danger';
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        $siswa->loadMissing('kelas');
        $today = now()->toDateString();
        $setting = PerpustakaanSetting::getSetting();
        $poin = $setting->poin_kunjungan ?? 5;

        // Cek apakah sudah berkunjung hari ini
        $kunjunganHariIni = KunjunganPerpustakaan::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        if ($kunjunganHariIni) {
            $this->message = "Halo {$siswa->nama}! Anda sudah tercatat berkunjung ke perpustakaan hari ini pada pukul " . Carbon::parse($kunjunganHariIni->waktu_masuk)->format('H:i') . " WIB.";
            $this->status = 'info';
            $this->dispatch('play-sound', type: 'info');
        } else {
            KunjunganPerpustakaan::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $today,
                'waktu_masuk' => now()->toTimeString(),
                'keperluan' => 'Membaca / Belajar',
            ]);

            $this->message = "Selamat Datang, {$siswa->nama}! Kunjungan perpustakaan berhasil dicatat (+{$poin} Poin Minat Literasi).";
            $this->status = 'success';
            $this->dispatch('play-sound', type: 'success');
        }

        // Tampilkan info siswa di layar
        $this->lastSiswa = [
            'nama' => $siswa->nama,
            'nis' => $siswa->nis,
            'kelas' => $siswa->kelas?->nama_kelas ?? 'Tanpa Kelas',
            'skor' => $siswa->getSkorLiterasi('all'),
            'total_kunjungan' => $siswa->kunjunganPerpustakaans()->count(),
            'total_pinjam' => $siswa->peminjamanBukus()->count(),
        ];

        $this->lastScanned = [
            'tipe' => 'kunjungan',
            'judul_kegiatan' => 'Kunjungan Perpustakaan',
            'nama' => $siswa->nama,
            'tipe_label' => 'Siswa',
            'nomor_induk_label' => 'NIS',
            'nomor_induk' => $siswa->nis,
            'kelas' => $siswa->kelas?->nama_kelas ?? 'Tanpa Kelas',
            'foto' => $siswa->foto_url,
            'status_kehadiran' => $kunjunganHariIni ? 'Sudah Berkunjung' : 'Kunjungan Tercatat (+ ' . $poin . ' Poin)',
            'status_badge' => $kunjunganHariIni ? 'info' : 'success',
            'jam' => $kunjunganHariIni ? Carbon::parse($kunjunganHariIni->waktu_masuk)->format('H:i:s') : now()->format('H:i:s'),
            'tanggal' => now()->translatedFormat('d F Y'),
            'skor' => $siswa->getSkorLiterasi('all'),
            'total_kunjungan' => $siswa->kunjunganPerpustakaans()->count(),
            'total_pinjam' => $siswa->peminjamanBukus()->count(),
            'extra_info' => null,
        ];
    }

    /**
     * 2. Peminjaman Buku: Scan Kartu Siswa lalu Scan Kode Buku
     */
    protected function handleScanPinjam(string $code): void
    {
        // Tahap 1: Belum scan siswa -> Scan Kartu Siswa
        if (!$this->pinjamSiswa) {
            $siswa = Siswa::where('nis', $code)->first();
            if (!$siswa) {
                $this->lastScanned = null;
                $this->message = "Kartu Siswa '{$code}' tidak ditemukan. Silakan scan kartu RFID siswa terlebih dahulu.";
                $this->status = 'danger';
                $this->dispatch('play-sound', type: 'danger');
                return;
            }

            $siswa->loadMissing('kelas');
            $this->pinjamSiswa = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas?->nama_kelas ?? 'Tanpa Kelas',
            ];

            $this->lastScanned = [
                'tipe' => 'pinjam',
                'judul_kegiatan' => 'Peminjaman Buku (Langkah 1/2)',
                'nama' => $siswa->nama,
                'tipe_label' => 'Peminjam',
                'nomor_induk_label' => 'NIS',
                'nomor_induk' => $siswa->nis,
                'kelas' => $siswa->kelas?->nama_kelas ?? 'Tanpa Kelas',
                'foto' => $siswa->foto_url,
                'status_kehadiran' => 'Kartu Terverifikasi',
                'status_badge' => 'info',
                'jam' => now()->format('H:i:s'),
                'tanggal' => now()->translatedFormat('d F Y'),
                'skor' => $siswa->getSkorLiterasi('all'),
                'total_kunjungan' => $siswa->kunjunganPerpustakaans()->count(),
                'total_pinjam' => $siswa->peminjamanBukus()->count(),
                'extra_info' => 'Silakan scan kode buku berikutnya',
            ];

            $this->message = "Siswa teridentifikasi: {$siswa->nama}. Sekarang silakan SCAN KODE BUKU yang ingin dipinjam.";
            $this->status = 'info';
            $this->dispatch('play-sound', type: 'success');
            return;
        }

        // Tahap 2: Sudah ada siswa -> Scan Kode Buku
        $buku = Buku::where('kode_buku', $code)->first();
        if (!$buku) {
            $this->message = "Kode Buku '{$code}' tidak ditemukan di katalog perpustakaan.";
            $this->status = 'danger';
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        if ($buku->tersedia <= 0) {
            $this->message = "Buku '{$buku->judul}' stoknya sedang kosong / sedang dipinjam seluruhnya.";
            $this->status = 'danger';
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        // Cek apakah siswa ini sudah sedang meminjam buku yang sama dan belum mengembalikannya
        $sudahPinjam = PeminjamanBuku::where('siswa_id', $this->pinjamSiswa['id'])
            ->where('buku_id', $buku->id)
            ->where('status', 'Dipinjam')
            ->exists();

        if ($sudahPinjam) {
            $this->message = "Siswa ini sedang meminjam buku '{$buku->judul}' dan belum mengembalikannya.";
            $this->status = 'danger';
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        $this->pinjamBuku = [
            'id' => $buku->id,
            'judul' => $buku->judul,
            'kode_buku' => $buku->kode_buku,
            'penulis' => $buku->penulis ?? '-',
            'rak' => $buku->rak ?? '-',
        ];

        if ($this->lastScanned) {
            $this->lastScanned['extra_info'] = 'Buku: ' . $buku->judul . ' (' . $buku->kode_buku . ')';
            $this->lastScanned['status_kehadiran'] = 'Siap Konfirmasi Pinjam';
        }

        $this->message = "Buku terdeteksi: '{$buku->judul}'. Tekan tombol 'Konfirmasi Pinjam' di bawah untuk menyelesaikan peminjaman.";
        $this->status = 'info';
        $this->dispatch('play-sound', type: 'success');
    }

    /**
     * Konfirmasi Selesai Peminjaman Buku
     */
    public function konfirmasiPinjam(): void
    {
        if (!$this->pinjamSiswa || !$this->pinjamBuku) {
            $this->message = "Harap scan kartu siswa dan buku terlebih dahulu.";
            $this->status = 'danger';
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        $pinjam = PeminjamanBuku::create([
            'siswa_id' => $this->pinjamSiswa['id'],
            'buku_id' => $this->pinjamBuku['id'],
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => $this->tanggalJatuhTempo ?: now()->addDays($this->lamaPinjamHari)->toDateString(),
            'status' => 'Dipinjam',
        ]);

        $setting = PerpustakaanSetting::getSetting();
        $poin = $setting->poin_peminjaman ?? 15;
        $jatuhTempoFormat = Carbon::parse($pinjam->tanggal_jatuh_tempo)->translatedFormat('l, d F Y');

        $siswaModel = Siswa::find($this->pinjamSiswa['id']);

        $this->lastScanned = [
            'tipe' => 'pinjam',
            'judul_kegiatan' => 'Peminjaman Buku Sukses',
            'nama' => $this->pinjamSiswa['nama'],
            'tipe_label' => 'Peminjam',
            'nomor_induk_label' => 'NIS',
            'nomor_induk' => $this->pinjamSiswa['nis'],
            'kelas' => $this->pinjamSiswa['kelas'],
            'foto' => $siswaModel?->foto_url,
            'status_kehadiran' => 'Pinjam Disetujui',
            'status_badge' => 'success',
            'jam' => now()->format('H:i:s'),
            'tanggal' => now()->translatedFormat('d F Y'),
            'skor' => $siswaModel?->getSkorLiterasi('all') ?? 0,
            'total_kunjungan' => $siswaModel?->kunjunganPerpustakaans()->count() ?? 0,
            'total_pinjam' => $siswaModel?->peminjamanBukus()->count() ?? 0,
            'extra_info' => 'Buku: ' . $this->pinjamBuku['judul'] . ' • Jatuh Tempo: ' . $jatuhTempoFormat,
        ];

        $this->message = "SUKSES! {$this->pinjamSiswa['nama']} berhasil meminjam '{$this->pinjamBuku['judul']}'. Batas pengembalian: {$jatuhTempoFormat} (+{$poin} Poin Literasi).";
        $this->status = 'success';
        $this->dispatch('play-sound', type: 'success');

        // Reset transaksi pinjam untuk melayani peminjam berikutnya
        $this->pinjamSiswa = null;
        $this->pinjamBuku = null;
    }

    /**
     * 3. Pengembalian Buku Cepat: Cukup Scan Kode Buku
     */
    protected function handleScanKembali(string $code): void
    {
        $buku = Buku::where('kode_buku', $code)->first();

        if (!$buku) {
            $this->lastScanned = null;
            $this->message = "Buku dengan kode '{$code}' tidak terdaftar di sistem.";
            $this->status = 'danger';
            $this->dispatch('play-sound', type: 'danger');
            return;
        }

        // Cari transaksi peminjaman aktif untuk buku ini
        $peminjaman = PeminjamanBuku::where('buku_id', $buku->id)
            ->where('status', 'Dipinjam')
            ->latest()
            ->first();

        if (!$peminjaman) {
            $this->lastScanned = null;
            $this->message = "Buku '{$buku->judul}' ({$code}) saat ini tidak dalam status dipinjam.";
            $this->status = 'info';
            $this->dispatch('play-sound', type: 'info');
            return;
        }

        $namaSiswa = $peminjaman->siswa?->nama ?? 'Siswa';
        $peminjaman->kembalikan();

        $siswaModel = $peminjaman->siswa;
        $siswaModel?->loadMissing('kelas');

        $this->lastScanned = [
            'tipe' => 'kembali',
            'judul_kegiatan' => 'Pengembalian Buku',
            'nama' => $namaSiswa,
            'tipe_label' => 'Peminjam',
            'nomor_induk_label' => 'NIS',
            'nomor_induk' => $siswaModel?->nis ?? '-',
            'kelas' => $siswaModel?->kelas?->nama_kelas ?? 'Tanpa Kelas',
            'foto' => $siswaModel?->foto_url,
            'status_kehadiran' => 'Buku Kembali',
            'status_badge' => 'success',
            'jam' => now()->format('H:i:s'),
            'tanggal' => now()->translatedFormat('d F Y'),
            'skor' => $siswaModel?->getSkorLiterasi('all') ?? 0,
            'total_kunjungan' => $siswaModel?->kunjunganPerpustakaans()->count() ?? 0,
            'total_pinjam' => $siswaModel?->peminjamanBukus()->count() ?? 0,
            'extra_info' => 'Buku: ' . $buku->judul . ' (' . $buku->kode_buku . ')',
        ];

        $this->message = "BERHASIL KEMBALI! Buku '{$buku->judul}' yang dipinjam oleh {$namaSiswa} telah sukses dikembalikan. Stok otomatis ditambahkan.";
        $this->status = 'success';
        $this->dispatch('play-sound', type: 'success');
    }
}
