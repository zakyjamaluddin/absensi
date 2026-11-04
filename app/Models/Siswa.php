<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Siswa extends Model
{
    protected $fillable = [
        'nis',
        'nama',
        'foto',
        'kelas_id',
        'nomor_wa_wali',
        'is_notif_wa_aktif',
    ];

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

    public function kunjunganPerpustakaans(): HasMany
    {
        return $this->hasMany(KunjunganPerpustakaan::class);
    }

    public function peminjamanBukus(): HasMany
    {
        return $this->hasMany(PeminjamanBuku::class);
    }

    /**
     * Menghitung total skor minat literasi siswa berdasarkan kunjungan & peminjaman buku
     */
    public function getSkorLiterasi(string $periode = 'all'): int
    {
        $setting = PerpustakaanSetting::getSetting();
        $poinKunjungan = $setting->poin_kunjungan ?? 5;
        $poinPinjam = $setting->poin_peminjaman ?? 15;

        $kunjunganQuery = $this->kunjunganPerpustakaans();
        $pinjamQuery = $this->peminjamanBukus();

        if ($periode === 'bulan_ini') {
            $startOfMonth = now()->startOfMonth()->toDateString();
            $endOfMonth = now()->endOfMonth()->toDateString();

            $kunjunganQuery->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
            $pinjamQuery->whereBetween('tanggal_pinjam', [$startOfMonth, $endOfMonth]);
        } elseif ($periode === 'tahun_ini') {
            $startOfYear = now()->startOfYear()->toDateString();
            $endOfYear = now()->endOfYear()->toDateString();

            $kunjunganQuery->whereBetween('tanggal', [$startOfYear, $endOfYear]);
            $pinjamQuery->whereBetween('tanggal_pinjam', [$startOfYear, $endOfYear]);
        }

        $totalKunjungan = $kunjunganQuery->count();
        $totalPinjam = $pinjamQuery->count();

        return ($totalKunjungan * $poinKunjungan) + ($totalPinjam * $poinPinjam);
    }
}
