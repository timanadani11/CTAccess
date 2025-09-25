<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
  estadisticas: Object,
  sistema_info: Object
})

// Estado para animaciones
const isVisible = ref(false)
const currentTime = ref(new Date())

// Actualizar hora cada segundo
onMounted(() => {
  isVisible.value = true
  setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
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
</script>

<template>
  <Head title="CTAccess - Sistema de Control de Acceso" />
  
  <!-- PWA Meta Tags -->
  <Head>
    <meta name="description" content="Sistema de control de acceso inteligente para instituciones educativas y empresariales" />
    <meta name="keywords" content="control acceso, seguridad, QR, gestión personas" />
    <meta name="author" content="CTAccess Team" />
    <meta property="og:title" content="CTAccess - Sistema de Control de Acceso" />
    <meta property="og:description" content="Plataforma moderna de gestión y control de acceso" />
    <meta property="og:type" content="website" />
  </Head>

  <div class="min-h-screen bg-gradient-to-br from-[#00304D] via-[#001a2e] to-black text-white overflow-hidden">
    <!-- Efectos de fondo animados -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#39A900]/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-[#50E5F9]/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-[#FDC300]/5 rounded-full blur-3xl animate-pulse delay-2000"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header -->
      <header class="flex flex-col sm:flex-row items-center justify-between py-6 gap-4">
        <!-- Logo y branding -->
        <div class="flex items-center gap-4">
          <div class="relative">
            <ApplicationLogo 
              alt="CTAccess Logo" 
              :classes="'h-12 w-12 rounded-xl shadow-lg'" 
            />
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-[#39A900] rounded-full animate-ping"></div>
          </div>
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-[#39A900] to-[#50E5F9] bg-clip-text text-transparent">
              CTAccess
            </h1>
            <p class="text-sm text-[#50E5F9]/80">Sistema de Control Inteligente</p>
          </div>
        </div>

        <!-- Navegación -->
        <nav class="flex flex-wrap gap-3 items-center">
          <template v-if="$page.props.auth && $page.props.auth.user">
            <Link 
              :href="route('dashboard')" 
              class="flex items-center gap-2 px-4 py-2 text-white border border-[#50E5F9]/50 rounded-xl hover:bg-[#50E5F9] hover:text-black transition-all duration-300 hover:scale-105"
            >
              <Icon name="home" :size="16" />
              Panel de Control
            </Link>
          </template>
          <template v-else>
            <Link 
              :href="route('login')" 
              class="flex items-center gap-2 px-4 py-2 text-white border border-[#50E5F9]/50 rounded-xl hover:bg-[#50E5F9] hover:text-black transition-all duration-300 hover:scale-105"
            >
              <Icon name="log-in" :size="16" />
              Iniciar Sesión
            </Link>
            <Link 
              :href="route('personas.create')" 
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#39A900] to-[#007832] text-white rounded-xl hover:from-[#007832] hover:to-[#39A900] transition-all duration-300 hover:scale-105 shadow-lg"
            >
              <Icon name="user-plus" :size="16" />
              Registrar Persona
            </Link>
          </template>
        </nav>
      </header>

      <!-- Contenido principal -->
      <main class="mt-8">
        <!-- Hero Section -->
        <div class="text-center max-w-4xl mx-auto mb-16">
          <div :class="['transition-all duration-1000', isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']">
            <h1 class="text-4xl sm:text-6xl font-bold text-white mb-6 leading-tight">
              Bienvenido a 
              <span class="bg-gradient-to-r from-[#39A900] via-[#50E5F9] to-[#FDC300] bg-clip-text text-transparent">
                CTAccess
              </span>
            </h1>
            
            <p class="text-lg sm:text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
              {{ sistema_info.descripcion }} - Versión {{ sistema_info.version }}
            </p>

            <!-- Reloj en tiempo real -->
            <div class="bg-black/30 backdrop-blur-sm border border-white/10 rounded-2xl p-6 mb-8 max-w-md mx-auto">
              <div class="text-3xl font-mono font-bold text-[#50E5F9] mb-2">
                {{ formatTime(currentTime) }}
              </div>
              <div class="text-sm text-gray-400 capitalize">
                {{ formatDate(currentTime) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Estadísticas en tiempo real -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          <div 
            v-for="(stat, index) in [
              { key: 'personas_registradas', label: 'Personas Registradas', icon: 'users', color: 'from-[#39A900] to-[#007832]', border: 'border-[#39A900]/30' },
              { key: 'accesos_hoy', label: 'Accesos Hoy', icon: 'calendar', color: 'from-[#50E5F9] to-[#0ea5e9]', border: 'border-[#50E5F9]/30' },
              { key: 'accesos_activos', label: 'Accesos Activos', icon: 'activity', color: 'from-[#FDC300] to-[#f59e0b]', border: 'border-[#FDC300]/30' },
              { key: 'total_accesos', label: 'Total Accesos', icon: 'bar-chart', color: 'from-purple-500 to-purple-700', border: 'border-purple-500/30' }
            ]" 
            :key="stat.key"
            :class="['transition-all duration-700 hover:scale-105', isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
            :style="{ transitionDelay: `${index * 200}ms` }"
          >
            <div class="bg-black/30 backdrop-blur-sm border rounded-2xl p-6 hover:bg-black/40 transition-all duration-300" :class="stat.border">
              <div class="flex items-center justify-between mb-4">
                <div :class="['w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br', stat.color]">
                  <Icon :name="stat.icon" :size="24" class="text-white" />
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-white">
                    {{ estadisticas[stat.key]?.toLocaleString() || '0' }}
                  </div>
                </div>
              </div>
              <h3 class="text-sm font-medium text-gray-300">{{ stat.label }}</h3>
            </div>
          </div>
        </div>

        <!-- Características principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <div 
            v-for="(feature, index) in [
              { 
                title: 'Control QR Inteligente', 
                description: 'Escaneo rápido y seguro de códigos QR para control de acceso instantáneo',
                icon: 'qr-code',
                color: 'from-[#39A900] to-[#007832]',
                border: 'border-[#39A900]/20'
              },
              { 
                title: 'Gestión de Personas', 
                description: 'Administración completa de usuarios, visitantes y personal autorizado',
                icon: 'users',
                color: 'from-[#50E5F9] to-[#0ea5e9]',
                border: 'border-[#50E5F9]/20'
              },
              { 
                title: 'Monitoreo en Tiempo Real', 
                description: 'Seguimiento instantáneo de entradas, salidas y actividad del sistema',
                icon: 'activity',
                color: 'from-[#FDC300] to-[#f59e0b]',
                border: 'border-[#FDC300]/20'
              }
            ]" 
            :key="index"
            :class="['transition-all duration-700 hover:scale-105', isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
            :style="{ transitionDelay: `${(index + 4) * 200}ms` }"
          >
            <div class="bg-black/20 backdrop-blur-sm border rounded-2xl p-8 hover:bg-black/30 transition-all duration-300 h-full" :class="feature.border">
              <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center bg-gradient-to-br mb-6 mx-auto', feature.color]">
                <Icon :name="feature.icon" :size="32" class="text-white" />
              </div>
              <h3 class="text-xl font-semibold text-white mb-4 text-center">{{ feature.title }}</h3>
              <p class="text-gray-400 text-center leading-relaxed">{{ feature.description }}</p>
            </div>
          </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="bg-black/20 backdrop-blur-sm border border-white/10 rounded-2xl p-8 text-center">
          <h2 class="text-2xl font-bold text-white mb-6">Accesos Rápidos</h2>
          <div class="flex flex-wrap justify-center gap-4">
            <Link 
              :href="route('personas.create')"
              class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-[#39A900] to-[#007832] text-white rounded-xl hover:from-[#007832] hover:to-[#39A900] transition-all duration-300 hover:scale-105 shadow-lg"
            >
              <Icon name="user-plus" :size="20" />
              Registrar Nueva Persona
            </Link>
            
            <a 
              href="/system/login"
              class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-[#50E5F9] to-[#0ea5e9] text-white rounded-xl hover:from-[#0ea5e9] hover:to-[#50E5F9] transition-all duration-300 hover:scale-105 shadow-lg"
            >
              <Icon name="shield" :size="20" />
              Acceso del Sistema
            </a>
            
            <Link 
              :href="route('personas.index')"
              class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-[#FDC300] to-[#f59e0b] text-black rounded-xl hover:from-[#f59e0b] hover:to-[#FDC300] transition-all duration-300 hover:scale-105 shadow-lg"
            >
              <Icon name="search" :size="20" />
              Consultar Personas
            </Link>
          </div>
        </div>

        <!-- Footer info -->
        <div class="mt-16 text-center text-gray-500 text-sm">
          <p>© {{ new Date().getFullYear() }} CTAccess - Sistema de Control de Acceso</p>
          <p class="mt-1">Última actualización: {{ sistema_info.ultima_actualizacion }}</p>
        </div>
      </main>
    </div>
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

