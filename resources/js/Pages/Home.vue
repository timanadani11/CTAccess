<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Icon from '@/Components/Icon.vue'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  estadisticas: Object,
  sistema_info: Object
})

// Estado para animaciones y PWA
const isVisible = ref(false)
const currentTime = ref(new Date())
const deferredPrompt = ref(null)

// Estado para widgets dinámicos
const recentActivity = ref([])
const weeklyActivity = ref([])
const systemStatus = ref({})
const loadingActivity = ref(true)
const loadingWeekly = ref(true)
const loadingStatus = ref(true)

// Tema
const { isDark, toggleTheme } = useTheme()

// Actualizar hora cada segundo y configurar PWA
onMounted(() => {
  isVisible.value = true
  setInterval(() => {
    currentTime.value = new Date()
  }, 1000)

  // Escuchar evento beforeinstallprompt para PWA
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt.value = e
  })

  // Cargar datos dinámicos
  fetchRecentActivity()
  fetchWeeklyActivity()
  fetchSystemStatus()

  // Actualizar actividad reciente cada 30 segundos
  setInterval(() => {
    fetchRecentActivity()
  }, 30000)
})

// Formatear hora
const formatTime = (date) => {
  return date.toLocaleTimeString('es-CO', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

// Formatear fecha
const formatDate = (date) => {
  return date.toLocaleDateString('es-CO', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Instalar PWA
const installPWA = async () => {
  if (deferredPrompt.value) {
    deferredPrompt.value.prompt()
    const { outcome } = await deferredPrompt.value.userChoice
    console.log(`PWA install outcome: ${outcome}`)
    deferredPrompt.value = null
  } else {
    alert('Para instalar la app, usa el menú de tu navegador o busca la opción "Instalar app" o "Añadir a pantalla de inicio"')
  }
}

// 🔥 Actividad en Tiempo Real
const fetchRecentActivity = async () => {
  try {
    // Simulamos datos realistas hasta implementar la API real
    const activities = [
      { id: 1, persona: 'Juan Pérez', tipo: 'entrada', tiempo: new Date(Date.now() - Math.random() * 300000) },
      { id: 2, persona: 'María García', tipo: 'salida', tiempo: new Date(Date.now() - Math.random() * 600000) },
      { id: 3, persona: 'Carlos López', tipo: 'entrada', tiempo: new Date(Date.now() - Math.random() * 900000) },
      { id: 4, persona: 'Ana Rodríguez', tipo: 'salida', tiempo: new Date(Date.now() - Math.random() * 1200000) },
      { id: 5, persona: 'Luis Martínez', tipo: 'entrada', tiempo: new Date(Date.now() - Math.random() * 1500000) }
    ]
    
    setTimeout(() => {
      recentActivity.value = activities.sort((a, b) => b.tiempo - a.tiempo).slice(0, 5)
      loadingActivity.value = false
    }, 800)
    
    // API real cuando esté lista:
    // const response = await fetch('/api/accesos/recientes')
    // recentActivity.value = await response.json()
    // loadingActivity.value = false
  } catch (error) {
    console.error('Error fetching recent activity:', error)
    loadingActivity.value = false
  }
}

// 📊 Gráfico de Actividad Semanal
const fetchWeeklyActivity = async () => {
  try {
    const days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']
    const data = days.map(day => ({
      day,
      accesos: Math.floor(Math.random() * 50) + 10,
      maxAccesos: 60
    }))
    
    setTimeout(() => {
      weeklyActivity.value = data
      loadingWeekly.value = false
    }, 1200)
    
    // API real:
    // const response = await fetch('/api/estadisticas/semanal')
    // weeklyActivity.value = await response.json()
    // loadingWeekly.value = false
  } catch (error) {
    console.error('Error fetching weekly activity:', error)
    loadingWeekly.value = false
  }
}

// ⚡ Estado del Sistema
const fetchSystemStatus = async () => {
  try {
    const status = {
      sistema: { status: 'online', uptime: '99.9%', color: 'green' },
      qr_scanner: { status: 'activo', devices: 3, color: 'green' },
      seguridad: { status: 'óptima', incidents: 0, color: 'green' },
      database: { status: 'saludable', response: '12ms', color: 'green' }
    }
    
    setTimeout(() => {
      systemStatus.value = status
      loadingStatus.value = false
    }, 600)
    
    // API real:
    // const response = await fetch('/api/sistema/estado')
    // systemStatus.value = await response.json()
    // loadingStatus.value = false
  } catch (error) {
    console.error('Error fetching system status:', error)
    loadingStatus.value = false
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
  <Head title="CTAccess - Sistema de Control de Acceso PWA" />
  
  <!-- PWA Meta Tags -->
  <Head>
    <meta name="description" content="Aplicación PWA de control de acceso inteligente - Instálala en tu dispositivo" />
    <meta name="keywords" content="PWA, control acceso, seguridad, QR, gestión personas, app móvil" />
    <meta name="author" content="CTAccess Team" />
    <meta name="theme-color" content="#00304D" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="CTAccess" />
    <meta property="og:title" content="CTAccess PWA - Sistema de Control de Acceso" />
    <meta property="og:description" content="Aplicación web progresiva para gestión y control de acceso" />
    <meta property="og:type" content="website" />
    <link rel="manifest" href="/manifest.json" />
  </Head>

  <div class="min-h-screen bg-theme-primary text-theme-primary flex flex-col">
    <!-- Header fijo -->
    <header class="bg-theme-navbar border-b border-theme-primary px-4 py-3 flex-shrink-0">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo y branding -->
        <div class="flex items-center gap-3">
          <div class="relative">
            <ApplicationLogo 
              alt="CTAccess Logo" 
              :classes="'h-10 w-10 rounded-lg'" 
            />
            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-ping"></div>
          </div>
          <div>
            <h1 class="text-xl font-bold text-theme-primary">CTAccess</h1>
            <p class="text-xs text-theme-secondary">Control de Acceso</p>
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
    <main class="flex-1 px-4 py-6 overflow-y-auto">
      <div class="max-w-7xl mx-auto h-full">
        
        <!-- Reloj en tiempo real -->
        <div class="max-w-md mx-auto mb-6">
          <div class="bg-theme-card border border-theme-primary rounded-xl p-8 shadow-theme-md">
            <div class="text-center">
              <div class="text-5xl font-mono font-bold text-theme-primary mb-3">
                {{ formatTime(currentTime) }}
              </div>
              <div class="text-sm text-theme-secondary capitalize">
                {{ formatDate(currentTime) }}
              </div>
            </div>
          </div>
        </div>


        <!-- Estadísticas en tiempo real -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div 
            v-for="(stat, index) in [
              { key: 'personas_registradas', label: 'Personas', icon: 'users' },
              { key: 'accesos_hoy', label: 'Accesos Hoy', icon: 'calendar' },
              { key: 'accesos_activos', label: 'Activos', icon: 'activity' },
              { key: 'total_accesos', label: 'Total', icon: 'bar-chart' }
            ]" 
            :key="stat.key"
          >
            <div class="bg-theme-card border border-theme-primary rounded-xl p-4 hover:bg-theme-secondary transition-all duration-200 shadow-theme-sm">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-theme-tertiary rounded-lg flex items-center justify-center">
                  <Icon :name="stat.icon" :size="16" class="text-theme-primary" />
                </div>
                <div class="text-xl font-bold text-theme-primary">
                  {{ estadisticas[stat.key]?.toLocaleString() || '0' }}
                </div>
              </div>
              <h3 class="text-xs font-medium text-theme-secondary">{{ stat.label }}</h3>
            </div>
          </div>
        </div>

        <!-- Widgets Dinámicos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
          
          <!-- 🔥 Actividad en Tiempo Real -->
          <div class="bg-theme-card border border-theme-primary rounded-xl p-4 shadow-theme-sm">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                <Icon name="activity" :size="16" class="text-white" />
              </div>
              <h3 class="text-sm font-semibold text-theme-primary">Actividad Reciente</h3>
              <div class="ml-auto">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              </div>
            </div>
            
            <div class="space-y-2">
              <template v-if="loadingActivity">
                <div v-for="i in 3" :key="i" class="flex items-center gap-2 animate-pulse">
                  <div class="w-2 h-2 bg-theme-tertiary rounded-full"></div>
                  <div class="h-3 bg-theme-tertiary rounded flex-1"></div>
                  <div class="h-3 bg-theme-tertiary rounded w-8"></div>
                </div>
              </template>
              <template v-else>
                <div 
                  v-for="activity in recentActivity" 
                  :key="activity.id"
                  class="flex items-center gap-2 text-xs"
                >
                  <div 
                    :class="[
                      'w-2 h-2 rounded-full',
                      activity.tipo === 'entrada' ? 'bg-green-500' : 'bg-red-500'
                    ]"
                  ></div>
                  <span class="text-theme-primary flex-1 truncate">{{ activity.persona }}</span>
                  <span class="text-theme-muted">{{ formatRelativeTime(activity.tiempo) }}</span>
                </div>
              </template>
            </div>
          </div>

          <!-- 📊 Gráfico de Actividad Semanal -->
          <div class="bg-theme-card border border-theme-primary rounded-xl p-4 shadow-theme-sm">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <Icon name="bar-chart" :size="16" class="text-white" />
              </div>
              <h3 class="text-sm font-semibold text-theme-primary">Actividad Semanal</h3>
            </div>
            
            <div class="space-y-2">
              <template v-if="loadingWeekly">
                <div v-for="i in 7" :key="i" class="flex items-center gap-2 animate-pulse">
                  <div class="w-6 h-3 bg-theme-tertiary rounded"></div>
                  <div class="h-2 bg-theme-tertiary rounded flex-1"></div>
                  <div class="w-6 h-3 bg-theme-tertiary rounded"></div>
                </div>
              </template>
              <template v-else>
                <div 
                  v-for="day in weeklyActivity" 
                  :key="day.day"
                  class="flex items-center gap-2 text-xs"
                >
                  <span class="text-theme-secondary w-6">{{ day.day }}</span>
                  <div class="flex-1 bg-theme-tertiary rounded-full h-2 overflow-hidden">
                    <div 
                      class="h-full bg-blue-500 rounded-full transition-all duration-500"
                      :style="{ width: `${(day.accesos / day.maxAccesos) * 100}%` }"
                    ></div>
                  </div>
                  <span class="text-theme-primary w-6 text-right">{{ day.accesos }}</span>
                </div>
              </template>
            </div>
          </div>

          <!-- ⚡ Estado del Sistema -->
          <div class="bg-theme-card border border-theme-primary rounded-xl p-4 shadow-theme-sm">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                <Icon name="shield" :size="16" class="text-white" />
              </div>
              <h3 class="text-sm font-semibold text-theme-primary">Estado Sistema</h3>
            </div>
            
            <div class="space-y-2">
              <template v-if="loadingStatus">
                <div v-for="i in 4" :key="i" class="flex items-center gap-2 animate-pulse">
                  <div class="w-2 h-2 bg-theme-tertiary rounded-full"></div>
                  <div class="h-3 bg-theme-tertiary rounded flex-1"></div>
                  <div class="h-3 bg-theme-tertiary rounded w-12"></div>
                </div>
              </template>
              <template v-else>
                <div class="flex items-center gap-2 text-xs">
                  <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                  <span class="text-theme-primary flex-1">Sistema</span>
                  <span class="text-green-400">{{ systemStatus.sistema?.uptime }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                  <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                  <span class="text-theme-primary flex-1">QR Scanner</span>
                  <span class="text-green-400">{{ systemStatus.qr_scanner?.devices }} activos</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                  <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                  <span class="text-theme-primary flex-1">Seguridad</span>
                  <span class="text-green-400">Óptima</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                  <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                  <span class="text-theme-primary flex-1">Base de datos</span>
                  <span class="text-green-400">{{ systemStatus.database?.response }}</span>
                </div>
              </template>
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
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}
</style>

