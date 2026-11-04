<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class JadwalGuruPiket extends Model
{
    protected $fillable = [
        'hari',
        'guru_id',
        'keterangan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public static array $hariList = [
        'Senin' => 'Senin',
        'Selasa' => 'Selasa',
        'Rabu' => 'Rabu',
        'Kamis' => 'Kamis',
        'Jumat' => 'Jumat',
        'Sabtu' => 'Sabtu',
        'Ahad' => 'Ahad',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Dapatkan nama hari ini dalam bahasa Indonesia
     */
    public static function getHariIniName(?string $date = null): string
    {
        $carbon = $date ? Carbon::parse($date) : Carbon::today();
        return match ($carbon->englishDayOfWeek) {
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Ahad',
            default => 'Senin',
        };
    }

    /**
     * Cek apakah user atau guru tertentu sedang piket hari ini
     */
    public static function isUserPiketToday($user = null): bool
    {
        $user = $user ?: auth()->user();
        if (!$user) {
            return false;
        }

        // Super Admin selalu memiliki otoritas setara guru piket
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if (!($user->userable instanceof Guru)) {
            return false;
        }

        $hariIni = static::getHariIniName();

        return static::where('hari', $hariIni)
            ->where('guru_id', $user->userable_id)
            ->where('is_aktif', true)
            ->exists();
    }

    /**
     * Dapatkan daftar Guru yang piket hari ini
     */
    public static function getGuruPiketHariIni(?string $date = null): Collection
    {
        $hari = static::getHariIniName($date);

        return static::with('guru.user')
            ->where('hari', $hari)
            ->where('is_aktif', true)
            ->get()
            ->pluck('guru')
            ->filter();
    }

    /**
     * Dapatkan daftar Akun User dari guru-guru yang piket hari ini
     */
    public static function getUserPiketHariIni(?string $date = null): Collection
    {
        $gurus = static::getGuruPiketHariIni($date);

        return $gurus->map(fn ($guru) => $guru->user)->filter();
    }
}
