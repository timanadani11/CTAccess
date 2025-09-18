<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleCsrfErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Si es una petición AJAX/Inertia, devolver error JSON
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page.',
                    'errors' => [
                        'csrf' => ['The page has expired due to inactivity. Please refresh and try again.']
                    ]
                ], 419);
            }

            // Para peticiones normales, redirigir con mensaje
            return redirect()->back()
                ->withInput($request->except('password', 'contraseña'))
                ->withErrors(['csrf' => 'The page has expired due to inactivity. Please refresh and try again.']);
        }
    }
}
