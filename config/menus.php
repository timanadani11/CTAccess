<?php

return [
    'system' => [
        'administrador' => [
            [
                'label' => 'Dashboard',
                'icon'  => 'heroicon-m-home',
                'route' => 'system.admin.dashboard',
            ],
            // Ejemplo de item controlado por Gate/Policy
            [
                'label' => 'Gestión de Usuarios',
                'icon'  => 'heroicon-m-users',
                'route' => 'system.admin.users.index',
                'can'   => 'manage-users',
            ],
        ],
        'celador' => [
            [
                'label' => 'Dashboard',
                'icon'  => 'heroicon-m-home',
                'route' => 'system.celador.dashboard',
            ],
            [
                'label' => 'Personas',
                'icon'  => 'heroicon-m-users',
                'route' => 'system.celador.personas.index',
            ],
            [
                'label' => 'Accesos',
                'icon'  => 'heroicon-m-key',
                'route' => 'system.celador.accesos.index',
                'can'   => 'accesos.view',
            ],
            [
                'label' => 'Verificación QR',
                'icon'  => 'heroicon-m-qr-code',
                'route' => 'system.celador.qr',
            ],
            [
                'label' => 'Incidencias',
                'icon'  => 'heroicon-m-exclamation-triangle',
                'route' => 'system.celador.incidencias.index',
                'can'   => 'incidencias.view',
            ],
            [
                'label' => 'Historial',
                'icon'  => 'heroicon-m-archive-box',
                'route' => 'system.celador.historial.index',
            ],
        ],
    ],
];
