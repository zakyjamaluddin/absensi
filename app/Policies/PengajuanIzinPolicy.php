<?php

namespace App\Policies;

use App\Models\JadwalGuruPiket;
use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengajuanIzinPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        // Admin, Staff Absensi, Guru, Wali Kelas, Siswa, atau Guru Piket berhak melihat data perizinan
        return $user->hasAnyRole(['super_admin', 'admin_absensi', 'staff_absensi', 'guru', 'wali_kelas', 'Wali Kelas', 'siswa'])
            || JadwalGuruPiket::isUserPiketToday($user);
    }

    public function view(User $user, PengajuanIzin $record): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin_absensi', 'staff_absensi']) || JadwalGuruPiket::isUserPiketToday($user)) {
            return true;
        }

        if ($user->userable_type === $record->izinable_type && $user->userable_id === $record->izinable_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['siswa', 'guru', 'wali_kelas', 'Wali Kelas', 'super_admin']) || ($user->userable_id !== null);
    }

    public function update(User $user, PengajuanIzin $record): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin_absensi', 'staff_absensi']) || JadwalGuruPiket::isUserPiketToday($user)) {
            return true;
        }

        return $record->status === 'Menunggu' && $user->userable_id === $record->izinable_id;
    }

    public function delete(User $user, PengajuanIzin $record): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $record->status === 'Menunggu' && $user->userable_id === $record->izinable_id;
    }
}
