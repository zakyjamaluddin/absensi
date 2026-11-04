<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JadwalMengajar;
use Illuminate\Auth\Access\HandlesAuthorization;

class JadwalMengajarPolicy
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
        return $authUser->can('ViewAny:JadwalMengajar');
    }

    public function view(AuthUser $authUser, JadwalMengajar $jadwal): bool
    {
        return $authUser->can('View:JadwalMengajar');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JadwalMengajar');
    }

    public function update(AuthUser $authUser, JadwalMengajar $jadwal): bool
    {
        return $authUser->can('Update:JadwalMengajar');
    }

    public function delete(AuthUser $authUser, JadwalMengajar $jadwal): bool
    {
        return $authUser->can('Delete:JadwalMengajar');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JadwalMengajar');
    }

    public function restore(AuthUser $authUser, JadwalMengajar $jadwal): bool
    {
        return $authUser->can('Restore:JadwalMengajar');
    }

    public function forceDelete(AuthUser $authUser, JadwalMengajar $jadwal): bool
    {
        return $authUser->can('ForceDelete:JadwalMengajar');
    }
}
