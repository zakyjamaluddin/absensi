<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JamSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEED DATA PENGATURAN JAM ABSENSI (Hanya 1 baris)
        // Catatan: Jam ini dibuat cukup longgar agar saat Anda menguji (pagi, siang, atau sore),
        // proses scan bisa langsung masuk ke salah satu jendela waktu tanpa terblokir.
        JamSetting::create([
            'mulai_masuk'    => '06:00:00', // Mulai absen masuk
            'selesai_masuk'  => '11:59:59', // Akhir batas absen masuk
            'batas_terlambat' => '07:00:00',
            'mulai_pulang'   => '12:00:00', // Mulai absen pulang
            'selesai_pulang' => '21:00:00', // Akhir batas absen pulang
        ]);

        // 2. SEED DATA GURU
        $guru1 = Guru::create([
            'nip'   => '19810101', // Ini NIP untuk bahan tes scan guru 1
            'nama'  => 'Ustadz Ahmad Fauzi',
            'no_hp' => '081234567890',
        ]);

        $guru2 = Guru::create([
            'nip'   => '19850202', // Ini NIP untuk bahan tes scan guru 2
            'nama'  => 'Ustadz Muhammad Ridwan',
            'no_hp' => '081234567891',
        ]);

        // 3. SEED DATA KELAS
        $kelas1 = Kelas::create([
            'nama_kelas'    => 'Kelas 10-A (Ula)',
            'wali_kelas_id' => $guru1->id, // Wali kelas adalah Ustadz Ahmad
        ]);

        $kelas2 = Kelas::create([
            'nama_kelas'    => 'Kelas 10-B (Ula)',
            'wali_kelas_id' => $guru2->id, // Wali kelas adalah Ustadz Ridwan
        ]);

        // 4. SEED DATA SISWA
        Siswa::create([
            'nis'      => '20260001', // Ini NIS untuk bahan tes scan siswa 1
            'nama'     => 'Muhammad Al-Fatih',
            'kelas_id' => $kelas1->id,
        ]);

        Siswa::create([
            'nis'      => '20260002', // Ini NIS untuk bahan tes scan siswa 2
            'nama'     => 'Ali bin Abi Thalib',
            'kelas_id' => $kelas1->id,
        ]);

        Siswa::create([
            'nis'      => '20260003', // Ini NIS untuk bahan tes scan siswa 3
            'nama'     => 'Umar bin Khattab',
            'kelas_id' => $kelas2->id,
        ]);
    }
}
