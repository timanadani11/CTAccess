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

    <div class="space-y-8">
      <!-- Bienvenida -->
      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 p-8 shadow-lg">
        <div class="absolute top-0 right-0 h-32 w-32 -translate-y-16 translate-x-16 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-24 w-24 translate-x-[-50%] translate-y-[50%] rounded-full bg-white/5"></div>
        <div class="relative">
          <div class="flex items-center gap-4 mb-4">
            <ApplicationLogo classes="h-12 w-auto object-contain" alt="CTAccess Logo" />
            <div>
              <h3 class="text-xl font-bold text-white">¡Bienvenido, Celador!</h3>
              <p class="text-emerald-100">Gestiona el acceso y seguridad del CTA</p>
            </div>
          </div>
          <p class="text-emerald-50">Utiliza los accesos rápidos a continuación para gestionar entradas, salidas e incidencias de manera eficiente.</p>
        </div>
      </div>

      <!-- Accesos rápidos -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <Link v-for="item in quickLinks" :key="item.title" :href="item.href" class="group relative overflow-hidden rounded-2xl bg-theme-card p-6 shadow-theme-md ring-1 border-theme-primary transition-all duration-300 hover:shadow-theme-lg hover:ring-theme-secondary hover:-translate-y-1">
          <div :class="['absolute inset-0 opacity-5 bg-gradient-to-br transition-opacity group-hover:opacity-10', item.color]" />
          <div class="relative">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br" :class="item.color">
              <Icon :name="item.icon" :size="24" class="text-white" />
            </div>
            <div class="text-xl font-bold text-theme-primary mb-2">{{ item.title }}</div>
            <p class="text-sm text-theme-secondary mb-4 leading-relaxed">{{ item.description }}</p>
            <div class="flex items-center text-sm font-semibold text-theme-secondary group-hover:text-theme-primary">
              <span class="mr-2">Acceder</span>
              <Icon name="arrow-right" :size="16" class="transition-transform group-hover:translate-x-1" />
            </div>
          </div>
        </Link>
      </div>
    </div>
  </SystemLayout>
</template>

