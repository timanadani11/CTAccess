<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  incidencias: Object,
  filters: Object,
})

const q = ref(props.filters?.q ?? '')
watch(q, (val) => {
  router.get(route('system.celador.incidencias.index'), { q: val }, { preserveState: true, replace: true })
})
</script>

<template>
  <SystemLayout>
    <Head title="Incidencias" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Incidencias</h2>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <input v-model="q" type="text" placeholder="Buscar por descripción" class="w-full max-w-md rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="overflow-hidden rounded-lg border bg-white">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Persona</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipo</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Descripción</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="i in incidencias.data" :key="i.incidenciaId">
                <td class="px-4 py-3">{{ i.acceso?.persona?.Nombre ?? '—' }}</td>
                <td class="px-4 py-3">{{ i.tipo ?? '—' }}</td>
                <td class="px-4 py-3">{{ i.descripcion ?? '—' }}</td>
                <td class="px-4 py-3">{{ i.created_at ?? '—' }}</td>
              </tr>
              <tr v-if="!incidencias.data?.length">
                <td colspan="4" class="px-4 py-6 text-center text-gray-500">No hay registros</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500">Mostrando {{ incidencias.from ?? 0 }}–{{ incidencias.to ?? 0 }} de {{ incidencias.total ?? 0 }}</div>
          <div class="flex gap-2">
            <Link v-for="link in incidencias.links" :key="link.url + link.label" :href="link.url || '#'" class="rounded-md px-3 py-1 text-sm" :class="[link.active ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 border']" v-html="link.label" preserve-scroll />
          </div>
        </div>
      </div>
    </div>
  </SystemLayout>
</template>
