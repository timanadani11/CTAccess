<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const quickLinks = computed(() => ([
  {
    title: 'Accesos',
    description: 'Gestiona entradas y salidas de personas y elementos.',
    href: route('system.celador.accesos.index'),
    color: 'from-emerald-500 to-teal-500',
    icon: '🔐',
  },
  {
    title: 'Verificación QR',
    description: 'Escanea y valida códigos QR al instante.',
    href: route('system.celador.qr'),
    color: 'from-indigo-500 to-sky-500',
    icon: '� QR',
  },
  {
    title: 'Incidencias',
    description: 'Registra y consulta incidencias reportadas.',
    href: route('system.celador.incidencias.index'),
    color: 'from-amber-500 to-orange-500',
    icon: '⚠️',
  },
  {
    title: 'Historial',
    description: 'Revisa el historial completo de accesos.',
    href: route('system.celador.historial.index'),
    color: 'from-slate-500 to-gray-600',
    icon: '📜',
  },
]))
</script>

<template>
  <SystemLayout>
    <Head title="Dashboard Celador" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-forest-800 dark:text-forest-200">Dashboard • Celador</h2>
        <div class="text-sm text-sage-600 dark:text-sage-400">Acceso rápido a funciones principales</div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Bienvenida -->
      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-forest-600 to-forest-700 dark:from-forest-700 dark:to-forest-800 p-8 shadow-lg">
        <div class="absolute top-0 right-0 h-32 w-32 -translate-y-16 translate-x-16 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-24 w-24 translate-x-[-50%] translate-y-[50%] rounded-full bg-white/5"></div>
        <div class="relative">
          <div class="flex items-center gap-4 mb-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-white">¡Bienvenido, Celador!</h3>
              <p class="text-forest-100 dark:text-forest-200">Gestiona el acceso y seguridad del centro educativo</p>
            </div>
          </div>
          <p class="text-forest-50 dark:text-forest-100">Utiliza los accesos rápidos a continuación para gestionar entradas, salidas e incidencias de manera eficiente.</p>
        </div>
      </div>

      <!-- Accesos rápidos -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <Link v-for="item in quickLinks" :key="item.title" :href="item.href" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-sage-800 p-6 shadow-md ring-1 ring-forest-200 dark:ring-sage-700 transition-all duration-300 hover:shadow-xl hover:ring-forest-300 dark:hover:ring-forest-600 hover:-translate-y-1">
          <div :class="['absolute inset-0 opacity-5 bg-gradient-to-br transition-opacity group-hover:opacity-10', item.color]" />
          <div class="relative">
            <div class="mb-4 text-3xl">{{ item.icon }}</div>
            <div class="text-xl font-bold text-forest-800 dark:text-forest-200 mb-2">{{ item.title }}</div>
            <p class="text-sm text-sage-600 dark:text-sage-400 mb-4 leading-relaxed">{{ item.description }}</p>
            <div class="flex items-center text-sm font-semibold text-forest-600 dark:text-forest-400 group-hover:text-forest-700 dark:group-hover:text-forest-300">
              <span class="mr-2">Acceder</span>
              <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </div>
          </div>
        </Link>
      </div>
    </div>
  </SystemLayout>
</template>

