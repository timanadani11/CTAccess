<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class QrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:system');
    }

    public function index()
    {
        return Inertia::render('System/Celador/Qr/Index');
    }
}
