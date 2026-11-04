<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Buku;
use Illuminate\Auth\Access\HandlesAuthorization;

class BukuPolicy
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
        return $authUser->can('ViewAny:Buku');
    }

    public function view(AuthUser $authUser, Buku $buku): bool
    {
        return $authUser->can('View:Buku');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Buku');
    }

    public function update(AuthUser $authUser, Buku $buku): bool
    {
        return $authUser->can('Update:Buku');
    }

    public function delete(AuthUser $authUser, Buku $buku): bool
    {
        return $authUser->can('Delete:Buku');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Buku');
    }

    public function restore(AuthUser $authUser, Buku $buku): bool
    {
        return $authUser->can('Restore:Buku');
    }

    public function forceDelete(AuthUser $authUser, Buku $buku): bool
    {
        return $authUser->can('ForceDelete:Buku');
    }
}
