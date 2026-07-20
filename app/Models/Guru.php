<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    protected $fillable = ['nip', 'nama', 'no_hp'];

    public function absensis(): MorphMany
    {
        return $this->morphMany(Absensi::class, 'absensable');
    }
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function kelas(): HasOne
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id');
    }
}
