<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PeminjamanBuku extends Model
{
    protected $fillable = [
        'kode_peminjaman',
        'siswa_id',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'status',
        'catatan',
    ];

    protected static function booted(): void
    {
        static::creating(function (PeminjamanBuku $pinjam) {
            // Generate kode transaksi unik jika belum ada
            if (empty($pinjam->kode_peminjaman)) {
                $pinjam->kode_peminjaman = self::generateUniqueKode();
            }

            // Set default tanggal pinjam hari ini
            if (empty($pinjam->tanggal_pinjam)) {
                $pinjam->tanggal_pinjam = now()->toDateString();
            }

            // Set default jatuh tempo dari settings jika kosong
            if (empty($pinjam->tanggal_jatuh_tempo)) {
                $setting = PerpustakaanSetting::getSetting();
                $pinjam->tanggal_jatuh_tempo = Carbon::parse($pinjam->tanggal_pinjam)
                    ->addDays($setting->lama_pinjam_default ?? 7)
                    ->toDateString();
            }

            // Kurangi ketersediaan stok buku
            $buku = Buku::find($pinjam->buku_id);
            if ($buku && $buku->tersedia > 0) {
                $buku->decrement('tersedia');
            }
        });
    }

    public static function generateUniqueKode(): string
    {
        do {
            $kode = 'PJ-' . date('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('kode_peminjaman', $kode)->exists());

        return $kode;
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    /**
     * Tandai buku telah dikembalikan
     */
    public function kembalikan(?string $tanggal = null): bool
    {
        if ($this->status === 'Dikembalikan') {
            return false;
        }

        $tanggalKembali = $tanggal ?: now()->toDateString();
        $this->update([
            'tanggal_kembali' => $tanggalKembali,
            'status' => 'Dikembalikan',
        ]);

        // Tambah kembali stok ketersediaan buku
        if ($this->buku) {
            $this->buku->increment('tersedia');
        }

        return true;
    }
}
