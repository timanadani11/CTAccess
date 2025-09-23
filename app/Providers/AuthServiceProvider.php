<?php

namespace App\Providers;

use App\Models\Acceso;
use App\Policies\AccesoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Acceso::class => AccesoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Global check: if the user model implements hasPermission(), let it decide
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ? true : null; // null => continue to other gates
            }
            return null;
        });

        // Example Gate for admin-only features
        Gate::define('manage-users', function ($user) {
            // Support both guards, prefer RBAC helpers
            if (method_exists($user, 'isAdmin')) {
                return (bool) $user->isAdmin();
            }
            if (method_exists($user, 'hasRole')) {
                return (bool) $user->hasRole('administrador');
            }
            return false;
        });

        // Explicit gates commonly used in menus (redundant with before(), but clear)
        foreach ([
            'personas.view', 'personas.create', 'personas.update', 'personas.delete',
            'accesos.view', 'accesos.create', 'accesos.update',
            'incidencias.view', 'incidencias.create',
        ] as $ability) {
            Gate::define($ability, function ($user) use ($ability) {
                return method_exists($user, 'hasPermission') ? $user->hasPermission($ability) : false;
            });
        }
    }
}
