<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EmailCampaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailCampaignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmailCampaign');
    }

    public function view(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('View:EmailCampaign');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmailCampaign');
    }

    public function update(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('Update:EmailCampaign');
    }

    public function delete(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('Delete:EmailCampaign');
    }

    public function restore(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('Restore:EmailCampaign');
    }

    public function forceDelete(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('ForceDelete:EmailCampaign');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmailCampaign');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmailCampaign');
    }

    public function replicate(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('Replicate:EmailCampaign');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmailCampaign');
    }

}