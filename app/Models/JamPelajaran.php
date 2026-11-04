<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    protected $fillable = [
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
    ];

    protected function casts(): array
    {
        return [
            'jam_ke' => 'integer',
        ];
    }

    /**
     * Dapatkan daftar pilihan jam pelajaran untuk Select dropdown
     */
    public static function getOptions(): array
    {
        return self::orderBy('jam_ke')->get()->mapWithKeys(function ($item) {
            $mulai = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
            $selesai = $item->jam_selesai ? Carbon::parse($item->jam_selesai)->format('H:i') : '--:--';
            return [$item->jam_ke => "Jam ke-{$item->jam_ke} ({$mulai} - {$selesai})"];
        })->toArray();
    }
}
