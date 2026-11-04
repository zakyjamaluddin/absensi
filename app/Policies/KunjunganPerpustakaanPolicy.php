<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KunjunganPerpustakaan;
use Illuminate\Auth\Access\HandlesAuthorization;

class KunjunganPerpustakaanPolicy
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
        return $authUser->can('ViewAny:KunjunganPerpustakaan');
    }

    public function view(AuthUser $authUser, KunjunganPerpustakaan $kunjungan): bool
    {
        return $authUser->can('View:KunjunganPerpustakaan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KunjunganPerpustakaan');
    }

    public function update(AuthUser $authUser, KunjunganPerpustakaan $kunjungan): bool
    {
        return $authUser->can('Update:KunjunganPerpustakaan');
    }

    public function delete(AuthUser $authUser, KunjunganPerpustakaan $kunjungan): bool
    {
        return $authUser->can('Delete:KunjunganPerpustakaan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KunjunganPerpustakaan');
    }

    public function restore(AuthUser $authUser, KunjunganPerpustakaan $kunjungan): bool
    {
        return $authUser->can('Restore:KunjunganPerpustakaan');
    }

    public function forceDelete(AuthUser $authUser, KunjunganPerpustakaan $kunjungan): bool
    {
        return $authUser->can('ForceDelete:KunjunganPerpustakaan');
    }
}
