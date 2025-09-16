<?php

use Illuminate\Support\Facades\Route;
use App\Models\Persona;

Route::get('/', function () {
    return view('welcome');
});

// Ruta de depuración para visualizar una Persona y sus relaciones sin frontend
Route::get('/debug/personas/{persona}', function (Persona $persona) {
    $persona->load(['portatiles', 'vehiculos']);
    return view('debug.persona', compact('persona'));
});
