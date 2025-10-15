<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'

const page = usePage()
const stats = page.props.stats || { personas: 0, usuarios: 0, accesos_hoy: 0, incidencias_7d: 0 }
const recent = page.props.recent || { accesos: [], incidencias: [] }
const meta = page.props.meta || {}
</script>

<template>
  <SystemLayout>
    <Head title="Panel de Administración" />

    <template #header>
      <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-lg sm:text-xl font-bold text-theme-primary">Panel de Administración</h2>
        <div class="text-xs text-theme-muted">{{ meta.generated_at }}</div>
      </div>
    </template>

    <div class="space-y-3 sm:space-y-4 lg:space-y-5">
      <!-- KPIs Compactos -->
      <section>
        <div class="grid gap-2 sm:gap-3 grid-cols-2 lg:grid-cols-4">
          <div class="group relative overflow-hidden rounded-lg border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-3 sm:p-4 shadow-theme-sm transition-all hover:shadow-theme-md active:scale-95 sm:hover:scale-105 touch-manipulation">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-gradient-to-br from-sena-green-400 to-sena-green-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-2 mb-2">
                <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-gradient-to-br from-sena-green-600 to-sena-green-700 shadow-sm">
                  <Icon name="users" :size="18" class="text-white sm:hidden" />
                  <Icon name="users" :size="20" class="text-white hidden sm:block" />
                </div>
                <div class="text-xs sm:text-sm font-medium text-theme-secondary leading-tight">Personas</div>
              </div>
              <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-theme-primary">{{ stats.personas }}</div>
              <div class="mt-1 text-xs text-theme-muted hidden sm:block">Total sistema</div>
            </div>
          </div>
          
          <div class="group relative overflow-hidden rounded-lg border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-3 sm:p-4 shadow-theme-sm transition-all hover:shadow-theme-md active:scale-95 sm:hover:scale-105 touch-manipulation">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-2 mb-2">
                <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 shadow-sm">
                  <Icon name="user-cog" :size="18" class="text-white sm:hidden" />
                  <Icon name="user-cog" :size="20" class="text-white hidden sm:block" />
                </div>
                <div class="text-xs sm:text-sm font-medium text-theme-secondary leading-tight">Usuarios</div>
              </div>
              <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-theme-primary">{{ stats.usuarios }}</div>
              <div class="mt-1 text-xs text-theme-muted hidden sm:block">Activos</div>
            </div>
          </div>
          
          <div class="group relative overflow-hidden rounded-lg border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-3 sm:p-4 shadow-theme-sm transition-all hover:shadow-theme-md active:scale-95 sm:hover:scale-105 touch-manipulation">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-gradient-to-br from-sena-yellow-400 to-sena-yellow-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-2 mb-2">
                <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-gradient-to-br from-sena-yellow-500 to-sena-yellow-600 shadow-sm">
                  <Icon name="log-in" :size="18" class="text-gray-900 sm:hidden" />
                  <Icon name="log-in" :size="20" class="text-gray-900 hidden sm:block" />
                </div>
                <div class="text-xs sm:text-sm font-medium text-theme-secondary leading-tight">Hoy</div>
              </div>
              <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-theme-primary">{{ stats.accesos_hoy }}</div>
              <div class="mt-1 text-xs text-theme-muted hidden sm:block">Accesos</div>
            </div>
          </div>
          
          <div class="group relative overflow-hidden rounded-lg border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-3 sm:p-4 shadow-theme-sm transition-all hover:shadow-theme-md active:scale-95 sm:hover:scale-105 touch-manipulation">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-gradient-to-br from-red-400 to-red-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-2 mb-2">
                <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-gradient-to-br from-red-500 to-red-700 shadow-sm">
                  <Icon name="alert-triangle" :size="18" class="text-white sm:hidden" />
                  <Icon name="alert-triangle" :size="20" class="text-white hidden sm:block" />
                </div>
                <div class="text-xs sm:text-sm font-medium text-theme-secondary leading-tight">Incidencias</div>
              </div>
              <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-600 dark:text-red-400">{{ stats.incidencias_7d }}</div>
              <div class="mt-1 text-xs text-theme-muted hidden sm:block">7 días</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Últimos accesos -->
      <section>
        <div class="overflow-hidden rounded-lg border border-theme-primary bg-theme-card shadow-theme-sm">
          <div class="flex items-center gap-2 sm:gap-3 border-b border-theme-primary bg-gradient-to-r from-sena-green-50 to-sena-green-100 dark:from-sena-blue-900/20 dark:to-sena-blue-900/30 p-3 sm:p-4">
            <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-lg bg-gradient-to-br from-sena-green-600 to-sena-green-700 dark:from-cyan-500 dark:to-cyan-600 shadow-sm flex-shrink-0">
              <Icon name="clock" :size="18" class="text-white sm:hidden" />
              <Icon name="clock" :size="20" class="text-white hidden sm:block" />
            </div>
            <div class="min-w-0">
              <h3 class="text-sm sm:text-base font-bold text-sena-green-800 dark:text-cyan-300">Últimos accesos</h3>
              <p class="text-xs text-sena-green-600 dark:text-cyan-400 hidden sm:block">Movimientos recientes</p>
            </div>
          </div>
          
          <!-- Vista Móvil -->
          <div class="lg:hidden divide-y divide-theme-primary">
            <div v-for="row in recent.accesos" :key="row.id" class="p-3 hover:bg-theme-secondary transition-colors">
              <div class="flex items-start justify-between gap-2 mb-2">
                <p class="font-semibold text-theme-primary text-sm flex-1 min-w-0 truncate">{{ row.persona || '—' }}</p>
                <span :class="[
                  'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold whitespace-nowrap',
                  row.estado === 'Dentro' ? 'bg-sena-green-100 dark:bg-sena-green-800 text-sena-green-700 dark:text-sena-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                ]">
                  {{ row.estado || '—' }}
                </span>
              </div>
              <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                  <p class="text-theme-muted">Entrada</p>
                  <p class="text-theme-secondary font-medium">{{ row.fecha_entrada || '—' }}</p>
                  <p class="text-theme-muted text-xs mt-0.5">{{ row.entrada_por || '—' }}</p>
                </div>
                <div>
                  <p class="text-theme-muted">Salida</p>
                  <p class="text-theme-secondary font-medium">{{ row.fecha_salida || '—' }}</p>
                  <p class="text-theme-muted text-xs mt-0.5">{{ row.salida_por || '—' }}</p>
                </div>
              </div>
            </div>
            <div v-if="!recent.accesos?.length" class="p-8 text-center">
              <Icon name="inbox" :size="40" class="mx-auto text-sena-green-500 dark:text-cyan-500 mb-2 opacity-50" />
              <p class="text-theme-muted text-sm">Sin datos disponibles</p>
            </div>
          </div>
          
          <!-- Vista Desktop -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-sm">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Persona</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Entrada</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Salida</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Estado</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Entrada por</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Salida por</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary bg-theme-card">
                <tr v-for="row in recent.accesos" :key="row.id" class="transition-colors hover:bg-theme-secondary">
                  <td class="px-3 py-3 font-semibold text-theme-primary">{{ row.persona || '—' }}</td>
                  <td class="px-3 py-3 text-theme-secondary">{{ row.fecha_entrada || '—' }}</td>
                  <td class="px-3 py-3 text-theme-secondary">{{ row.fecha_salida || '—' }}</td>
                  <td class="px-3 py-3">
                    <span :class="[
                      'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold',
                      row.estado === 'Dentro' ? 'bg-sena-green-100 dark:bg-sena-green-800 text-sena-green-700 dark:text-sena-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                    ]">
                      {{ row.estado || '—' }}
                    </span>
                  </td>
                  <td class="px-3 py-3 text-theme-secondary">{{ row.entrada_por || '—' }}</td>
                  <td class="px-3 py-3 text-theme-secondary">{{ row.salida_por || '—' }}</td>
                </tr>
                <tr v-if="!recent.accesos?.length">
                  <td colspan="6" class="px-3 py-12 text-center">
                    <Icon name="inbox" :size="40" class="mx-auto text-sena-green-500 dark:text-cyan-500 mb-2 opacity-50" />
                    <p class="text-theme-muted text-sm">Sin datos disponibles</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Últimas incidencias -->
      <section>
        <div class="overflow-hidden rounded-lg border border-red-300 dark:border-red-800 bg-theme-card shadow-theme-sm">
          <div class="flex items-center gap-2 sm:gap-3 border-b border-red-300 dark:border-red-800 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/30 p-3 sm:p-4">
            <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-lg bg-gradient-to-br from-red-500 to-red-700 shadow-sm flex-shrink-0">
              <Icon name="alert-triangle" :size="18" class="text-white sm:hidden" />
              <Icon name="alert-triangle" :size="20" class="text-white hidden sm:block" />
            </div>
            <div class="min-w-0">
              <h3 class="text-sm sm:text-base font-bold text-red-800 dark:text-red-200">Últimas incidencias</h3>
              <p class="text-xs text-red-600 dark:text-red-400 hidden sm:block">Últimos 7 días</p>
            </div>
          </div>
          
          <!-- Vista Móvil -->
          <div class="lg:hidden divide-y divide-theme-primary">
            <div v-for="row in recent.incidencias" :key="row.id" class="p-3 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
              <div class="flex items-start justify-between gap-2 mb-2">
                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-semibold text-red-700 dark:text-red-300">
                  {{ row.tipo || '—' }}
                </span>
                <span class="text-xs text-theme-muted">{{ row.fecha || '—' }}</span>
              </div>
              <p class="font-semibold text-theme-primary text-sm mb-1 line-clamp-2">{{ row.descripcion || '—' }}</p>
              <p class="text-xs text-theme-secondary">{{ row.persona || '—' }}</p>
            </div>
            <div v-if="!recent.incidencias?.length" class="p-8 text-center">
              <Icon name="check-circle" :size="40" class="mx-auto text-sena-green-500 dark:text-cyan-500 mb-2" />
              <p class="text-theme-muted text-sm font-medium">Sin incidencias</p>
              <p class="text-xs text-theme-muted mt-1">¡Todo en orden!</p>
            </div>
          </div>
          
          <!-- Vista Desktop -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-sm">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Tipo</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Descripción</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Persona</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-theme-secondary">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary bg-theme-card">
                <tr v-for="row in recent.incidencias" :key="row.id" class="transition-colors hover:bg-red-50 dark:hover:bg-red-900/10">
                  <td class="px-3 py-3">
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-1 text-xs font-semibold text-red-700 dark:text-red-300">
                      {{ row.tipo || '—' }}
                    </span>
                  </td>
                  <td class="px-3 py-3 font-semibold text-theme-primary">{{ row.descripcion || '—' }}</td>
                  <td class="px-3 py-3 text-theme-secondary">{{ row.persona || '—' }}</td>
                  <td class="px-3 py-3 text-theme-secondary">{{ row.fecha || '—' }}</td>
                </tr>
                <tr v-if="!recent.incidencias?.length">
                  <td colspan="4" class="px-3 py-12 text-center">
                    <Icon name="check-circle" :size="40" class="mx-auto text-sena-green-500 dark:text-cyan-500 mb-2" />
                    <p class="text-theme-muted text-sm font-medium">Sin incidencias reportadas</p>
                    <p class="text-xs text-theme-muted mt-1">¡Todo está en orden!</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </SystemLayout>
</template>