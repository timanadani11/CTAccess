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
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-theme-primary">Panel de Administración</h2>
        <div class="text-xs text-theme-muted">Actualizado: {{ meta.generated_at }}</div>
      </div>
    </template>

    <div class="space-y-6 lg:space-y-8">
      <!-- KPIs -->
      <section>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:gap-6">
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-6 lg:p-7 shadow-theme-sm transition-all hover:shadow-theme-lg hover:scale-105">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-12 translate-x-12 rounded-full bg-gradient-to-br from-sena-green-400 to-sena-green-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-3">
                <div class="flex h-12 w-12 lg:h-14 lg:w-14 items-center justify-center rounded-xl bg-gradient-to-br from-sena-green-600 to-sena-green-700 shadow-md">
                  <Icon name="users" :size="24" class="text-white" />
                </div>
                <div class="text-sm lg:text-base font-medium text-theme-secondary">Personas registradas</div>
              </div>
              <div class="text-3xl lg:text-4xl xl:text-5xl font-bold text-theme-primary">{{ stats.personas }}</div>
              <div class="mt-2 text-xs text-theme-muted">Total en el sistema</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-6 lg:p-7 shadow-theme-sm transition-all hover:shadow-theme-lg hover:scale-105">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-12 translate-x-12 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-3">
                <div class="flex h-12 w-12 lg:h-14 lg:w-14 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 shadow-md">
                  <Icon name="user-cog" :size="24" class="text-white" />
                </div>
                <div class="text-sm lg:text-base font-medium text-theme-secondary">Usuarios del sistema</div>
              </div>
              <div class="text-3xl lg:text-4xl xl:text-5xl font-bold text-theme-primary">{{ stats.usuarios }}</div>
              <div class="mt-2 text-xs text-theme-muted">Usuarios activos</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-6 lg:p-7 shadow-theme-sm transition-all hover:shadow-theme-lg hover:scale-105">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-12 translate-x-12 rounded-full bg-gradient-to-br from-sena-yellow-400 to-sena-yellow-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-3">
                <div class="flex h-12 w-12 lg:h-14 lg:w-14 items-center justify-center rounded-xl bg-gradient-to-br from-sena-yellow-500 to-sena-yellow-600 shadow-md">
                  <Icon name="log-in" :size="24" class="text-gray-900" />
                </div>
                <div class="text-sm lg:text-base font-medium text-theme-secondary">Accesos hoy</div>
              </div>
              <div class="text-3xl lg:text-4xl xl:text-5xl font-bold text-theme-primary">{{ stats.accesos_hoy }}</div>
              <div class="mt-2 text-xs text-theme-muted">Hoy {{ new Date().toLocaleDateString() }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-gradient-to-br from-theme-card to-theme-secondary p-6 lg:p-7 shadow-theme-sm transition-all hover:shadow-theme-lg hover:scale-105">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-12 translate-x-12 rounded-full bg-gradient-to-br from-red-400 to-red-600 opacity-20"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-3">
                <div class="flex h-12 w-12 lg:h-14 lg:w-14 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-red-700 shadow-md">
                  <Icon name="alert-triangle" :size="24" class="text-white" />
                </div>
                <div class="text-sm lg:text-base font-medium text-theme-secondary">Incidencias (7 días)</div>
              </div>
              <div class="text-3xl lg:text-4xl xl:text-5xl font-bold text-red-600 dark:text-red-400">{{ stats.incidencias_7d }}</div>
              <div class="mt-2 text-xs text-theme-muted">Últimos 7 días</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Últimos accesos -->
      <section>
        <div class="overflow-hidden rounded-xl border-2 border-theme-primary bg-theme-card shadow-theme-md hover:shadow-theme-lg transition-shadow">
          <div class="flex items-center gap-3 border-b-2 border-theme-primary bg-gradient-to-r from-sena-green-50 to-sena-green-100 dark:from-sena-blue-900/20 dark:to-sena-blue-900/30 p-5 lg:p-6">
            <div class="flex h-10 w-10 lg:h-12 lg:w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sena-green-600 to-sena-green-700 dark:from-cyan-500 dark:to-cyan-600 shadow-md">
              <Icon name="clock" :size="20" class="text-white lg:hidden" />
              <Icon name="clock" :size="24" class="text-white hidden lg:block" />
            </div>
            <div>
              <h3 class="text-lg lg:text-xl font-bold text-sena-green-800 dark:text-cyan-300">Últimos accesos</h3>
              <p class="text-xs text-sena-green-600 dark:text-cyan-400">Movimientos recientes del día</p>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-sm lg:text-base">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Persona</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Entrada</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Salida</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Estado</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Entrada por</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Salida por</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary bg-theme-card">
                <tr v-for="row in recent.accesos" :key="row.id" class="transition-all hover:bg-theme-secondary hover:scale-[1.01]">
                  <td class="px-4 lg:px-6 py-3 lg:py-4 font-semibold text-theme-primary">{{ row.persona || '—' }}</td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 text-theme-secondary">{{ row.fecha_entrada || '—' }}</td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 text-theme-secondary">{{ row.fecha_salida || '—' }}</td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <span :class="[
                      'inline-flex items-center rounded-full px-3 py-1 text-xs lg:text-sm font-semibold shadow-sm',
                      row.estado === 'Dentro' ? 'bg-sena-green-100 dark:bg-sena-green-800 text-sena-green-700 dark:text-sena-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                    ]">
                      {{ row.estado || '—' }}
                    </span>
                  </td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 text-theme-secondary">{{ row.entrada_por || '—' }}</td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 text-theme-secondary">{{ row.salida_por || '—' }}</td>
                </tr>
                <tr v-if="!recent.accesos?.length">
                  <td colspan="6" class="px-6 py-16 text-center">
                    <Icon name="inbox" :size="48" class="mx-auto text-sena-green-500 dark:text-cyan-500 mb-3 opacity-50" />
                    <p class="text-theme-muted text-base">Sin datos disponibles</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Últimas incidencias -->
      <section>
        <div class="overflow-hidden rounded-xl border-2 border-red-300 dark:border-red-800 bg-theme-card shadow-theme-md hover:shadow-theme-lg transition-shadow">
          <div class="flex items-center gap-3 border-b-2 border-red-300 dark:border-red-800 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/30 p-5 lg:p-6">
            <div class="flex h-10 w-10 lg:h-12 lg:w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-red-700 shadow-md">
              <Icon name="alert-triangle" :size="20" class="text-white lg:hidden" />
              <Icon name="alert-triangle" :size="24" class="text-white hidden lg:block" />
            </div>
            <div>
              <h3 class="text-lg lg:text-xl font-bold text-red-800 dark:text-red-200">Últimas incidencias</h3>
              <p class="text-xs text-red-600 dark:text-red-400">Reportes de los últimos 7 días</p>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-sm lg:text-base">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Tipo</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Descripción</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Persona</th>
                  <th class="px-4 lg:px-6 py-3 lg:py-4 text-left font-semibold text-theme-secondary">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary bg-theme-card">
                <tr v-for="row in recent.incidencias" :key="row.id" class="transition-all hover:bg-red-50 dark:hover:bg-red-900/10 hover:scale-[1.01]">
                  <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-xs lg:text-sm font-semibold text-red-700 dark:text-red-300 shadow-sm">
                      {{ row.tipo || '—' }}
                    </span>
                  </td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 font-semibold text-theme-primary">{{ row.descripcion || '—' }}</td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 text-theme-secondary">{{ row.persona || '—' }}</td>
                  <td class="px-4 lg:px-6 py-3 lg:py-4 text-theme-secondary">{{ row.fecha || '—' }}</td>
                </tr>
                <tr v-if="!recent.incidencias?.length">
                  <td colspan="4" class="px-6 py-16 text-center">
                    <Icon name="check-circle" :size="48" class="mx-auto text-sena-green-500 dark:text-cyan-500 mb-3" />
                    <p class="text-theme-muted text-base">Sin incidencias reportadas</p>
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