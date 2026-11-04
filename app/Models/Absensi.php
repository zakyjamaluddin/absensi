<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Absensi extends Model
{
    protected $guarded = [];

    public function absensable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Memastikan seluruh santri dan guru aktif memiliki slot absensi untuk tanggal tertentu
     * dengan status default 'Belum Absen' jika belum melakukan presensi.
     */
    public static function ensureDailyRecordsForDate(string $date, ?int $kelasId = null): void
    {
        // 1. Siswa
        $siswaQuery = Siswa::query();
        if ($kelasId) {
            $siswaQuery->where('kelas_id', $kelasId);
        }
        $siswas = $siswaQuery->get();

        foreach ($siswas as $siswa) {
            static::firstOrCreate(
                [
                    'tanggal' => $date,
                    'absensable_type' => Siswa::class,
                    'absensable_id' => $siswa->id,
                ],
                [
                    'jam_masuk' => null,
                    'status_masuk' => 'Belum Absen',
                ]
            );
        }

        // 2. Guru (jika tidak dibatasi kelas tertentu)
        if (! $kelasId) {
            $gurus = Guru::all();
            foreach ($gurus as $guru) {
                static::firstOrCreate(
                    [
                        'tanggal' => $date,
                        'absensable_type' => Guru::class,
                        'absensable_id' => $guru->id,
                    ],
                    [
                        'jam_masuk' => null,
                        'status_masuk' => 'Belum Absen',
                    ]
                );
            }
        }
    }

    /**
     * Mendapatkan rekapitulasi statistik kehadiran untuk siswa atau guru tertentu.
     */
    public static function getStatsForSubject($subject, string $periode = 'bulan_ini', ?string $dari = null, ?string $sampai = null): array
    {
        $query = static::where('absensable_type', get_class($subject))
            ->where('absensable_id', $subject->id);

        if ($periode === 'bulan_ini') {
            $query->whereMonth('tanggal', Carbon::now()->month)
                  ->whereYear('tanggal', Carbon::now()->year);
        } elseif ($periode === 'tahun_ini') {
            $query->whereYear('tanggal', Carbon::now()->year);
        } elseif ($periode === 'custom' && $dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($periode === 'custom' && $dari) {
            $query->whereDate('tanggal', '>=', $dari);
        } elseif ($periode === 'custom' && $sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        $records = $query->orderBy('tanggal', 'desc')->get();

        $tepatWaktu = $records->where('status_masuk', 'Tepat Waktu')->count();
        $terlambat = $records->where('status_masuk', 'Terlambat')->count();
        $sakit = $records->where('status_masuk', 'Sakit')->count();
        $izin = $records->where('status_masuk', 'Izin')->count();
        $alpa = $records->where('status_masuk', 'Alpa')->count();
        $belumAbsen = $records->where('status_masuk', 'Belum Absen')->count();

        $totalHadir = $tepatWaktu + $terlambat;
        $totalAktif = $tepatWaktu + $terlambat + $sakit + $izin + $alpa;
        $persentase = $totalAktif > 0 ? round(($totalHadir / $totalAktif) * 100, 1) : 0;

        return [
            'tepat_waktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'belum_absen' => $belumAbsen,
            'total_hadir' => $totalHadir,
            'total_aktif' => $totalAktif,
            'persentase' => $persentase,
            'records' => $records,
        ];
    }
}
