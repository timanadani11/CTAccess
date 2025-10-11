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

// Estado para el clima
const weather = ref(null)
const loadingWeather = ref(true)
const weatherError = ref(false)

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
  fetchWeather()

  // 🔥 WEBSOCKET: Escuchar nuevos accesos en tiempo real
  if (typeof window.Echo !== 'undefined') {
    window.Echo.channel('accesos')
      .listen('.acceso.registrado', (data) => {
        console.log('🎉 Nuevo acceso registrado:', data)
        
        // Agregar al inicio de la actividad reciente
        recentActivity.value.unshift({
          id: data.id,
          persona: data.persona.nombre,
          documento: data.persona.documento,
          tipo: data.tipo_acceso,
          tiempo: new Date(data.timestamp)
        })
        
        // Mantener solo los últimos 10
        if (recentActivity.value.length > 10) {
          recentActivity.value = recentActivity.value.slice(0, 10)
        }
        
        // Actualizar estadísticas
        if (data.tipo_acceso === 'entrada') {
          props.estadisticas.accesos_hoy++
          props.estadisticas.accesos_activos++
        } else {
          props.estadisticas.accesos_activos--
        }
      })
  } else {
    console.warn('⚠️ Laravel Echo no está disponible. WebSockets deshabilitados.')
  }

  // Actualizar clima cada 10 minutos
  setInterval(() => {
    fetchWeather()
  }, 600000)
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

// 🌤️ Obtener datos del clima
const fetchWeather = async () => {
  try {
    // Primero obtener geolocalización
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const { latitude, longitude } = position.coords
          
          // API Key de OpenWeatherMap desde variables de entorno
          const API_KEY = import.meta.env.VITE_OPENWEATHER_API_KEY
          
          // Si no hay API key configurada, usar datos simulados
          if (!API_KEY) {
            useFallbackWeather()
            return
          }
          
          const url = `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&units=metric&lang=es&appid=${API_KEY}`
          
          try {
            const response = await fetch(url)
            if (response.ok) {
              const data = await response.json()
              weather.value = {
                temp: Math.round(data.main.temp),
                feels_like: Math.round(data.main.feels_like),
                description: data.weather[0].description,
                icon: data.weather[0].icon,
                humidity: data.main.humidity,
                wind: Math.round(data.wind.speed * 3.6), // m/s a km/h
                city: data.name
              }
              loadingWeather.value = false
              weatherError.value = false
            } else {
              // Si falla la API, usar datos simulados
              useFallbackWeather()
            }
          } catch (error) {
            console.error('Error fetching weather:', error)
            useFallbackWeather()
          }
        },
        (error) => {
          console.error('Geolocation error:', error)
          useFallbackWeather()
        }
      )
    } else {
      useFallbackWeather()
    }
  } catch (error) {
    console.error('Error in fetchWeather:', error)
    useFallbackWeather()
  }
}

// Datos de clima simulados si falla la API
const useFallbackWeather = () => {
  weather.value = {
    temp: 22,
    feels_like: 24,
    description: 'parcialmente nublado',
    icon: '02d',
    humidity: 65,
    wind: 15,
    city: 'Tu Ciudad'
  }
  loadingWeather.value = false
  weatherError.value = false
}

// Obtener emoji según el código del clima
const getWeatherEmoji = (icon) => {
  const emojiMap = {
    '01d': '☀️', '01n': '🌙',
    '02d': '⛅', '02n': '☁️',
    '03d': '☁️', '03n': '☁️',
    '04d': '☁️', '04n': '☁️',
    '09d': '🌧️', '09n': '🌧️',
    '10d': '🌦️', '10n': '🌧️',
    '11d': '⛈️', '11n': '⛈️',
    '13d': '❄️', '13n': '❄️',
    '50d': '🌫️', '50n': '🌫️'
  }
  return emojiMap[icon] || '🌤️'
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
        <!-- Logo -->
        <div class="flex items-center gap-3">
          <div class="relative">
            <ApplicationLogo 
              alt="CTAccess Logo" 
              classes="h-12 w-auto object-contain"
            />
            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-ping"></div>
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
        
        <!-- Reloj y Clima Compactos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 max-w-4xl mx-auto">
          
          <!-- Reloj Digital -->
          <div class="bg-theme-card border border-theme-primary rounded-xl p-6 shadow-theme-md">
            <div class="text-center">
              <div class="text-4xl font-mono font-bold text-theme-primary mb-2">
                {{ formatTime(currentTime) }}
              </div>
              <div class="text-xs text-theme-secondary capitalize">
                {{ formatDate(currentTime) }}
              </div>
            </div>
          </div>

          <!-- Widget del Clima -->
          <div class="bg-theme-card border border-theme-primary rounded-xl p-6 shadow-theme-md">
            <template v-if="loadingWeather">
              <div class="flex items-center justify-center h-full">
                <Icon name="loader" :size="24" class="text-theme-muted animate-spin" />
              </div>
            </template>
            <template v-else-if="weather">
              <div class="flex items-center gap-4">
                <div class="text-5xl">{{ getWeatherEmoji(weather.icon) }}</div>
                <div class="flex-1">
                  <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-theme-primary">{{ weather.temp }}°</span>
                    <span class="text-sm text-theme-muted">Sensación {{ weather.feels_like }}°</span>
                  </div>
                  <p class="text-xs text-theme-secondary capitalize mb-1">{{ weather.description }}</p>
                  <div class="flex gap-3 text-xs text-theme-muted">
                    <span class="flex items-center gap-1">
                      <Icon name="droplet" :size="12" />
                      {{ weather.humidity }}%
                    </span>
                    <span class="flex items-center gap-1">
                      <Icon name="wind" :size="12" />
                      {{ weather.wind }} km/h
                    </span>
                  </div>
                </div>
              </div>
            </template>
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

