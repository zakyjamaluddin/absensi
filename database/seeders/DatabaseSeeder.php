<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Guru;
use App\Models\JadwalGuruPiket;
use App\Models\JamSetting;
use App\Models\Kelas;
use App\Models\PerpustakaanSetting;
use App\Models\Siswa;
use App\Models\WaSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEED DATA PENGATURAN JAM ABSENSI (Hanya 1 baris)
        JamSetting::firstOrCreate(
            ['id' => 1],
            [
                'mulai_masuk'         => '06:00:00',
                'selesai_masuk'       => '11:59:00',
                'batas_terlambat'     => '07:00:00',
                'mulai_pulang'        => '12:00:00',
                'selesai_pulang'      => '21:00:00',
                'libur_pekanan'       => ['Friday'],
                'mode_absensi_guru'   => 'statis',
                'aturan_terlambat_guru' => 'jadwal_mengajar',
            ]
        );

        // 2. SEED PENGATURAN WHATSAPP GATEWAY (Hanya 1 baris)
        WaSetting::firstOrCreate(
            ['id' => 1],
            [
                'token'               => 'DEMO_SIDOBE_TOKEN',
                'tipe_proses_alpa'    => 'Otomatis',
                'jam_proses_alpa'     => '08:30:00',
                'notif_wa_wali_masuk' => true,
                'notif_wa_wali_pulang' => true,
                'notif_wa_wali_alpa'  => true,
                'notif_wa_guru_masuk' => true,
                'notif_wa_guru_pulang' => true,
                'report_guru_alpa'    => true,
            ]
        );

        // 3. SEED PENGATURAN PERPUSTAKAAN (Hanya 1 baris)
        PerpustakaanSetting::firstOrCreate(
            ['id' => 1],
            [
                'lama_pinjam_default' => 7,
                'poin_kunjungan'      => 5,
                'poin_peminjaman'     => 15,
            ]
        );

        // 4. SEED DATA GURU
        $guru1 = Guru::firstOrCreate(
            ['nip' => '19810101'],
            [
                'nama'  => 'Ustadz Ahmad Fauzi',
                'no_hp' => '081234567890',
                'is_notif_wa_aktif' => true,
            ]
        );

        $guru2 = Guru::firstOrCreate(
            ['nip' => '19850202'],
            [
                'nama'  => 'Ustadz Muhammad Ridwan',
                'no_hp' => '081234567891',
                'is_notif_wa_aktif' => true,
            ]
        );

        $guru3 = Guru::firstOrCreate(
            ['nip' => '19900303'],
            [
                'nama'  => 'Ustadzah Siti Aisyah',
                'no_hp' => '081234567892',
                'is_notif_wa_aktif' => true,
            ]
        );

        // 5. SEED DATA KELAS
        $kelas1 = Kelas::firstOrCreate(
            ['nama_kelas' => 'Kelas 10-A (Ula)'],
            ['wali_kelas_id' => $guru1->id]
        );

        $kelas2 = Kelas::firstOrCreate(
            ['nama_kelas' => 'Kelas 10-B (Ula)'],
            ['wali_kelas_id' => $guru2->id]
        );

        // 6. SEED DATA SISWA
        Siswa::firstOrCreate(
            ['nis' => '20260001'],
            [
                'nama'              => 'Muhammad Al-Fatih',
                'kelas_id'          => $kelas1->id,
                'nomor_wa_wali'     => '081234567899',
                'is_notif_wa_aktif' => true,
            ]
        );

        Siswa::firstOrCreate(
            ['nis' => '20260002'],
            [
                'nama'              => 'Ali bin Abi Thalib',
                'kelas_id'          => $kelas1->id,
                'nomor_wa_wali'     => '081234567898',
                'is_notif_wa_aktif' => true,
            ]
        );

        Siswa::firstOrCreate(
            ['nis' => '20260003'],
            [
                'nama'              => 'Umar bin Khattab',
                'kelas_id'          => $kelas2->id,
                'nomor_wa_wali'     => '081234567897',
                'is_notif_wa_aktif' => true,
            ]
        );

        Siswa::firstOrCreate(
            ['nis' => '20260004'],
            [
                'nama'              => 'Ahmad Dani',
                'kelas_id'          => $kelas1->id,
                'nomor_wa_wali'     => '081234567896',
                'is_notif_wa_aktif' => true,
            ]
        );

        // 7. SEED DATA BUKU PERPUSTAKAAN
        Buku::firstOrCreate(
            ['kode_buku' => 'BK-001'],
            [
                'judul'        => 'Fiqih Sunnah Praktis',
                'penulis'      => 'Sayyid Sabiq',
                'penerbit'     => 'Darul Kutub',
                'tahun_terbit' => 2022,
                'kategori'     => 'Agama Islam',
                'stok'         => 10,
                'tersedia'     => 10,
                'rak'          => 'Rak A-1',
            ]
        );

        Buku::firstOrCreate(
            ['kode_buku' => 'BK-002'],
            [
                'judul'        => 'Laskar Pelangi',
                'penulis'      => 'Andrea Hirata',
                'penerbit'     => 'Bentang Pustaka',
                'tahun_terbit' => 2018,
                'kategori'     => 'Sastra / Novel',
                'stok'         => 5,
                'tersedia'     => 5,
                'rak'          => 'Rak B-2',
            ]
        );

        // 8. SEED JADWAL GURU PIKET
        JadwalGuruPiket::firstOrCreate(
            ['hari' => 'Senin', 'guru_id' => $guru1->id],
            ['keterangan' => 'Pos Gerbang Utama', 'is_aktif' => true]
        );

        JadwalGuruPiket::firstOrCreate(
            ['hari' => 'Selasa', 'guru_id' => $guru2->id],
            ['keterangan' => 'Pos Gerbang Barat', 'is_aktif' => true]
        );

        // 9. SEED ROLES, PERMISSIONS, DAN AKUN RESMI (@maisba.sch.id)
        $this->call(RoleAndPermissionSeeder::class);
    }
}
