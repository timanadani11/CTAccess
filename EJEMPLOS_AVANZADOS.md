# 🎓 Ejemplos Avanzados de WebSockets con Laravel Reverb

## 📋 Índice de Ejemplos

1. [Canales Privados](#1-canales-privados)
2. [Canales de Presencia](#2-canales-de-presencia)
3. [Autenticación de Canales](#3-autenticación-de-canales)
4. [Notificaciones en Tiempo Real](#4-notificaciones-en-tiempo-real)
5. [Chat en Tiempo Real](#5-chat-en-tiempo-real)
6. [Actualización de Registros](#6-actualización-de-registros)
7. [Uso de Queues con Broadcasting](#7-uso-de-queues-con-broadcasting)
8. [Broadcasting Condicional](#8-broadcasting-condicional)

---

## 1. Canales Privados

### Backend - Definir Canal Privado

**routes/channels.php**
```php
<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\UsuarioSistema;

// Canal privado: solo el usuario autenticado puede escuchar sus propias notificaciones
Broadcast::channel('notifications.{userId}', function (UsuarioSistema $user, $userId) {
    return $user->idUsuario === (int) $userId;
});

// Canal privado por área: solo usuarios de esa área pueden escuchar
Broadcast::channel('area.{areaId}', function (UsuarioSistema $user, $areaId) {
    return $user->area_id === (int) $areaId;
});
```

### Evento con Canal Privado

**app/Events/NotificacionPrivada.php**
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificacionPrivada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;
    public $userId;

    public function __construct($mensaje, $userId)
    {
        $this->mensaje = $mensaje;
        $this->userId = $userId;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'notificacion.recibida';
    }

    public function broadcastWith(): array
    {
        return [
            'mensaje' => $this->mensaje,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
```

### Frontend - Escuchar Canal Privado

**resources/js/components/Notificaciones.vue**
```javascript
<template>
    <div class="notifications">
        <div v-for="notif in notificaciones" :key="notif.id" class="notification">
            {{ notif.mensaje }}
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            notificaciones: [],
            userId: null
        }
    },
    mounted() {
        this.userId = document.querySelector('[data-user-id]').dataset.userId;
        
        // Suscribirse al canal PRIVADO (requiere autenticación)
        window.Echo.private(`notifications.${this.userId}`)
            .listen('.notificacion.recibida', (data) => {
                this.notificaciones.push({
                    id: Date.now(),
                    mensaje: data.mensaje
                });
            });
    }
}
</script>
```

---

## 2. Canales de Presencia

### Backend - Canal de Presencia

**routes/channels.php**
```php
<?php

use Illuminate\Support\Facades\Broadcast;

// Canal de presencia: muestra quién está conectado
Broadcast::channel('chat-room', function ($user) {
    if ($user) {
        return [
            'id' => $user->idUsuario,
            'nombre' => $user->nombre,
            'rol' => $user->rol,
        ];
    }
});
```

### Frontend - Presencia de Usuarios

**resources/js/components/ChatRoom.vue**
```javascript
<template>
    <div class="chat-room">
        <!-- Lista de usuarios conectados -->
        <div class="usuarios-conectados">
            <h3>En línea ({{ usuariosConectados.length }})</h3>
            <ul>
                <li v-for="usuario in usuariosConectados" :key="usuario.id">
                    <span class="status-dot green"></span>
                    {{ usuario.nombre }} ({{ usuario.rol }})
                </li>
            </ul>
        </div>
        
        <!-- Mensajes del chat -->
        <div class="chat-messages">
            <div v-for="msg in mensajes" :key="msg.id">
                <strong>{{ msg.usuario }}:</strong> {{ msg.mensaje }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            usuariosConectados: [],
            mensajes: []
        }
    },
    mounted() {
        // Unirse al canal de presencia
        window.Echo.join('chat-room')
            // Usuarios que ya están conectados
            .here((users) => {
                console.log('Usuarios actuales:', users);
                this.usuariosConectados = users;
            })
            // Alguien se une
            .joining((user) => {
                console.log('Usuario conectado:', user.nombre);
                this.usuariosConectados.push(user);
                this.mensajes.push({
                    id: Date.now(),
                    usuario: 'Sistema',
                    mensaje: `${user.nombre} se ha unido al chat`
                });
            })
            // Alguien se va
            .leaving((user) => {
                console.log('Usuario desconectado:', user.nombre);
                this.usuariosConectados = this.usuariosConectados.filter(
                    u => u.id !== user.id
                );
                this.mensajes.push({
                    id: Date.now(),
                    usuario: 'Sistema',
                    mensaje: `${user.nombre} ha salido del chat`
                });
            })
            // Escuchar mensajes del chat
            .listen('.mensaje.nuevo', (data) => {
                this.mensajes.push(data);
            });
    }
}
</script>
```

---

## 3. Autenticación de Canales

### Backend - Middleware de Broadcasting

**routes/channels.php**
```php
<?php

use Illuminate\Support\Facades\Broadcast;

// Canal privado con validación compleja
Broadcast::channel('acceso.celador.{celadorId}', function ($user, $celadorId) {
    // Solo celadores pueden acceder a este canal
    if ($user->rol !== 'celador') {
        return false;
    }
    
    // Solo puede acceder a su propio canal
    if ($user->idUsuario !== (int) $celadorId) {
        return false;
    }
    
    // Verificar que esté en turno activo
    $turnoActivo = \App\Models\Turno::where('celador_id', $user->idUsuario)
        ->where('activo', true)
        ->exists();
    
    return $turnoActivo;
});
```

### Frontend - Configurar Headers de Autenticación

**resources/js/echo.js**
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    
    // Configurar autenticación
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
        }
    },
    
    // Endpoint de autenticación
    authEndpoint: '/broadcasting/auth',
});
```

---

## 4. Notificaciones en Tiempo Real

### Backend - Sistema de Notificaciones

**app/Events/NuevaNotificacion.php**
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevaNotificacion implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notificacion;

    public function __construct($notificacion)
    {
        $this->notificacion = $notificacion;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->notificacion->user_id);
    }

    public function broadcastAs(): string
    {
        return 'notificacion.nueva';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notificacion->id,
            'tipo' => $this->notificacion->tipo,
            'titulo' => $this->notificacion->titulo,
            'mensaje' => $this->notificacion->mensaje,
            'icono' => $this->notificacion->icono,
            'url' => $this->notificacion->url,
            'leida' => false,
            'timestamp' => $this->notificacion->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
```

**app/Services/NotificacionService.php**
```php
<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Events\NuevaNotificacion;

class NotificacionService
{
    public function notificar($userId, $tipo, $titulo, $mensaje, $url = null)
    {
        $notificacion = Notificacion::create([
            'user_id' => $userId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'url' => $url,
            'icono' => $this->getIcono($tipo),
        ]);

        // Emitir evento de broadcasting
        event(new NuevaNotificacion($notificacion));

        return $notificacion;
    }

    private function getIcono($tipo)
    {
        return match($tipo) {
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
            'info' => 'ℹ️',
            default => '🔔',
        };
    }
}
```

### Frontend - Widget de Notificaciones

**resources/js/components/NotificacionesWidget.vue**
```javascript
<template>
    <div class="notifications-widget">
        <!-- Badge con contador -->
        <button @click="toggleDropdown" class="notification-bell">
            🔔
            <span v-if="noLeidas > 0" class="badge">{{ noLeidas }}</span>
        </button>
        
        <!-- Dropdown de notificaciones -->
        <div v-if="mostrarDropdown" class="dropdown">
            <div class="dropdown-header">
                <h3>Notificaciones</h3>
                <button @click="marcarTodasLeidas">Marcar todas como leídas</button>
            </div>
            
            <div class="notifications-list">
                <div 
                    v-for="notif in notificaciones" 
                    :key="notif.id"
                    :class="['notification-item', { 'no-leida': !notif.leida }]"
                    @click="abrirNotificacion(notif)"
                >
                    <div class="icon">{{ notif.icono }}</div>
                    <div class="content">
                        <div class="titulo">{{ notif.titulo }}</div>
                        <div class="mensaje">{{ notif.mensaje }}</div>
                        <div class="timestamp">{{ formatearFecha(notif.timestamp) }}</div>
                    </div>
                </div>
                
                <div v-if="notificaciones.length === 0" class="empty">
                    No tienes notificaciones
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            notificaciones: [],
            mostrarDropdown: false,
            userId: null
        }
    },
    computed: {
        noLeidas() {
            return this.notificaciones.filter(n => !n.leida).length;
        }
    },
    mounted() {
        this.userId = this.$page.props.auth.user.id;
        this.cargarNotificaciones();
        this.escucharNuevasNotificaciones();
    },
    methods: {
        cargarNotificaciones() {
            axios.get('/api/notificaciones')
                .then(response => {
                    this.notificaciones = response.data;
                });
        },
        
        escucharNuevasNotificaciones() {
            window.Echo.private(`notifications.${this.userId}`)
                .listen('.notificacion.nueva', (data) => {
                    // Agregar al inicio
                    this.notificaciones.unshift(data);
                    
                    // Mostrar notificación nativa del navegador
                    if (Notification.permission === 'granted') {
                        new Notification(data.titulo, {
                            body: data.mensaje,
                            icon: data.icono
                        });
                    }
                    
                    // Reproducir sonido
                    this.reproducirSonido();
                });
        },
        
        toggleDropdown() {
            this.mostrarDropdown = !this.mostrarDropdown;
        },
        
        abrirNotificacion(notif) {
            if (!notif.leida) {
                axios.post(`/api/notificaciones/${notif.id}/marcar-leida`)
                    .then(() => {
                        notif.leida = true;
                    });
            }
            
            if (notif.url) {
                window.location.href = notif.url;
            }
        },
        
        marcarTodasLeidas() {
            axios.post('/api/notificaciones/marcar-todas-leidas')
                .then(() => {
                    this.notificaciones.forEach(n => n.leida = true);
                });
        },
        
        reproducirSonido() {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play();
        },
        
        formatearFecha(timestamp) {
            // Implementar lógica de formato
            return moment(timestamp).fromNow();
        }
    }
}
</script>
```

---

## 5. Chat en Tiempo Real

### Backend - Evento de Mensaje

**app/Events/MensajeEnviado.php**
```php
<?php

namespace App\Events;

use App\Models\Mensaje;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajeEnviado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;

    public function __construct(Mensaje $mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('chat.' . $this->mensaje->room_id);
    }

    public function broadcastAs(): string
    {
        return 'mensaje.enviado';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->mensaje->id,
            'usuario' => [
                'id' => $this->mensaje->usuario->idUsuario,
                'nombre' => $this->mensaje->usuario->nombre,
            ],
            'texto' => $this->mensaje->texto,
            'timestamp' => $this->mensaje->created_at->format('H:i'),
        ];
    }
}
```

### Controlador de Chat

**app/Http/Controllers/ChatController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Events\MensajeEnviado;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function enviarMensaje(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer',
            'texto' => 'required|string|max:1000',
        ]);

        $mensaje = Mensaje::create([
            'room_id' => $validated['room_id'],
            'user_id' => auth()->id(),
            'texto' => $validated['texto'],
        ]);

        // Emitir evento
        event(new MensajeEnviado($mensaje));

        return response()->json([
            'success' => true,
            'mensaje' => $mensaje
        ]);
    }
}
```

---

## 6. Actualización de Registros

### Evento de Actualización en Tiempo Real

**app/Events/RegistroActualizado.php**
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistroActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tabla;
    public $id;
    public $datos;
    public $usuarioQueActualizo;

    public function __construct($tabla, $id, $datos, $usuario)
    {
        $this->tabla = $tabla;
        $this->id = $id;
        $this->datos = $datos;
        $this->usuarioQueActualizo = $usuario;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('actualizaciones.' . $this->tabla);
    }

    public function broadcastAs(): string
    {
        return 'registro.actualizado';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'datos' => $this->datos,
            'actualizado_por' => $this->usuarioQueActualizo,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
```

### Frontend - Sincronización de Tabla

```javascript
window.Echo.channel('actualizaciones.personas')
    .listen('.registro.actualizado', (data) => {
        // Buscar el registro en la tabla
        const index = this.personas.findIndex(p => p.id === data.id);
        
        if (index !== -1) {
            // Actualizar el registro existente
            this.personas[index] = {
                ...this.personas[index],
                ...data.datos,
                actualizado_por: data.actualizado_por
            };
            
            // Mostrar notificación
            this.mostrarNotificacion(`Registro actualizado por ${data.actualizado_por}`);
        }
    });
```

---

## 7. Uso de Queues con Broadcasting

### Evento con Queue

**app/Events/ProcesoCompletado.php**
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Broadcast inmediato
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Broadcast con queue
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcesoCompletado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $resultado;

    public function __construct($resultado)
    {
        $this->resultado = $resultado;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('procesos');
    }

    // Especificar queue y conexión
    public function broadcastQueue(): string
    {
        return 'broadcasts';
    }

    public function broadcastConnection(): string
    {
        return 'redis';
    }
}
```

---

## 8. Broadcasting Condicional

### Evento con Condición

**app/Events/AccesoRegistrado.php**
```php
<?php

namespace App\Events;

use App\Models\Acceso;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccesoRegistrado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $acceso;

    public function __construct(Acceso $acceso)
    {
        $this->acceso = $acceso;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('accesos')  // Canal general
        ];

        // Solo broadcastear al canal del área si el tipo es sospechoso
        if ($this->acceso->tipo_persona === 'Visitante') {
            $channels[] = new Channel('alertas.seguridad');
        }

        return $channels;
    }

    public function broadcastWhen(): bool
    {
        // Solo broadcastear si es un acceso nuevo (no salida)
        return $this->acceso->estado === 'activo';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->acceso->idAcceso,
            'persona' => [
                'nombre' => $this->acceso->persona->Nombre,
                'documento' => $this->acceso->persona->documento,
                'tipo' => $this->acceso->persona->TipoPersona,
            ],
            'hora_entrada' => $this->acceso->horaEntrada->format('H:i:s'),
            'alerta' => $this->acceso->tipo_persona === 'Visitante',
        ];
    }
}
```

---

## 📚 Recursos Adicionales

- **Broadcasting Privado:** https://laravel.com/docs/broadcasting#private-channels
- **Canales de Presencia:** https://laravel.com/docs/broadcasting#presence-channels
- **Autenticación:** https://laravel.com/docs/broadcasting#authorizing-channels
- **Laravel Echo:** https://github.com/laravel/echo

---

**💡 Estos ejemplos son plantillas reutilizables para casos de uso avanzados.**
