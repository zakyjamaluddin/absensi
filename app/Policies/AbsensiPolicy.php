<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Absensi;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;

    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        return null;
    }
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Absensi');
    }

    public function view(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('View:Absensi');
    }

    public function create(AuthUser $authUser): bool
    {
        if ($authUser->hasAnyRole(['guru', 'wali_kelas', 'Wali Kelas', 'siswa'])) {
            return false;
        }

        return $authUser->can('Create:Absensi');
    }

    public function update(AuthUser $authUser, Absensi $absensi): bool
    {
        if ($authUser->hasAnyRole(['guru', 'wali_kelas', 'Wali Kelas', 'siswa'])) {
            return false;
        }

        return $authUser->can('Update:Absensi');
    }

    public function delete(AuthUser $authUser, Absensi $absensi): bool
    {
        if ($authUser->hasAnyRole(['guru', 'wali_kelas', 'Wali Kelas', 'siswa'])) {
            return false;
        }

        return $authUser->can('Delete:Absensi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Absensi');
    }

    public function restore(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('Restore:Absensi');
    }

    public function forceDelete(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('ForceDelete:Absensi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Absensi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Absensi');
    }

    public function replicate(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('Replicate:Absensi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Absensi');
    }

}