<?php

namespace App\Http\Controllers\System\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('System/Auth/Login', [
            'canResetPassword' => false,
            'status' => session('status'),
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'UserName' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Nota: no usamos "remember me" porque la tabla no tiene remember_token
        if (Auth::guard('system')->attempt($credentials, false)) {
            $request->session()->regenerate();
            return redirect()->intended(route('system.panel'));
        }

        return back()->withErrors([
            'UserName' => 'Credenciales inválidas',
        ])->onlyInput('UserName');
    }

    public function destroy(Request $request)
    {
        Auth::guard('system')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('system.login');
    }
}
