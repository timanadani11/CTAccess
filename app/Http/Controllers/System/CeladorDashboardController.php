<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
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
        return Inertia::render('System/Celador/Dashboard');
    }
}
