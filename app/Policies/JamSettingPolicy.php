<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JamSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class JamSettingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JamSetting');
    }

    public function view(AuthUser $authUser, JamSetting $jamSetting): bool
    {
        return $authUser->can('View:JamSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JamSetting');
    }

    public function update(AuthUser $authUser, JamSetting $jamSetting): bool
    {
        return $authUser->can('Update:JamSetting');
    }

    public function delete(AuthUser $authUser, JamSetting $jamSetting): bool
    {
        return $authUser->can('Delete:JamSetting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JamSetting');
    }

    public function restore(AuthUser $authUser, JamSetting $jamSetting): bool
    {
        return $authUser->can('Restore:JamSetting');
    }

    public function forceDelete(AuthUser $authUser, JamSetting $jamSetting): bool
    {
        return $authUser->can('ForceDelete:JamSetting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JamSetting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JamSetting');
    }

    public function replicate(AuthUser $authUser, JamSetting $jamSetting): bool
    {
        return $authUser->can('Replicate:JamSetting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JamSetting');
    }

}