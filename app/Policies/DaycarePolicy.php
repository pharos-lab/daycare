<?php

namespace App\Policies;

use App\Models\Daycare;
use App\Models\User;

class DaycarePolicy
{

    /**
     * Determine if the user can view the daycare.
     */
    public function view(User $user, Daycare $daycare): bool
    {
        // Admin can view any daycare
        if ($user->hasRole('admin')) {
            return true;
        }

        // User can view daycare if they are associated with it
        if ($daycare->hasUser($user->id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create daycares.
     */
    public function create(User $user): bool
    {
        // Only admin and directors can create daycares
        return $user->hasRole(['admin', 'director']);
    }

    /**
     * Determine if the user can update the daycare.
     */
    public function update(User $user, Daycare $daycare): bool
    {
        // Admin can update any daycare
        if ($user->hasRole('admin')) {
            return true;
        }

        // Director can only update their own daycares
        if ($user->hasRole('director') && $daycare->director_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the daycare.
     */
    public function delete(User $user, Daycare $daycare): bool
    {
        // Admin can delete any daycare
        if ($user->hasRole('admin')) {
            return true;
        }

        // Director can only delete their own daycares
        if ($user->hasRole('director') && $daycare->director_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can restore the daycare.
     */
    public function restore(User $user, Daycare $daycare): bool
    {
        // Only admin can restore deleted daycares
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete the daycare.
     */
    public function forceDelete(User $user, Daycare $daycare): bool
    {
        // Only admin can force delete
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can manage staff for the daycare.
     */
    public function manageStaff(User $user, Daycare $daycare): bool
    {
        // Admin can manage staff for any daycare
        if ($user->hasRole('admin')) {
            return true;
        }

        // Director can manage staff for their own daycares
        if ($user->hasRole('director') && $daycare->director_id === $user->id) {
            return true;
        }

        return false;
    }
}