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
        <h2 class="text-2xl font-bold text-forest-800 dark:text-forest-200">Panel de Administración</h2>
        <div class="text-xs text-sage-500 dark:text-sage-400">Actualizado: {{ meta.generated_at }}</div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- KPIs -->
      <section>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div class="group relative overflow-hidden rounded-xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 p-6 shadow-sm transition-all hover:shadow-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-forest-100 dark:bg-forest-900 opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-forest-100 dark:bg-forest-800">
                  <Icon name="users" :size="20" class="text-forest-600 dark:text-forest-400" />
                </div>
                <div class="text-sm font-medium text-sage-600 dark:text-sage-400">Personas registradas</div>
              </div>
              <div class="text-3xl font-bold text-forest-800 dark:text-forest-200">{{ stats.personas }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 p-6 shadow-sm transition-all hover:shadow-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-forest-100 dark:bg-forest-900 opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-forest-100 dark:bg-forest-800">
                  <Icon name="user-cog" :size="20" class="text-forest-600 dark:text-forest-400" />
                </div>
                <div class="text-sm font-medium text-sage-600 dark:text-sage-400">Usuarios del sistema</div>
              </div>
              <div class="text-3xl font-bold text-forest-800 dark:text-forest-200">{{ stats.usuarios }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 p-6 shadow-sm transition-all hover:shadow-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-forest-100 dark:bg-forest-900 opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-forest-100 dark:bg-forest-800">
                  <Icon name="log-in" :size="20" class="text-forest-600 dark:text-forest-400" />
                </div>
                <div class="text-sm font-medium text-sage-600 dark:text-sage-400">Accesos hoy</div>
              </div>
              <div class="text-3xl font-bold text-forest-800 dark:text-forest-200">{{ stats.accesos_hoy }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 p-6 shadow-sm transition-all hover:shadow-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-forest-100 dark:bg-forest-900 opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900">
                  <Icon name="alert-triangle" :size="20" class="text-red-600 dark:text-red-400" />
                </div>
                <div class="text-sm font-medium text-sage-600 dark:text-sage-400">Incidencias (7 días)</div>
              </div>
              <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ stats.incidencias_7d }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Últimos accesos -->
      <section>
        <div class="overflow-hidden rounded-xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 shadow-sm">
          <div class="flex items-center gap-3 border-b border-forest-100 dark:border-sage-700 bg-forest-50 dark:bg-sage-800 p-6">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-forest-600">
              <Icon name="clock" :size="16" class="text-white" />
            </div>
            <h3 class="text-lg font-semibold text-forest-800 dark:text-forest-200">Últimos accesos</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-forest-100 dark:divide-sage-700 text-sm">
              <thead class="bg-sage-50 dark:bg-sage-800">
                <tr>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Persona</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Entrada</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Salida</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Estado</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Entrada por</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Salida por</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-forest-100 dark:divide-sage-700 bg-white dark:bg-sage-800">
                <tr v-for="row in recent.accesos" :key="row.id" class="transition-colors hover:bg-forest-50 dark:hover:bg-sage-700">
                  <td class="px-6 py-4 font-medium text-forest-800 dark:text-forest-200">{{ row.persona || '—' }}</td>
                  <td class="px-6 py-4 text-sage-700 dark:text-sage-300">{{ row.fecha_entrada || '—' }}</td>
                  <td class="px-6 py-4 text-sage-700 dark:text-sage-300">{{ row.fecha_salida || '—' }}</td>
                  <td class="px-6 py-4">
                    <span :class="[
                      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                      row.estado === 'Dentro' ? 'bg-forest-100 dark:bg-forest-800 text-forest-700 dark:text-forest-300' : 'bg-sage-100 dark:bg-sage-700 text-sage-700 dark:text-sage-300'
                    ]">
                      {{ row.estado || '—' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sage-700 dark:text-sage-300">{{ row.entrada_por || '—' }}</td>
                  <td class="px-6 py-4 text-sage-700 dark:text-sage-300">{{ row.salida_por || '—' }}</td>
                </tr>
                <tr v-if="!recent.accesos?.length">
                  <td colspan="6" class="px-6 py-12 text-center text-sage-500 dark:text-sage-400">Sin datos disponibles</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Últimas incidencias -->
      <section>
        <div class="overflow-hidden rounded-xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 shadow-sm">
          <div class="flex items-center gap-3 border-b border-forest-100 dark:border-sage-700 bg-red-50 dark:bg-red-900/20 p-6">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600">
              <Icon name="alert-triangle" :size="16" class="text-white" />
            </div>
            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Últimas incidencias</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-forest-100 dark:divide-sage-700 text-sm">
              <thead class="bg-sage-50 dark:bg-sage-800">
                <tr>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Tipo</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Descripción</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Persona</th>
                  <th class="px-6 py-3 text-left font-semibold text-sage-700 dark:text-sage-300">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-forest-100 dark:divide-sage-700 bg-white dark:bg-sage-800">
                <tr v-for="row in recent.incidencias" :key="row.id" class="transition-colors hover:bg-red-50 dark:hover:bg-red-900/20">
                  <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-300">
                      {{ row.tipo || '—' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 font-medium text-forest-800 dark:text-forest-200">{{ row.descripcion || '—' }}</td>
                  <td class="px-6 py-4 text-sage-700 dark:text-sage-300">{{ row.persona || '—' }}</td>
                  <td class="px-6 py-4 text-sage-700 dark:text-sage-300">{{ row.fecha || '—' }}</td>
                </tr>
                <tr v-if="!recent.incidencias?.length">
                  <td colspan="4" class="px-6 py-12 text-center text-sage-500 dark:text-sage-400">Sin incidencias reportadas</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </SystemLayout>
</template>