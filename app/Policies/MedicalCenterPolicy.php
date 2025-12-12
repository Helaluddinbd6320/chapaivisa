<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MedicalCenter;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalCenterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedicalCenter');
    }

    public function view(AuthUser $authUser, MedicalCenter $medicalCenter): bool
    {
        return $authUser->can('View:MedicalCenter');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedicalCenter');
    }

    public function update(AuthUser $authUser, MedicalCenter $medicalCenter): bool
    {
        return $authUser->can('Update:MedicalCenter');
    }

    public function delete(AuthUser $authUser, MedicalCenter $medicalCenter): bool
    {
        return $authUser->can('Delete:MedicalCenter');
    }

    public function restore(AuthUser $authUser, MedicalCenter $medicalCenter): bool
    {
        return $authUser->can('Restore:MedicalCenter');
    }

    public function forceDelete(AuthUser $authUser, MedicalCenter $medicalCenter): bool
    {
        return $authUser->can('ForceDelete:MedicalCenter');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedicalCenter');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedicalCenter');
    }

    public function replicate(AuthUser $authUser, MedicalCenter $medicalCenter): bool
    {
        return $authUser->can('Replicate:MedicalCenter');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedicalCenter');
    }

}