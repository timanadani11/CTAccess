<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CeladorDashboardController extends Controller
{
    public function __construct()
    {
        // Asegurar guard del sistema
        $this->middleware('auth:system');
    }

    public function index()
    {
        $user = Auth::guard('system')->user();
        if (! $user || ! method_exists($user, 'isCelador') || ! $user->isCelador()) {
            abort(403, 'No autorizado');
        }

        return Inertia::render('System/Celador/Dashboard');
    }
}
