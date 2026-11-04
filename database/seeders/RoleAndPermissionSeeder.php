<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. PASTIKAN SELURUH PERMISSION ENTITAS & HALAMAN SISTEM TERSEDIA
        $entities = [
            'Absensi', 'AjukanIzin', 'Buku', 'Guru', 'HariLibur',
            'JadwalGuruPiket', 'JadwalMengajar', 'JamSetting', 'Kelas',
            'KunjunganPerpustakaan', 'PeminjamanBuku', 'PengajuanIzin',
            'PerpustakaanSetting', 'PresensiKelasSaya', 'Role', 'Siswa',
            'User', 'WaSetting',
        ];
        $actions = ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}:{$entity}", 'guard_name' => 'web']);
            }
        }

        $pagePermissions = [
            'View:Dashboard',
            'View:DashboardPerpustakaan',
            'View:ScanPerpustakaan',
            'View:LeaderboardLiterasi',
            'View:Dokumentasi',
            'View:ScanAbsensi',
            'View:LeaderboardKedisiplinan',
            'View:AttendanceStatsOverview',
            'View:HeaderGreetingWidget',
            'View:RecentAbsensisTable',
        ];

        foreach ($pagePermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. DAFTARKAN SEMUA ROLE SESUAI KEBUTUHAN SISTEM
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminPerpusRole = Role::firstOrCreate(['name' => 'admin_perpustakaan', 'guard_name' => 'web']);
        $staffPerpusRole = Role::firstOrCreate(['name' => 'staff_perpustakaan', 'guard_name' => 'web']);
        $adminAbsensiRole = Role::firstOrCreate(['name' => 'admin_absensi', 'guard_name' => 'web']);
        $staffAbsensiRole = Role::firstOrCreate(['name' => 'staff_absensi', 'guard_name' => 'web']);
        $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $waliKelasRole = Role::firstOrCreate(['name' => 'wali_kelas', 'guard_name' => 'web']);
        $waliKelasLegacyRole = Role::firstOrCreate(['name' => 'Wali Kelas', 'guard_name' => 'web']);
        $siswaRole = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

        // 3. ASSIGN PERMISSION: SUPER ADMIN (MENGELOLA SEMUA FITUR)
        $superAdminRole->syncPermissions(Permission::all());

        // 4. ASSIGN PERMISSION: ADMIN PERPUSTAKAAN
        // Mengelola seluruh data buku, peminjaman, kunjungan, pengaturan perpus, dan dashboard perpus (tanpa dashboard utama)
        $perpusPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%:Buku%')
                ->orWhere('name', 'like', '%:PeminjamanBuku%')
                ->orWhere('name', 'like', '%:KunjunganPerpustakaan%')
                ->orWhere('name', 'like', '%:PerpustakaanSetting%')
                ->orWhereIn('name', [
                    'View:DashboardPerpustakaan',
                    'View:ScanPerpustakaan',
                    'View:LeaderboardLiterasi',
                    'View:Dokumentasi',
                ]);
        })->get();
        $adminPerpusRole->syncPermissions($perpusPermissions);

        // 5. ASSIGN PERMISSION: STAFF PERPUSTAKAAN
        // Hanya akses ke dashboard perpustakaan dan menu scanner perpustakaan
        $staffPerpusRole->syncPermissions([
            'View:DashboardPerpustakaan',
            'View:ScanPerpustakaan',
        ]);

        // 6. ASSIGN PERMISSION: ADMIN ABSENSI
        // Mengelola seluruh data absensi, siswa, guru, kelas, jadwal, jam, WhatsApp, JadwalGuruPiket, PengajuanIzin, dan dashboard utama
        $absensiPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%:Absensi%')
                ->orWhere('name', 'like', '%:Siswa%')
                ->orWhere('name', 'like', '%:Guru%')
                ->orWhere('name', 'like', '%:Kelas%')
                ->orWhere('name', 'like', '%:HariLibur%')
                ->orWhere('name', 'like', '%:JamSetting%')
                ->orWhere('name', 'like', '%:JadwalMengajar%')
                ->orWhere('name', 'like', '%:WaSetting%')
                ->orWhere('name', 'like', '%:JadwalGuruPiket%')
                ->orWhere('name', 'like', '%:PengajuanIzin%')
                ->orWhereIn('name', [
                    'View:Dashboard',
                    'View:ScanAbsensi',
                    'View:LeaderboardKedisiplinan',
                    'View:Dokumentasi',
                    'View:AttendanceStatsOverview',
                    'View:HeaderGreetingWidget',
                    'View:RecentAbsensisTable',
                ]);
        })->get();
        $adminAbsensiRole->syncPermissions($absensiPermissions);

        // 7. ASSIGN PERMISSION: STAFF ABSENSI
        // Hanya akses ke dashboard utama dan menu scanner absensi
        $staffAbsensiRole->syncPermissions([
            'View:Dashboard',
            'View:ScanAbsensi',
            'View:AttendanceStatsOverview',
            'View:HeaderGreetingWidget',
            'View:RecentAbsensisTable',
        ]);

        // 8. ASSIGN PERMISSION: GURU
        // Akses: Dashboard, Rekap Absensi (pribadi, read-only), Ajukan Izin
        $guruPermissions = [
            'View:Dashboard',
            'View:AttendanceStatsOverview',
            'View:HeaderGreetingWidget',
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:PengajuanIzin',
            'View:PengajuanIzin',
            'Create:PengajuanIzin',
        ];
        $guruRole->syncPermissions($guruPermissions);

        // 9. ASSIGN PERMISSION: WALI KELAS
        // Akses: Dashboard, Rekap Absensi (pribadi + kelasnya, read-only), Data Kelas (kelas binaannya, read-only + ekspor bulanan), Ajukan Izin
        $waliKelasPermissions = [
            'View:Dashboard',
            'View:AttendanceStatsOverview',
            'View:HeaderGreetingWidget',
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:Kelas',
            'View:Kelas',
            'ViewAny:PengajuanIzin',
            'View:PengajuanIzin',
            'Create:PengajuanIzin',
        ];
        $waliKelasRole->syncPermissions($waliKelasPermissions);
        $waliKelasLegacyRole->syncPermissions($waliKelasPermissions);

        // 10. ASSIGN PERMISSION: SISWA
        // Akses: Dashboard, Rekap Absensi (pribadi, read-only), Ajukan Izin
        $siswaPermissions = [
            'View:Dashboard',
            'View:HeaderGreetingWidget',
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:PengajuanIzin',
            'View:PengajuanIzin',
            'Create:PengajuanIzin',
        ];
        $siswaRole->syncPermissions($siswaPermissions);

        // 11. BUAT AKUN DEFAULT RESMI UNTUK SEMUA ROLE (@maisba.sch.id)
        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'admin@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'Super Administrator (Alternatif)',
                'email' => 'superadmin@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin Perpustakaan',
                'email' => 'admin.perpus@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin_perpustakaan',
            ],
            [
                'name' => 'Staff Perpustakaan',
                'email' => 'staff.perpus@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'staff_perpustakaan',
            ],
            [
                'name' => 'Admin Absensi',
                'email' => 'admin.absensi@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin_absensi',
            ],
            [
                'name' => 'Staff Absensi',
                'email' => 'staff.absensi@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'staff_absensi',
            ],
            [
                'name' => 'Guru Demo',
                'email' => 'guru@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ],
            [
                'name' => 'Wali Kelas Demo',
                'email' => 'walikelas@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
            ],
            [
                'name' => 'Siswa Demo',
                'email' => 'siswa@maisba.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ],
        ];

        // Ambil profil referensi untuk binding
        $sampleGuru = Guru::doesntHave('kelas')->first() ?: Guru::first();
        $sampleWali = Guru::whereHas('kelas')->first() ?: Guru::first();
        $sampleSiswa = Siswa::first();

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Bind polymorphic profil jika role adalah guru, wali_kelas, atau siswa
            if ($roleName === 'guru' && $sampleGuru) {
                $user->update([
                    'userable_type' => Guru::class,
                    'userable_id' => $sampleGuru->id,
                ]);
            } elseif ($roleName === 'wali_kelas' && $sampleWali) {
                $user->update([
                    'userable_type' => Guru::class,
                    'userable_id' => $sampleWali->id,
                ]);
            } elseif ($roleName === 'siswa' && $sampleSiswa) {
                $user->update([
                    'userable_type' => Siswa::class,
                    'userable_id' => $sampleSiswa->id,
                ]);
            }

            $user->syncRoles([$roleName]);
        }

        // Pastikan akun admin utama (User ID 1 atau admin@maisba.sch.id / admin@maisba.com) tetap memiliki super_admin
        $adminUsers = User::where('id', 1)
            ->orWhereIn('email', ['admin@maisba.sch.id', 'admin@maisba.com'])
            ->get();
        foreach ($adminUsers as $admin) {
            $admin->assignRole('super_admin');
        }

        // Clear cache permissions lagi
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
