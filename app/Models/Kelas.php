<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $fillable = ['nama_kelas', 'wali_kelas_id'];

    // Relasi ke Guru sebagai Wali Kelas
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    // Relasi ke Siswa
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }
}
