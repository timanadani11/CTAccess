<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('check.system.role:administrador') or ->middleware('check.system.role:celador')
     * 
     * IMPORTANTE: El administrador tiene acceso a TODO, incluyendo rutas de celador.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @var \App\Models\UsuarioSistema|null $user */
        $user = Auth::guard('system')->user();

        if (! $user) {
            abort(401);
        }

        // Rely exclusively on RBAC helper hasRole()
        if (! method_exists($user, 'hasRole')) {
            abort(500, 'RBAC no disponible en el modelo de usuario del sistema');
        }

        // ✅ ADMIN TIENE ACCESO A TODO
        // Si el usuario es administrador, siempre tiene acceso
        if ($user->hasRole('administrador')) {
            return $next($request);
        }

        // Si no es administrador, verificar el rol específico
        $hasRole = $user->hasRole($role);

        if (! $hasRole) {
            abort(403, 'No autorizado');
        }

        return $next($request);
    }
}
