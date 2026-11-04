<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerpustakaanSetting extends Model
{
    protected $fillable = [
        'lama_pinjam_default',
        'poin_kunjungan',
        'poin_peminjaman',
    ];

    public static function getSetting(): self
    {
        return self::firstOrCreate([], [
            'lama_pinjam_default' => 7,
            'poin_kunjungan' => 5,
            'poin_peminjaman' => 15,
        ]);
    }
}
