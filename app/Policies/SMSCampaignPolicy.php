<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SMSCampaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class SMSCampaignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SMSCampaign');
    }

    public function view(AuthUser $authUser, SMSCampaign $sMSCampaign): bool
    {
        return $authUser->can('View:SMSCampaign');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SMSCampaign');
    }

    public function update(AuthUser $authUser, SMSCampaign $sMSCampaign): bool
    {
        return $authUser->can('Update:SMSCampaign');
    }

    public function delete(AuthUser $authUser, SMSCampaign $sMSCampaign): bool
    {
        return $authUser->can('Delete:SMSCampaign');
    }

    public function restore(AuthUser $authUser, SMSCampaign $sMSCampaign): bool
    {
        return $authUser->can('Restore:SMSCampaign');
    }

    public function forceDelete(AuthUser $authUser, SMSCampaign $sMSCampaign): bool
    {
        return $authUser->can('ForceDelete:SMSCampaign');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SMSCampaign');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SMSCampaign');
    }

    public function replicate(AuthUser $authUser, SMSCampaign $sMSCampaign): bool
    {
        return $authUser->can('Replicate:SMSCampaign');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SMSCampaign');
    }

}