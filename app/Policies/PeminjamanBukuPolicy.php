<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PeminjamanBuku;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeminjamanBukuPolicy
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
        return $authUser->can('ViewAny:PeminjamanBuku');
    }

    public function view(AuthUser $authUser, PeminjamanBuku $peminjamanBuku): bool
    {
        return $authUser->can('View:PeminjamanBuku');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PeminjamanBuku');
    }

    public function update(AuthUser $authUser, PeminjamanBuku $peminjamanBuku): bool
    {
        return $authUser->can('Update:PeminjamanBuku');
    }

    public function delete(AuthUser $authUser, PeminjamanBuku $peminjamanBuku): bool
    {
        return $authUser->can('Delete:PeminjamanBuku');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PeminjamanBuku');
    }

    public function restore(AuthUser $authUser, PeminjamanBuku $peminjamanBuku): bool
    {
        return $authUser->can('Restore:PeminjamanBuku');
    }

    public function forceDelete(AuthUser $authUser, PeminjamanBuku $peminjamanBuku): bool
    {
        return $authUser->can('ForceDelete:PeminjamanBuku');
    }
}
