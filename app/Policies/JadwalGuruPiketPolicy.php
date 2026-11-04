<?php

namespace App\Policies;

use App\Models\JadwalGuruPiket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JadwalGuruPiketPolicy
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
        return $user->hasRole('admin_absensi') || $user->can('ViewAny:JadwalGuruPiket');
    }

    public function view(User $user, JadwalGuruPiket $record): bool
    {
        return $user->hasRole('admin_absensi') || $user->can('View:JadwalGuruPiket');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin_absensi') || $user->can('Create:JadwalGuruPiket');
    }

    public function update(User $user, JadwalGuruPiket $record): bool
    {
        return $user->hasRole('admin_absensi') || $user->can('Update:JadwalGuruPiket');
    }

    public function delete(User $user, JadwalGuruPiket $record): bool
    {
        return $user->hasRole('admin_absensi') || $user->can('Delete:JadwalGuruPiket');
    }
}
