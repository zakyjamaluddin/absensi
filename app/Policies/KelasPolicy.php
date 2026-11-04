<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Kelas;
use Illuminate\Auth\Access\HandlesAuthorization;

class KelasPolicy
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
        if ($authUser->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return true;
        }

        return $authUser->can('ViewAny:Kelas');
    }

    public function view(AuthUser $authUser, Kelas $kelas): bool
    {
        if ($authUser->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            $guruId = $authUser->userable_id;
            return $kelas->wali_kelas_id === $guruId;
        }

        return $authUser->can('View:Kelas');
    }

    public function create(AuthUser $authUser): bool
    {
        if ($authUser->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return false;
        }

        return $authUser->can('Create:Kelas');
    }

    public function update(AuthUser $authUser, Kelas $kelas): bool
    {
        if ($authUser->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return false;
        }

        return $authUser->can('Update:Kelas');
    }

    public function delete(AuthUser $authUser, Kelas $kelas): bool
    {
        if ($authUser->hasAnyRole(['wali_kelas', 'Wali Kelas'])) {
            return false;
        }

        return $authUser->can('Delete:Kelas');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Kelas');
    }

    public function restore(AuthUser $authUser, Kelas $kelas): bool
    {
        return $authUser->can('Restore:Kelas');
    }

    public function forceDelete(AuthUser $authUser, Kelas $kelas): bool
    {
        return $authUser->can('ForceDelete:Kelas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Kelas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Kelas');
    }

    public function replicate(AuthUser $authUser, Kelas $kelas): bool
    {
        return $authUser->can('Replicate:Kelas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Kelas');
    }

}