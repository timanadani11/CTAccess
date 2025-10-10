<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import Icon from '@/Components/Icon.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const quickLinks = computed(() => ([
  {
    title: 'Personas',
    description: 'Consulta información de personas registradas.',
    href: route('system.celador.personas.index'),
    color: 'from-blue-500 to-indigo-500',
    icon: 'users',
  },
  {
    title: 'Accesos',
    description: 'Gestiona entradas y salidas de personas y elementos.',
    href: route('system.celador.accesos.index'),
    color: 'from-emerald-500 to-teal-500',
    icon: 'key',
  },
  {
    title: 'Verificación QR',
    description: 'Escanea y valida códigos QR al instante.',
    href: route('system.celador.qr.index'),
    color: 'from-indigo-500 to-sky-500',
    icon: 'qr-code',
  },
  {
    title: 'Incidencias',
    description: 'Registra y consulta incidencias reportadas.',
    href: route('system.celador.incidencias.index'),
    color: 'from-amber-500 to-orange-500',
    icon: 'alert-triangle',
  },
  {
    title: 'Historial',
    description: 'Revisa el historial completo de accesos.',
    href: route('system.celador.historial.index'),
    color: 'from-slate-500 to-gray-600',
    icon: 'file-text',
  },
]))
</script>

<template>
  <SystemLayout>
    <Head title="Dashboard Celador" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-theme-primary">Dashboard • Celador</h2>
        <div class="text-sm text-theme-secondary">Acceso rápido a funciones principales</div>
      </div>
    </template>

    <div class="space-y-6 lg:space-y-8">
      <!-- Bienvenida -->
      <div class="relative overflow-hidden rounded-2xl lg:rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 p-6 sm:p-8 lg:p-10 xl:p-12 shadow-2xl">
        <div class="absolute top-0 right-0 h-40 w-40 lg:h-56 lg:w-56 -translate-y-20 translate-x-20 lg:-translate-y-28 lg:translate-x-28 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 lg:h-40 lg:w-40 translate-x-[-50%] translate-y-[50%] rounded-full bg-white/5 blur-xl"></div>
        <div class="absolute top-1/2 left-1/2 h-20 w-20 lg:h-32 lg:w-32 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/5 blur-3xl"></div>
        <div class="relative">
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6 mb-4 lg:mb-6">
            <div class="flex h-16 w-16 lg:h-20 lg:w-20 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm ring-2 ring-white/20 shadow-xl">
              <ApplicationLogo classes="h-12 lg:h-16 w-auto object-contain" alt="CTAccess Logo" />
            </div>
            <div>
              <h3 class="text-2xl lg:text-3xl xl:text-4xl font-bold text-white mb-2">¡Bienvenido, Celador!</h3>
              <p class="text-base lg:text-lg text-emerald-100">Gestiona el acceso y seguridad del Centro de Teleinformática y Producción Industrial</p>
            </div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 lg:p-5 border border-white/20">
            <p class="text-sm lg:text-base text-emerald-50 leading-relaxed">Utiliza los accesos rápidos a continuación para gestionar entradas, salidas e incidencias de manera eficiente. Todas las herramientas están diseñadas para facilitar tu trabajo diario.</p>
          </div>
        </div>
      </div>

      <!-- Accesos rápidos -->
      <div>
        <div class="mb-6">
          <h3 class="text-xl lg:text-2xl font-bold text-theme-primary mb-2">Accesos Rápidos</h3>
          <p class="text-theme-secondary text-sm lg:text-base">Selecciona una opción para comenzar</p>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 xl:gap-6">
          <Link v-for="item in quickLinks" :key="item.title" :href="item.href" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-theme-card to-theme-secondary p-6 lg:p-7 shadow-theme-md ring-2 ring-theme-primary transition-all duration-300 hover:shadow-2xl hover:ring-emerald-400 hover:-translate-y-2 hover:scale-105">
            <div :class="['absolute inset-0 opacity-0 bg-gradient-to-br transition-opacity duration-300 group-hover:opacity-10', item.color]" />
            <div class="relative">
              <div class="mb-4 lg:mb-5 flex h-14 w-14 lg:h-16 lg:w-16 items-center justify-center rounded-xl lg:rounded-2xl bg-gradient-to-br shadow-lg transform transition-transform group-hover:scale-110 group-hover:rotate-3" :class="item.color">
                <Icon :name="item.icon" :size="28" class="text-white lg:hidden" />
                <Icon :name="item.icon" :size="32" class="text-white hidden lg:block" />
              </div>
              <div class="text-lg lg:text-xl xl:text-2xl font-bold text-theme-primary mb-2 lg:mb-3">{{ item.title }}</div>
              <p class="text-sm lg:text-base text-theme-secondary mb-4 lg:mb-5 leading-relaxed min-h-[3rem] lg:min-h-[3.5rem]">{{ item.description }}</p>
              <div class="flex items-center text-sm lg:text-base font-semibold text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-300">
                <span class="mr-2">Acceder ahora</span>
                <Icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-2" />
              </div>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>

