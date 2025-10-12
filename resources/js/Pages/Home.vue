<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, nextTick } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Icon from '@/Components/Icon.vue'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  estadisticas: Object,
  sistema_info: Object
})

// Estado para actividad reciente
const recentActivity = ref([])
const loadingActivity = ref(true)

// Tema
const { isDark, toggleTheme } = useTheme()

// Reloj Digital
const currentTime = ref(new Date())
const updateClock = () => {
  currentTime.value = new Date()
}

// Formatear hora
const formatTime = (date) => {
  return date.toLocaleTimeString('es-ES', { 
    hour: '2-digit', 
    minute: '2-digit', 
    second: '2-digit',
    hour12: false 
  })
}

// Formatear fecha
const formatDate = (date) => {
  return date.toLocaleDateString('es-ES', { 
    weekday: 'short', 
    day: '2-digit', 
    month: 'short'
  })
}

// Configurar WebSocket y cargar datos
onMounted(() => {
  // Cargar actividad reciente inicial
  fetchRecentActivity()
  
  // Iniciar reloj
  setInterval(updateClock, 1000)

  // 🔥 WEBSOCKET: Escuchar nuevos accesos en tiempo real
  if (typeof window.Echo !== 'undefined') {
    window.Echo.channel('accesos')
      .listen('.acceso.registrado', (data) => {
        console.log('Nuevo acceso registrado:', data)
        
        const newAccess = {
          id: data.id,
          persona: data.persona.nombre,
          documento: data.persona.documento,
          tipo: data.tipo_acceso,
          tiempo: new Date(data.timestamp),
          isNew: true // Marcador para animación
        }
        
        // Agregar al inicio de la actividad reciente
        recentActivity.value.unshift(newAccess)
        
        // Mantener solo los últimos 15
        if (recentActivity.value.length > 15) {
          recentActivity.value = recentActivity.value.slice(0, 15)
        }

        // Quitar el marcador "isNew" después de 5 segundos
        setTimeout(() => {
          const index = recentActivity.value.findIndex(a => a.id === newAccess.id)
          if (index !== -1) {
            recentActivity.value[index].isNew = false
          }
        }, 5000)
      })
  } else {
    console.warn('⚠️ Laravel Echo no está disponible. WebSockets deshabilitados.')
  }
})

// 🔥 Actividad en Tiempo Real con WebSockets
const fetchRecentActivity = async () => {
  try {
    const response = await fetch('/api/accesos/recientes')
    const data = await response.json()
    recentActivity.value = data
    loadingActivity.value = false
  } catch (error) {
    console.error('Error fetching recent activity:', error)
    loadingActivity.value = false
  }
}

// Formatear tiempo relativo
const formatRelativeTime = (date) => {
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  
  if (minutes < 1) return 'Ahora'
  if (minutes < 60) return `${minutes}m`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours}h`
  return `${Math.floor(hours / 24)}d`
}
</script>

<template>
  <Head title="CTAccess - Sistema de Control de Acceso" />

  <div class="min-h-screen bg-theme-primary text-theme-primary flex flex-col">
    <!-- Header fijo -->
    <header class="bg-theme-navbar border-b border-theme-primary px-4 py-3 flex-shrink-0">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-4">
          <div class="relative">
            <ApplicationLogo 
              alt="CTAccess Logo" 
              classes="h-12 w-auto object-contain"
            />
          </div>
          
          <!-- Reloj Digital -->
          <div class="hidden md:flex items-center gap-2 bg-theme-secondary border-2 border-theme-primary rounded-lg px-3 py-1.5 shadow-lg">
            <Icon name="clock" :size="16" class="text-blue-500 dark:text-blue-400" />
            <div class="flex flex-col">
              <div class="text-theme-primary font-bold text-sm tabular-nums leading-tight digital-clock">
                {{ formatTime(currentTime) }}
              </div>
              <div class="text-theme-muted text-[10px] font-medium uppercase leading-tight">
                {{ formatDate(currentTime) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Navegación -->
        <nav class="flex gap-2 items-center">
          <!-- Theme toggle button -->
          <button
            @click="toggleTheme"
            class="rounded-md p-2 text-theme-muted hover:bg-theme-tertiary hover:text-theme-secondary focus:outline-none focus:ring-2 focus:ring-green-500"
            :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          >
            <Icon :name="isDark ? 'sun' : 'moon'" :size="20" />
          </button>
          
          <template v-if="$page.props.auth && $page.props.auth.user">
            <Link 
              :href="route('dashboard')" 
              class="flex items-center gap-2 px-3 py-2 text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-all duration-200"
            >
              <Icon name="home" :size="16" />
              <span class="hidden sm:inline">Panel</span>
            </Link>
          </template>
          <template v-else>
            <Link 
              :href="route('login')" 
              class="flex items-center gap-2 px-3 py-2 text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-all duration-200"
            >
              <Icon name="log-in" :size="16" />
              <span class="hidden sm:inline">Iniciar Sesión</span>
            </Link>
            <Link 
              :href="route('personas.create')" 
              class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium"
            >
              <Icon name="user-plus" :size="16" />
              Registrarse
            </Link>
          </template>
        </nav>
      </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-2 px-4 py-2 overflow-y-auto">
      <div class="max-w-7xl mx-auto h-full">
        
        <!-- 🔥 Actividad en Tiempo Real - Diseño Compacto -->
        <div class="relative w-fit mr-auto">
          <!-- Título Diagonal en Esquina -->
          <div class="absolute -top-12 -left-6 z-10">
            <div class="bg-slate-700 dark:bg-slate-800 px-6 py-3 rounded-xl shadow-2xl border-2 border-slate-600 dark:border-slate-700 transform -rotate-2 hover:rotate-0 transition-transform duration-300">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-slate-600 dark:bg-slate-700 rounded-lg flex items-center justify-center relative">
                  <Icon name="activity" :size="18" class="text-white" />
                  <div class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-slate-700 dark:border-slate-800 live-indicator"></div>
                </div>
                <div>
                  <h3 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
                    Actividad reciente
                    <span class="text-xs font-normal text-slate-300">{{ recentActivity.length }} registros</span>
                  </h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Contenedor de la Tabla -->
          <div class="bg-theme-card border-2 border-theme-primary rounded-xl shadow-xl overflow-hidden mt-12 w-fit">
            <!-- Feed de Registros Compacto -->
            <div class="p-2 bg-theme-secondary">
              <div class="space-y-1.5 max-h-[480px] overflow-y-auto custom-scrollbar pr-1">
              
              <!-- Loading State -->
              <template v-if="loadingActivity">
                <div v-for="i in 4" :key="i" class="flex items-center gap-2 p-1.5 bg-theme-card border border-theme-primary rounded-lg animate-pulse w-fit min-w-[400px]">
                  <div class="w-8 h-8 bg-theme-tertiary rounded-lg"></div>
                  <div class="flex-1 space-y-1">
                    <div class="h-2.5 bg-theme-tertiary rounded w-32"></div>
                    <div class="h-2 bg-theme-tertiary rounded w-24"></div>
                  </div>
                  <div class="w-9 h-6 bg-theme-tertiary rounded"></div>
                </div>
              </template>

              <!-- Lista de Actividades -->
              <template v-else-if="recentActivity.length > 0">
                <transition-group name="spotlight">
                  <div 
                    v-for="activity in recentActivity" 
                    :key="activity.id"
                    :class="[
                      'relative flex items-center gap-2 p-1.5 rounded-lg border-2 transition-all duration-500 w-fit min-w-[400px]',
                      activity.isNew 
                        ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-400 dark:border-yellow-600 spotlight-card' 
                        : 'bg-theme-card border-theme-primary hover:border-slate-400 dark:hover:border-slate-600 hover:shadow-lg',
                      'cursor-pointer group'
                    ]"
                  >
                    <!-- Spotlight Effect para nuevos (4 esquinas brillantes) -->
                    <div v-if="activity.isNew" class="corner-spotlight top-left"></div>
                    <div v-if="activity.isNew" class="corner-spotlight top-right"></div>
                    <div v-if="activity.isNew" class="corner-spotlight bottom-left"></div>
                    <div v-if="activity.isNew" class="corner-spotlight bottom-right"></div>
                    
                    <!-- Avatar Mate con Icono -->
                    <div class="relative flex-shrink-0">
                      <div 
                        :class="[
                          'w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold border-2 transition-all duration-300',
                          activity.tipo === 'entrada' 
                            ? 'bg-green-500 border-green-600 dark:bg-green-600 dark:border-green-700' 
                            : 'bg-red-500 border-red-600 dark:bg-red-600 dark:border-red-700',
                          activity.isNew ? 'scale-110 shake' : 'group-hover:scale-105'
                        ]"
                      >
                        <Icon :name="activity.tipo === 'entrada' ? 'log-in' : 'log-out'" :size="14" />
                      </div>
                      <!-- Notificación Badge Animado -->
                      <div 
                        v-if="activity.isNew"
                        class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-yellow-400 border-2 border-white dark:border-gray-900 rounded-full flex items-center justify-center notification-badge"
                      >
                        <span class="text-[7px] font-black text-yellow-900">!</span>
                      </div>
                    </div>

                    <!-- Información Compacta -->
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-1.5 mb-0.5">
                        <p :class="[
                          'font-bold text-[13px] truncate',
                          activity.isNew ? 'text-yellow-900 dark:text-yellow-200' : 'text-theme-primary'
                        ]">
                          {{ activity.persona }}
                        </p>
                        <!-- Badge "NUEVO" llamativo -->
                        <span 
                          v-if="activity.isNew"
                          class="px-1.5 py-0.5 bg-yellow-400 text-yellow-900 text-[8px] font-black rounded uppercase tracking-wider border border-yellow-500 blink-badge"
                        >
                          ¡Nuevo!
                        </span>
                      </div>
                      <div class="flex items-center gap-1.5 text-[10px]">
                        <Icon name="credit-card" :size="9" :class="activity.isNew ? 'text-yellow-700 dark:text-yellow-400' : 'text-theme-muted'" />
                        <span :class="activity.isNew ? 'text-yellow-800 dark:text-yellow-300 font-semibold' : 'text-theme-muted'">
                          {{ activity.documento }}
                        </span>
                        <span class="text-theme-muted text-[8px]">•</span>
                        <Icon name="clock" :size="9" :class="activity.isNew ? 'text-yellow-700 dark:text-yellow-400' : 'text-theme-muted'" />
                        <span :class="[
                          'font-semibold',
                          activity.isNew ? 'text-yellow-800 dark:text-yellow-300' : 'text-theme-muted'
                        ]">
                          {{ formatRelativeTime(activity.tiempo) }}
                        </span>
                      </div>
                    </div>

                    <!-- Badge de Estado Ultra Compacto -->
                    <div class="flex-shrink-0">
                      <div 
                        :class="[
                          'px-1.5 py-0.5 rounded text-[9px] font-black uppercase border-2 transition-transform duration-300',
                          activity.tipo === 'entrada' 
                            ? 'bg-green-500 text-white border-green-600 dark:bg-green-600 dark:border-green-700' 
                            : 'bg-red-500 text-white border-red-600 dark:bg-red-600 dark:border-red-700',
                          'group-hover:scale-105'
                        ]"
                      >
                        <div class="flex items-center gap-0.5">
                          <div 
                            :class="[
                              'w-1 h-1 rounded-full bg-white',
                              activity.isNew ? 'pulse-dot' : ''
                            ]"
                          ></div>
                          {{ activity.tipo === 'entrada' ? 'IN' : 'OUT' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </transition-group>
              </template>

              <!-- Empty State -->
              <template v-else>
                <div class="text-center py-12 text-theme-muted">
                  <div class="w-14 h-14 mx-auto mb-3 bg-theme-tertiary rounded-xl flex items-center justify-center border-2 border-theme-primary">
                    <Icon name="inbox" :size="28" class="opacity-40" />
                  </div>
                  <p class="text-sm font-bold">Sin actividad reciente</p>
                  <p class="text-xs mt-1 opacity-70">Los accesos aparecerán aquí automáticamente</p>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>

      </div>
    </main>

    <!-- Footer fijo -->
    <footer class="bg-theme-navbar border-t border-theme-primary px-4 py-3 flex-shrink-0">
      <div class="max-w-7xl mx-auto text-center">
        <p class="text-xs text-theme-muted">© {{ new Date().getFullYear() }} CTAccess - v{{ sistema_info?.version || '1.0' }}</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* ⚡ ANIMACIÓN SPOTLIGHT - Efecto super llamativo para registros nuevos */
.spotlight-enter-active {
  animation: spotlightEntrance 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.spotlight-leave-active {
  animation: spotlightExit 0.5s ease-in-out;
  position: absolute;
  width: 100%;
}

.spotlight-move {
  transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* 🎬 Entrada dramática con zoom y rotación */
@keyframes spotlightEntrance {
  0% {
    opacity: 0;
    transform: translateX(150px) scale(0.3) rotate(15deg);
    filter: brightness(3);
  }
  50% {
    transform: translateX(-20px) scale(1.15) rotate(-2deg);
    filter: brightness(1.5);
  }
  70% {
    transform: translateX(5px) scale(0.95) rotate(1deg);
  }
  100% {
    opacity: 1;
    transform: translateX(0) scale(1) rotate(0);
    filter: brightness(1);
  }
}

@keyframes spotlightExit {
  from {
    opacity: 1;
    transform: scale(1);
  }
  to {
    opacity: 0;
    transform: scale(0.8) translateX(-50px);
  }
}

/* � Spotlight Card - Efecto de reflector en las 4 esquinas */
.spotlight-card {
  animation: cardPulse 2s ease-in-out infinite;
  position: relative;
}

@keyframes cardPulse {
  0%, 100% {
    box-shadow: 0 0 0 rgba(251, 191, 36, 0.5),
                0 8px 24px -4px rgba(251, 191, 36, 0.3);
  }
  50% {
    box-shadow: 0 0 30px rgba(251, 191, 36, 0.6),
                0 12px 32px -4px rgba(251, 191, 36, 0.4);
  }
}

/* 💡 Corner Spotlights - Luces en las esquinas */
.corner-spotlight {
  position: absolute;
  width: 16px;
  height: 16px;
  background: #fbbf24;
  opacity: 0;
  animation: cornerFlash 1.5s ease-in-out infinite;
}

.corner-spotlight.top-left {
  top: -2px;
  left: -2px;
  border-radius: 0 0 100% 0;
  animation-delay: 0s;
}

.corner-spotlight.top-right {
  top: -2px;
  right: -2px;
  border-radius: 0 0 0 100%;
  animation-delay: 0.3s;
}

.corner-spotlight.bottom-left {
  bottom: -2px;
  left: -2px;
  border-radius: 0 100% 0 0;
  animation-delay: 0.6s;
}

.corner-spotlight.bottom-right {
  bottom: -2px;
  right: -2px;
  border-radius: 100% 0 0 0;
  animation-delay: 0.9s;
}

@keyframes cornerFlash {
  0%, 100% {
    opacity: 0;
    transform: scale(0);
  }
  50% {
    opacity: 0.8;
    transform: scale(1);
  }
}

/* � Notification Badge - Badge amarillo animado */
.notification-badge {
  animation: badgeBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 3;
}

@keyframes badgeBounce {
  0%, 100% {
    transform: scale(1) rotate(0deg);
  }
  25% {
    transform: scale(1.3) rotate(-10deg);
  }
  75% {
    transform: scale(1.3) rotate(10deg);
  }
}

/* 🌀 Shake Animation - Avatar temblando */
.shake {
  animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) 3;
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0) scale(1.1);
  }
  25% {
    transform: translateX(-5px) rotate(-5deg) scale(1.15);
  }
  75% {
    transform: translateX(5px) rotate(5deg) scale(1.15);
  }
}

/* ⚡ Blink Badge - Parpadeo llamativo del badge "NUEVO" */
.blink-badge {
  animation: blinkScale 1s ease-in-out infinite;
}

@keyframes blinkScale {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.1);
  }
}

/* 🔴 Pulse Dot - Punto pulsante en badge de estado */
.pulse-dot {
  animation: pulseDot 1.5s ease-in-out infinite;
}

@keyframes pulseDot {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.5);
    opacity: 0.5;
  }
}

/* 🟢 Live Indicator - Indicador verde pulsante */
.live-indicator {
  animation: liveIndicator 2s ease-in-out infinite;
}

@keyframes liveIndicator {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.3);
    opacity: 0.7;
  }
}

/* 📐 Título Diagonal - Hover suave */
.transform.-rotate-2 {
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
}

.transform.-rotate-2:hover {
  box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.4);
}

/* 🕐 Reloj Digital - Estilo LED moderno */
.digital-clock {
  font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
  letter-spacing: 0.05em;
  text-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
  animation: digitGlow 2s ease-in-out infinite;
}

@keyframes digitGlow {
  0%, 100% {
    text-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
  }
  50% {
    text-shadow: 0 0 12px rgba(59, 130, 246, 0.8), 0 0 20px rgba(59, 130, 246, 0.4);
  }
}

/* Números con espaciado uniforme */
.tabular-nums {
  font-variant-numeric: tabular-nums;
}

/* 📜 Custom Scrollbar - Mate y moderno */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #3b82f6 transparent;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #3b82f6;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #2563eb;
}

/* 🎨 Hover effects para cards */
.group:hover {
  transform: translateY(-2px);
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* ✨ Transiciones suaves globales */
* {
  transition-property: transform, background-color, border-color, box-shadow, opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
</style>

