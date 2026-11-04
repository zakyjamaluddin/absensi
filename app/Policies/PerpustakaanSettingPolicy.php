<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PerpustakaanSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class PerpustakaanSettingPolicy
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
        return $authUser->can('ViewAny:PerpustakaanSetting');
    }

    public function view(AuthUser $authUser, PerpustakaanSetting $setting): bool
    {
        return $authUser->can('View:PerpustakaanSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PerpustakaanSetting');
    }

    public function update(AuthUser $authUser, PerpustakaanSetting $setting): bool
    {
        return $authUser->can('Update:PerpustakaanSetting');
    }

    public function delete(AuthUser $authUser, PerpustakaanSetting $setting): bool
    {
        return $authUser->can('Delete:PerpustakaanSetting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PerpustakaanSetting');
    }

    public function restore(AuthUser $authUser, PerpustakaanSetting $setting): bool
    {
        return $authUser->can('Restore:PerpustakaanSetting');
    }

    public function forceDelete(AuthUser $authUser, PerpustakaanSetting $setting): bool
    {
        return $authUser->can('ForceDelete:PerpustakaanSetting');
    }
}
