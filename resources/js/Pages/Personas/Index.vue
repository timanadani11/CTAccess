<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Personas</h1>

    <div class="mb-4 flex items-center gap-2">
      <button @click="fetchList" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Recargar
      </button>
      <span v-if="loading" class="text-gray-500 text-sm">Cargando...</span>
      <span v-if="error" class="text-red-600 text-sm">{{ error }}</span>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="p-3">ID</th>
            <th class="p-3">Documento</th>
            <th class="p-3">Nombre</th>
            <th class="p-3">Tipo</th>
            <th class="p-3">Portátiles</th>
            <th class="p-3">Vehículos</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in personas" :key="p.id" class="border-t">
            <td class="p-3">{{ p.id }}</td>
            <td class="p-3">{{ p.documento ?? '—' }}</td>
            <td class="p-3">{{ p.nombre }}</td>
            <td class="p-3">{{ p.tipoPersona }}</td>
            <td class="p-3">{{ p.portatiles?.length ?? 0 }}</td>
            <td class="p-3">{{ p.vehiculos?.length ?? 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 text-sm text-gray-500">
      Mostrando {{ personas.length }} registros (paginado por API)
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const personas = ref([])
const loading = ref(false)
const error = ref('')

async function fetchList() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axios.get('/api/v1/personas', {
      params: { with_relations: 1, per_page: 20 }
    })
    personas.value = data.data || []
  } catch (e) {
    error.value = e?.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

onMounted(fetchList)
</script>
