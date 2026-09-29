<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'libur_pekanan' => 'array', // Otomatis mengubah JSON menjadi Array PHP murni
        ];
    }
}
