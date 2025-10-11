<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * ============================================
 * 🚀 CANAL PÚBLICO PARA ACCESOS EN TIEMPO REAL
 * ============================================
 * 
 * Este canal público permite que cualquier cliente escuche
 * los eventos de accesos en tiempo real.
 * 
 * Eventos que se emiten en este canal:
 * - acceso.registrado (cuando se registra entrada o salida)
 */
Broadcast::channel('accesos', function () {
    // Retornar true permite acceso público sin autenticación
    return true;
});
