<?php

namespace App\Policies;

use App\Models\Acceso;
use Illuminate\Contracts\Auth\Authenticatable;

class AccesoPolicy
{
    /**
     * Determine whether the user can view any Acceso models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        // Allow admins and celadores by convention (RBAC)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('administrador') || $user->hasRole('celador');
        }
        return false;
    }

    /**
     * Determine whether the user can view a specific Acceso model.
     */
    public function view(Authenticatable $user, Acceso $acceso): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create Acceso models.
     */
    public function create(Authenticatable $user): bool
    {
        // Example: both roles can create
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Acceso $acceso): bool
    {
        // Example rule; refine as needed
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Acceso $acceso): bool
    {
        // Example: only admin (RBAC)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('administrador');
        }
        return false;
    }
}
