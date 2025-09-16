<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;

Route::prefix('v1')->group(function () {
    Route::apiResource('personas', PersonaController::class);
});
