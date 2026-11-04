<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Buku extends Model
{
    protected $fillable = [
        'kode_buku',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'kategori',
        'stok',
        'tersedia',
        'rak',
        'deskripsi',
    ];

    protected static function booted(): void
    {
        static::creating(function (Buku $buku) {
            // Jika kode buku tidak diisi, generate kode otomatis unik
            if (empty($buku->kode_buku)) {
                $buku->kode_buku = self::generateUniqueKode();
            }

            // Inisialisasi jumlah buku yang tersedia sama dengan stok
            if (!isset($buku->tersedia)) {
                $buku->tersedia = $buku->stok ?? 1;
            }
        });
    }

    public static function generateUniqueKode(): string
    {
        do {
            $kode = 'BK-' . date('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('kode_buku', $kode)->exists());

        return $kode;
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(PeminjamanBuku::class);
    }

    public function isAvailable(): bool
    {
        return $this->tersedia > 0;
    }
}
