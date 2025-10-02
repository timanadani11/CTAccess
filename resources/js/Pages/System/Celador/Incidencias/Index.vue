<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
  incidencias: Object,
  filters: Object,
  estadisticas: Object,
})

const q = ref(props.filters?.q ?? '')
const tipo = ref(props.filters?.tipo ?? '')
const prioridad = ref(props.filters?.prioridad ?? '')

watch([q, tipo, prioridad], ([newQ, newTipo, newPrioridad]) => {
  router.get(route('system.celador.incidencias.index'), 
    { q: newQ, tipo: newTipo, prioridad: newPrioridad }, 
    { preserveState: true, replace: true }
  )
}, { debounce: 300 })

const formatDate = (dateString) => {
  if (!dateString) return '—'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const getTipoColor = (tipo) => {
  const colores = {
    'seguridad': 'text-red-600 bg-red-50 border-red-200',
    'acceso': 'text-blue-600 bg-blue-50 border-blue-200',
    'equipamiento': 'text-purple-600 bg-purple-50 border-purple-200',
    'comportamiento': 'text-orange-600 bg-orange-50 border-orange-200',
    'otro': 'text-gray-600 bg-gray-50 border-gray-200'
  }
  return colores[tipo?.toLowerCase()] || colores['otro']
}

const getPrioridadColor = (prioridad) => {
  const colores = {
    'alta': 'text-red-600 bg-red-50 border-red-200',
    'media': 'text-yellow-600 bg-yellow-50 border-yellow-200',
    'baja': 'text-green-600 bg-green-50 border-green-200'
  }
  return colores[prioridad?.toLowerCase()] || colores['baja']
}

const getPrioridadIcon = (prioridad) => {
  const iconos = {
    'alta': 'alert-triangle',
    'media': 'alert-circle',
    'baja': 'info'
  }
  return iconos[prioridad?.toLowerCase()] || 'info'
}
</script>

<template>
  <SystemLayout>
    <Head title="Incidencias" />

    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Icon name="alert-triangle" :size="24" class="text-red-500" />
          <h2 class="text-xl font-semibold text-theme-primary">Incidencias</h2>
        </div>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Total Incidencias</p>
                <p class="text-2xl font-bold text-theme-primary mt-1">{{ estadisticas?.total ?? 0 }}</p>
              </div>
              <div class="p-3 bg-red-500/10 rounded-lg">
                <Icon name="alert-triangle" :size="24" class="text-red-500" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Prioridad Alta</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ estadisticas?.alta ?? 0 }}</p>
              </div>
              <div class="p-3 bg-red-600/10 rounded-lg">
                <Icon name="alert-circle" :size="24" class="text-red-600" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Este Mes</p>
                <p class="text-2xl font-bold text-[#FDC300] mt-1">{{ estadisticas?.mes ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#FDC300]/10 rounded-lg">
                <Icon name="calendar" :size="24" class="text-[#FDC300]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Hoy</p>
                <p class="text-2xl font-bold text-[#50E5F9] mt-1">{{ estadisticas?.hoy ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#50E5F9]/10 rounded-lg">
                <Icon name="clock" :size="24" class="text-[#50E5F9]" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="relative">
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="search" :size="16" class="inline mr-1" />
                Buscar
              </label>
              <input 
                v-model="q" 
                type="text" 
                placeholder="Buscar por descripción o persona" 
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="tag" :size="16" class="inline mr-1" />
                Tipo
              </label>
              <select 
                v-model="tipo"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
              >
                <option value="">Todos los tipos</option>
                <option value="seguridad">Seguridad</option>
                <option value="acceso">Acceso</option>
                <option value="equipamiento">Equipamiento</option>
                <option value="comportamiento">Comportamiento</option>
                <option value="otro">Otro</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="alert-circle" :size="16" class="inline mr-1" />
                Prioridad
              </label>
              <select 
                v-model="prioridad"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
              >
                <option value="">Todas las prioridades</option>
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            <div class="flex items-end">
              <button 
                @click="q = ''; tipo = ''; prioridad = ''"
                class="w-full sm:w-auto px-4 py-2.5 bg-theme-secondary text-theme-inverse rounded-lg hover:bg-theme-secondary/80 transition-all flex items-center justify-center gap-2"
              >
                <Icon name="x" :size="16" />
                Limpiar filtros
              </button>
            </div>
          </div>
        </div>

        <!-- Tabla de Incidencias -->
        <div class="bg-theme-card rounded-xl border border-theme-primary overflow-hidden shadow-theme-md">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary">
              <thead class="bg-theme-secondary/5">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    <Icon name="alert-triangle" :size="14" class="inline mr-1" />
                    Prioridad
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    <Icon name="user" :size="14" class="inline mr-1" />
                    Persona
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden md:table-cell">
                    <Icon name="tag" :size="14" class="inline mr-1" />
                    Tipo
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    <Icon name="file-text" :size="14" class="inline mr-1" />
                    Descripción
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden lg:table-cell">
                    <Icon name="user-check" :size="14" class="inline mr-1" />
                    Reportado por
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden sm:table-cell">
                    <Icon name="clock" :size="14" class="inline mr-1" />
                    Fecha
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary">
                <tr v-for="i in incidencias.data" :key="i.incidenciaId" class="hover:bg-theme-secondary/5 transition-colors">
                  <td class="px-4 py-4">
                    <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border', getPrioridadColor(i.prioridad)]">
                      <Icon :name="getPrioridadIcon(i.prioridad)" :size="14" />
                      {{ i.prioridad || 'Baja' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-semibold text-sm">
                        {{ (i.acceso?.persona?.Nombre ?? 'U').charAt(0).toUpperCase() }}
                      </div>
                      <div>
                        <p class="font-medium text-theme-primary">{{ i.acceso?.persona?.Nombre ?? '—' }}</p>
                        <p class="text-xs text-theme-secondary hidden sm:block">{{ i.acceso?.persona?.tipo_persona ?? '' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden md:table-cell">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border', getTipoColor(i.tipo)]">
                      {{ i.tipo || 'Otro' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <p class="text-sm text-theme-primary line-clamp-2 max-w-md">
                      {{ i.descripcion || '—' }}
                    </p>
                  </td>
                  <td class="px-4 py-4 hidden lg:table-cell">
                    <div class="text-sm">
                      <p class="font-medium text-theme-primary">{{ i.reportadoPor?.nombre ?? 'Sistema' }}</p>
                      <p class="text-xs text-theme-secondary">{{ i.reportadoPor?.rol ?? 'Automático' }}</p>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden sm:table-cell">
                    <div class="text-sm">
                      <p class="font-medium text-theme-primary">{{ formatDate(i.created_at).split(',')[0] }}</p>
                      <p class="text-xs text-theme-secondary">{{ formatDate(i.created_at).split(',')[1] }}</p>
                    </div>
                  </td>
                </tr>
                <tr v-if="!incidencias.data?.length">
                  <td colspan="6" class="px-4 py-12 text-center">
                    <Icon name="check-circle" :size="48" class="mx-auto text-[#39A900] mb-3" />
                    <p class="text-theme-primary font-medium">No hay incidencias registradas</p>
                    <p class="text-sm text-theme-muted mt-1">¡Excelente! Todo está funcionando correctamente</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Paginación -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <div class="text-sm text-theme-secondary">
            Mostrando <span class="font-semibold text-theme-primary">{{ incidencias.from ?? 0 }}</span> - 
            <span class="font-semibold text-theme-primary">{{ incidencias.to ?? 0 }}</span> de 
            <span class="font-semibold text-theme-primary">{{ incidencias.total ?? 0 }}</span> registros
          </div>
          <div class="flex flex-wrap gap-2 justify-center">
            <Link 
              v-for="link in incidencias.links" 
              :key="link.url + link.label" 
              :href="link.url || '#'" 
              class="min-w-[2.5rem] h-10 flex items-center justify-center rounded-lg px-3 text-sm font-medium transition-all"
              :class="[
                link.active 
                  ? 'bg-red-500 text-white shadow-lg' 
                  : 'bg-theme-card border border-theme-primary text-theme-primary hover:bg-theme-secondary/10',
                !link.url && 'opacity-50 cursor-not-allowed'
              ]" 
              v-html="link.label" 
              preserve-scroll 
            />
          </div>
        </div>

      </div>
    </div>
  </SystemLayout>
</template>
