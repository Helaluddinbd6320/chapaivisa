<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Visa;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Visa');
    }

    public function view(AuthUser $authUser, Visa $visa): bool
    {
        return $authUser->can('View:Visa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Visa');
    }

    public function update(AuthUser $authUser, Visa $visa): bool
    {
        return $authUser->can('Update:Visa');
    }

    public function delete(AuthUser $authUser, Visa $visa): bool
    {
        return $authUser->can('Delete:Visa');
    }

    public function restore(AuthUser $authUser, Visa $visa): bool
    {
        return $authUser->can('Restore:Visa');
    }

    public function forceDelete(AuthUser $authUser, Visa $visa): bool
    {
        return $authUser->can('ForceDelete:Visa');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Visa');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Visa');
    }

    public function replicate(AuthUser $authUser, Visa $visa): bool
    {
        return $authUser->can('Replicate:Visa');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Visa');
    }

}