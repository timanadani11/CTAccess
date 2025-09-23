<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'

const page = usePage()
const stats = page.props.stats || { personas: 0, usuarios: 0, accesos_hoy: 0, incidencias_7d: 0 }
const recent = page.props.recent || { accesos: [], incidencias: [] }
const meta = page.props.meta || {}
</script>

<template>
  <SystemLayout>
    <Head title="Panel de Administración" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Panel de Administración</h2>
        <div class="text-xs text-gray-500">Actualizado: {{ meta.generated_at }}</div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- KPIs -->
      <section>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Personas registradas</div>
            <div class="mt-1 text-2xl font-semibold text-slate-800">{{ stats.personas }}</div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Usuarios del sistema</div>
            <div class="mt-1 text-2xl font-semibold text-slate-800">{{ stats.usuarios }}</div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Accesos hoy</div>
            <div class="mt-1 text-2xl font-semibold text-slate-800">{{ stats.accesos_hoy }}</div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Incidencias (7 días)</div>
            <div class="mt-1 text-2xl font-semibold text-slate-800">{{ stats.incidencias_7d }}</div>
          </div>
        </div>
      </section>

      <!-- Últimos accesos -->
      <section>
        <div class="rounded-lg border border-slate-200 bg-white">
          <div class="border-b border-slate-200 p-4 text-sm font-semibold text-slate-700">Últimos accesos</div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Persona</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Entrada</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Salida</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Estado</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Entrada por</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Salida por</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="row in recent.accesos" :key="row.id" class="hover:bg-slate-50">
                  <td class="px-4 py-2">{{ row.persona || '—' }}</td>
                  <td class="px-4 py-2">{{ row.fecha_entrada || '—' }}</td>
                  <td class="px-4 py-2">{{ row.fecha_salida || '—' }}</td>
                  <td class="px-4 py-2">{{ row.estado || '—' }}</td>
                  <td class="px-4 py-2">{{ row.entrada_por || '—' }}</td>
                  <td class="px-4 py-2">{{ row.salida_por || '—' }}</td>
                </tr>
                <tr v-if="!recent.accesos?.length">
                  <td colspan="6" class="px-4 py-8 text-center text-slate-500">Sin datos</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Últimas incidencias -->
      <section>
        <div class="rounded-lg border border-slate-200 bg-white">
          <div class="border-b border-slate-200 p-4 text-sm font-semibold text-slate-700">Últimas incidencias</div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Tipo</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Descripción</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Persona</th>
                  <th class="px-4 py-2 text-left font-medium text-slate-600">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="row in recent.incidencias" :key="row.id" class="hover:bg-slate-50">
                  <td class="px-4 py-2">{{ row.tipo || '—' }}</td>
                  <td class="px-4 py-2">{{ row.descripcion || '—' }}</td>
                  <td class="px-4 py-2">{{ row.persona || '—' }}</td>
                  <td class="px-4 py-2">{{ row.fecha || '—' }}</td>
                </tr>
                <tr v-if="!recent.incidencias?.length">
                  <td colspan="4" class="px-4 py-8 text-center text-slate-500">Sin datos</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </SystemLayout>
</template>
