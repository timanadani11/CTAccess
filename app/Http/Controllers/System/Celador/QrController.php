<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:system');
    }

    protected function ensureRole(): void
    {
        $user = Auth::guard('system')->user();
        if (! $user || ! method_exists($user, 'isCelador') || ! $user->isCelador()) {
            abort(403, 'No autorizado');
        }
    }

    public function index()
    {
        $this->ensureRole();
        return Inertia::render('System/Celador/Qr/Index');
    }
}
