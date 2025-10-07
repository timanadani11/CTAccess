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

    <div class="space-y-8">
      <!-- KPIs -->
      <section>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-theme-card p-6 shadow-theme-sm transition-all hover:shadow-theme-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-theme-tertiary opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-theme-tertiary">
                  <Icon name="users" :size="20" class="text-forest-600 dark:text-forest-400" />
                </div>
                <div class="text-sm font-medium text-theme-secondary">Personas registradas</div>
              </div>
              <div class="text-3xl font-bold text-theme-primary">{{ stats.personas }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-theme-card p-6 shadow-theme-sm transition-all hover:shadow-theme-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-theme-tertiary opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-theme-tertiary">
                  <Icon name="user-cog" :size="20" class="text-forest-600 dark:text-forest-400" />
                </div>
                <div class="text-sm font-medium text-theme-secondary">Usuarios del sistema</div>
              </div>
              <div class="text-3xl font-bold text-theme-primary">{{ stats.usuarios }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-theme-card p-6 shadow-theme-sm transition-all hover:shadow-theme-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-theme-tertiary opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-theme-tertiary">
                  <Icon name="log-in" :size="20" class="text-forest-600 dark:text-forest-400" />
                </div>
                <div class="text-sm font-medium text-theme-secondary">Accesos hoy</div>
              </div>
              <div class="text-3xl font-bold text-theme-primary">{{ stats.accesos_hoy }}</div>
            </div>
          </div>
          <div class="group relative overflow-hidden rounded-xl border border-theme-primary bg-theme-card p-6 shadow-theme-sm transition-all hover:shadow-theme-md">
            <div class="absolute top-0 right-0 h-20 w-20 -translate-y-10 translate-x-10 rounded-full bg-red-100 dark:bg-red-900/20 opacity-50"></div>
            <div class="relative">
              <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/20">
                  <Icon name="alert-triangle" :size="20" class="text-red-600 dark:text-red-400" />
                </div>
                <div class="text-sm font-medium text-theme-secondary">Incidencias (7 días)</div>
              </div>
              <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ stats.incidencias_7d }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Últimos accesos -->
      <section>
        <div class="overflow-hidden rounded-xl border border-theme-primary bg-theme-card shadow-theme-sm">
          <div class="flex items-center gap-3 border-b border-theme-primary bg-theme-secondary p-6">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-forest-600">
              <Icon name="clock" :size="16" class="text-white" />
            </div>
            <h3 class="text-lg font-semibold text-theme-primary">Últimos accesos</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-sm">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Persona</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Entrada</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Salida</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Estado</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Entrada por</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Salida por</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary bg-theme-card">
                <tr v-for="row in recent.accesos" :key="row.id" class="transition-colors hover:bg-theme-secondary">
                  <td class="px-6 py-4 font-medium text-theme-primary">{{ row.persona || '—' }}</td>
                  <td class="px-6 py-4 text-theme-secondary">{{ row.fecha_entrada || '—' }}</td>
                  <td class="px-6 py-4 text-theme-secondary">{{ row.fecha_salida || '—' }}</td>
                  <td class="px-6 py-4">
                    <span :class="[
                      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                      row.estado === 'Dentro' ? 'bg-forest-100 dark:bg-forest-800 text-forest-700 dark:text-forest-300' : 'bg-theme-tertiary text-theme-secondary'
                    ]">
                      {{ row.estado || '—' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-theme-secondary">{{ row.entrada_por || '—' }}</td>
                  <td class="px-6 py-4 text-theme-secondary">{{ row.salida_por || '—' }}</td>
                </tr>
                <tr v-if="!recent.accesos?.length">
                  <td colspan="6" class="px-6 py-12 text-center text-theme-muted">Sin datos disponibles</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Últimas incidencias -->
      <section>
        <div class="overflow-hidden rounded-xl border border-theme-primary bg-theme-card shadow-theme-sm">
          <div class="flex items-center gap-3 border-b border-theme-primary bg-red-50 dark:bg-red-900/20 p-6">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600">
              <Icon name="alert-triangle" :size="16" class="text-white" />
            </div>
            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Últimas incidencias</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary text-sm">
              <thead class="bg-theme-secondary">
                <tr>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Tipo</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Descripción</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Persona</th>
                  <th class="px-6 py-3 text-left font-semibold text-theme-secondary">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary bg-theme-card">
                <tr v-for="row in recent.incidencias" :key="row.id" class="transition-colors hover:bg-red-50 dark:hover:bg-red-900/10">
                  <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-300">
                      {{ row.tipo || '—' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 font-medium text-theme-primary">{{ row.descripcion || '—' }}</td>
                  <td class="px-6 py-4 text-theme-secondary">{{ row.persona || '—' }}</td>
                  <td class="px-6 py-4 text-theme-secondary">{{ row.fecha || '—' }}</td>
                </tr>
                <tr v-if="!recent.incidencias?.length">
                  <td colspan="4" class="px-6 py-12 text-center text-theme-muted">Sin incidencias reportadas</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </SystemLayout>
</template>