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
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard • Celador</h2>
    </template>

    <div class="py-10">
      <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
        <!-- Bienvenida -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
          <p class="text-gray-700">Bienvenido, Celador. Usa los accesos rápidos para comenzar.</p>
        </div>

        <!-- Accesos rápidos -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
          <Link v-for="item in quickLinks" :key="item.title" :href="item.href" class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md">
            <div :class="['absolute inset-0 opacity-10 bg-gradient-to-br', item.color]" />
            <div class="mb-2 text-2xl">{{ item.icon }}</div>
            <div class="text-lg font-semibold text-gray-800">{{ item.title }}</div>
            <p class="mt-1 text-sm text-gray-600">{{ item.description }}</p>
            <div class="mt-3 text-sm font-medium text-emerald-700 group-hover:underline">Ir →</div>
          </Link>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>

