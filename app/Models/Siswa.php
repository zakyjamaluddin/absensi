<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Siswa extends Model
{
    protected $fillable = ['nis', 'nama', 'kelas_id'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensis(): MorphMany
    {
        return $this->morphMany(Absensi::class, 'absensable');
    }

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
