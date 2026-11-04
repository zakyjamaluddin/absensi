<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KunjunganPerpustakaan extends Model
{
    protected $fillable = [
        'siswa_id',
        'tanggal',
        'waktu_masuk',
        'keperluan',
    ];

    protected static function booted(): void
    {
        static::creating(function (KunjunganPerpustakaan $kunjungan) {
            if (empty($kunjungan->tanggal)) {
                $kunjungan->tanggal = now()->toDateString();
            }
            if (empty($kunjungan->waktu_masuk)) {
                $kunjungan->waktu_masuk = now()->toTimeString();
            }
        });
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
