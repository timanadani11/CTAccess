import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

/**
 * ============================================
 * 🚀 CONFIGURACIÓN DE LARAVEL ECHO + REVERB
 * ============================================
 * 
 * Este archivo configura Laravel Echo para conectarse al servidor
 * de WebSockets de Laravel Reverb en local.
 * 
 * Variables de entorno necesarias en .env:
 * - REVERB_APP_KEY
 * - REVERB_HOST
 * - REVERB_PORT
 * - REVERB_SCHEME
 */

// Asignar Pusher globalmente (requerido por Laravel Echo)
window.Pusher = Pusher;

// Configurar Laravel Echo con Reverb
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'testkey',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    cluster: 'mt1', // Requerido por pusher-js
});

// Log de conexión (útil para debugging)
console.log('🚀 Laravel Echo configurado con Reverb');
console.log('📡 WebSocket Server: ws://localhost:8080');

// Eventos de conexión/desconexión para debugging
setTimeout(() => {
    if (window.Echo.connector && window.Echo.connector.pusher) {
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Conectado al servidor WebSocket');
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.warn('❌ Desconectado del servidor WebSocket');
        });

        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('⚠️ Error en WebSocket:', error);
        });
        
        window.Echo.connector.pusher.connection.bind('unavailable', () => {
            console.error('⚠️ Servidor WebSocket no disponible');
        });
    }
}, 1000);

export default window.Echo;
