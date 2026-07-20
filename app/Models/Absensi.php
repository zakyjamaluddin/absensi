<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Absensi extends Model
{
    protected $guarded = [];

    public function absensable(): MorphTo
    {
        return $this->morphTo();
    }
}
