<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
</script>

<template>
  <Head title="Inicio" />
  <div class="min-h-screen bg-gradient-to-br from-[#00304D] to-black text-white">
    <div class="max-w-7xl mx-auto px-6 py-10">
      <header class="flex items-center justify-between py-6">
        <!-- Logo -->
        <div class="flex items-center gap-3">
          <ApplicationLogo 
            alt="CTAccess Logo" 
            :classes="'h-10 w-10 rounded-lg'" 
          />
          <div>
            <h1 class="text-xl font-bold text-[#39A900]">CTAccess</h1>
            <p class="text-xs text-[#50E5F9]">Sistema de Control</p>
          </div>
        </div>

        <!-- Navegación -->
        <nav class="flex gap-3">
          <template v-if="$page.props.auth && $page.props.auth.user">
            <Link 
              :href="route('dashboard')" 
              class="px-4 py-2 text-white border border-[#50E5F9] rounded-lg hover:bg-[#50E5F9] hover:text-black transition-colors"
            >
              Panel de Control
            </Link>
          </template>
          <template v-else>
            <Link 
              :href="route('login')" 
              class="px-4 py-2 text-white border border-[#50E5F9] rounded-lg hover:bg-[#50E5F9] hover:text-black transition-colors"
            >
              Iniciar Sesión
            </Link>
            <Link 
              :href="route('personas.create')" 
              class="px-4 py-2 bg-[#39A900] text-white rounded-lg hover:bg-[#007832] transition-colors"
            >
              Registrar Persona
            </Link>
          </template>
        </nav>
      </header>

      <!-- Contenido principal -->
      <main class="mt-20">
        <div class="text-center max-w-3xl mx-auto">
          <h1 class="text-5xl font-bold text-white mb-4">
            Bienvenido a <span class="text-[#39A900]">CTAccess</span>
          </h1>
          
          <p class="text-lg text-gray-300 mb-12">
            Plataforma de gestión y control de acceso
          </p>

          <!-- Tarjetas simples -->
          <div class="grid md:grid-cols-3 gap-6 mt-16">
            <div class="bg-[#00304D]/50 border border-[#39A900]/20 p-6 rounded-lg">
              <div class="w-10 h-10 bg-[#39A900] rounded-lg mx-auto mb-4 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">Seguridad</h3>
              <p class="text-gray-400 text-sm">Control de acceso confiable</p>
            </div>

            <div class="bg-[#00304D]/50 border border-[#50E5F9]/20 p-6 rounded-lg">
              <div class="w-10 h-10 bg-[#50E5F9] rounded-lg mx-auto mb-4 flex items-center justify-center">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">Gestión</h3>
              <p class="text-gray-400 text-sm">Administración de personas</p>
            </div>

            <div class="bg-[#00304D]/50 border border-[#FDC300]/20 p-6 rounded-lg">
              <div class="w-10 h-10 bg-[#FDC300] rounded-lg mx-auto mb-4 flex items-center justify-center">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">Control</h3>
              <p class="text-gray-400 text-sm">Monitoreo en tiempo real</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

