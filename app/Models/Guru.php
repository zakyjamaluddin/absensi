<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Carbon\Carbon;

class Guru extends Model
{
    protected $fillable = ['nip', 'nama', 'foto', 'no_hp', 'is_notif_wa_aktif'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!empty($this->foto)) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    protected function casts(): array
    {
        return [
            'is_notif_wa_aktif' => 'boolean',
        ];
    }

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

    public function jadwalMengajars(): HasMany
    {
        return $this->hasMany(JadwalMengajar::class);
    }

    public static function getHariMapping(): array
    {
        return JadwalMengajar::$hariMap;
    }

    public function getJadwalHariIni(?string $dateOrDay = null)
    {
        if ($dateOrDay) {
            if (isset(JadwalMengajar::$hariMap[$dateOrDay])) {
                $hari = JadwalMengajar::$hariMap[$dateOrDay];
            } elseif (in_array($dateOrDay, JadwalMengajar::$hariMap)) {
                $hari = $dateOrDay;
            } else {
                $hari = JadwalMengajar::$hariMap[Carbon::parse($dateOrDay)->englishDayOfWeek] ?? 'Senin';
            }
        } else {
            $hari = JadwalMengajar::$hariMap[Carbon::today()->englishDayOfWeek] ?? 'Senin';
        }

        return $this->jadwalMengajars()->where('hari', $hari)->orderBy('jam_mulai', 'asc')->get();
    }

    public function getEarliestJamMulai(?string $dateOrDay = null): ?string
    {
        $jadwal = $this->getJadwalHariIni($dateOrDay)->first();
        return $jadwal ? $jadwal->jam_mulai : null;
    }
}
