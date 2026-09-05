<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WaSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class WaSettingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WaSetting');
    }

    public function view(AuthUser $authUser, WaSetting $waSetting): bool
    {
        return $authUser->can('View:WaSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WaSetting');
    }

    public function update(AuthUser $authUser, WaSetting $waSetting): bool
    {
        return $authUser->can('Update:WaSetting');
    }

    public function delete(AuthUser $authUser, WaSetting $waSetting): bool
    {
        return $authUser->can('Delete:WaSetting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WaSetting');
    }

    public function restore(AuthUser $authUser, WaSetting $waSetting): bool
    {
        return $authUser->can('Restore:WaSetting');
    }

    public function forceDelete(AuthUser $authUser, WaSetting $waSetting): bool
    {
        return $authUser->can('ForceDelete:WaSetting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WaSetting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WaSetting');
    }

    public function replicate(AuthUser $authUser, WaSetting $waSetting): bool
    {
        return $authUser->can('Replicate:WaSetting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WaSetting');
    }

}