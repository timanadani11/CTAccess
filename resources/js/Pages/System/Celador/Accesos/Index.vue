<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  accesos: Object,
  filters: Object,
})

const q = ref(props.filters?.q ?? '')

watch(q, (val) => {
  router.get(route('system.celador.accesos.index'), { q: val }, { preserveState: true, replace: true })
})
</script>

<template>
  <SystemLayout>
    <Head title="Accesos" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Accesos</h2>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <input v-model="q" type="text" placeholder="Buscar por persona o correo" class="w-full max-w-md rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="overflow-hidden rounded-lg border bg-white">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Persona</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Entrada</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Salida</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="a in accesos.data" :key="a.id">
                <td class="px-4 py-3">{{ a.persona?.Nombre ?? '—' }}</td>
                <td class="px-4 py-3">{{ a.fecha_entrada ?? '—' }}</td>
                <td class="px-4 py-3">{{ a.fecha_salida ?? '—' }}</td>
                <td class="px-4 py-3">
                  <span :class="['inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium', a.estado === 'dentro' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                    {{ a.estado ?? '—' }}
                  </span>
                </td>
              </tr>
              <tr v-if="!accesos.data?.length">
                <td colspan="4" class="px-4 py-6 text-center text-gray-500">No hay registros</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500">Mostrando {{ accesos.from ?? 0 }}–{{ accesos.to ?? 0 }} de {{ accesos.total ?? 0 }}</div>
          <div class="flex gap-2">
            <Link v-for="link in accesos.links" :key="link.url + link.label" :href="link.url || '#'" class="rounded-md px-3 py-1 text-sm" :class="[link.active ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 border']" v-html="link.label" preserve-scroll />
          </div>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>
