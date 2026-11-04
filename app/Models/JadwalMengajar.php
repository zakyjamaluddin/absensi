<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalMengajar extends Model
{
    protected $fillable = [
        'guru_id',
        'hari',
        'kelas_id',
        'mata_pelajaran',
        'jam_ke',
        'mulai_jam_ke',
        'sampai_jam_ke',
        'jam_mulai',
        'jam_selesai',
    ];

    protected function casts(): array
    {
        return [
            'mulai_jam_ke' => 'integer',
            'sampai_jam_ke' => 'integer',
        ];
    }

    public static array $hariMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];

    protected static function booted(): void
    {
        static::saving(function (JadwalMengajar $jadwal) {
            if (!empty($jadwal->mulai_jam_ke)) {
                $mulai = (int) $jadwal->mulai_jam_ke;
                $sampai = (int) ($jadwal->sampai_jam_ke ?? $mulai);

                $start = min($mulai, $sampai);
                $end = max($mulai, $sampai);

                $jadwal->mulai_jam_ke = $start;
                $jadwal->sampai_jam_ke = $end;
                $jadwal->jam_ke = ($start === $end) ? (string) $start : "{$start} - {$end}";

                $jpStart = JamPelajaran::where('jam_ke', $start)->first();
                $jpEnd = JamPelajaran::where('jam_ke', $end)->first();

                if ($jpStart) {
                    $jadwal->jam_mulai = $jpStart->jam_mulai;
                }
                if ($jpEnd) {
                    $jadwal->jam_selesai = $jpEnd->jam_selesai;
                }
            }
        });
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function getHariIndoAttribute(): string
    {
        return self::$hariMap[$this->hari] ?? $this->hari;
    }
}
