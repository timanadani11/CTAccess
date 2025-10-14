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
    'seguridad': 'text-red-600 bg-red-600/10 border-red-600/20',
    'acceso': 'text-[#50E5F9] bg-[#50E5F9]/10 border-[#50E5F9]/20',
    'equipamiento': 'text-purple-600 bg-purple-600/10 border-purple-600/20',
    'comportamiento': 'text-[#FDC300] bg-[#FDC300]/10 border-[#FDC300]/20',
    'otro': 'text-gray-600 bg-gray-600/10 border-gray-600/20'
  }
  return colores[tipo?.toLowerCase()] || colores['otro']
}

const getTipoIcon = (tipo) => {
  const iconos = {
    'seguridad': 'shield-alert',
    'acceso': 'key',
    'equipamiento': 'tool',
    'comportamiento': 'user-x',
    'otro': 'alert-circle'
  }
  return iconos[tipo?.toLowerCase()] || 'alert-circle'
}

const getPrioridadColor = (prioridad) => {
  const colores = {
    'alta': 'text-red-600 bg-red-600/10 border-red-600/20',
    'media': 'text-[#FDC300] bg-[#FDC300]/10 border-[#FDC300]/20',
    'baja': 'text-[#39A900] bg-[#39A900]/10 border-[#39A900]/20'
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
          <Icon name="alert-triangle" :size="24" class="text-red-600" />
          <h2 class="text-xl font-semibold text-theme-primary">Incidencias</h2>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-sm text-theme-secondary">Gestión de Incidentes</span>
        </div>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-theme-card rounded-xl border border-theme-primary p-5 shadow-theme-sm hover:shadow-theme-md transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-theme-secondary">Total Incidencias</p>
                <p class="text-3xl font-bold text-theme-primary mt-2">{{ estadisticas?.total ?? 0 }}</p>
              </div>
              <div class="p-3 bg-red-600/10 rounded-lg">
                <Icon name="alert-triangle" :size="28" class="text-red-600" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-5 shadow-theme-sm hover:shadow-theme-md transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-theme-secondary">Prioridad Alta</p>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ estadisticas?.alta ?? 0 }}</p>
                <p class="text-xs text-theme-muted mt-1">Requieren atención</p>
              </div>
              <div class="p-3 bg-red-600/10 rounded-lg">
                <Icon name="alert-circle" :size="28" class="text-red-600" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-5 shadow-theme-sm hover:shadow-theme-md transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-theme-secondary">Este Mes</p>
                <p class="text-3xl font-bold text-[#FDC300] mt-2">{{ estadisticas?.mes ?? 0 }}</p>
                <p class="text-xs text-theme-muted mt-1">{{ new Date().toLocaleString('es-CO', { month: 'long' }) }}</p>
              </div>
              <div class="p-3 bg-[#FDC300]/10 rounded-lg">
                <Icon name="calendar" :size="28" class="text-[#FDC300]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-5 shadow-theme-sm hover:shadow-theme-md transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-theme-secondary">Hoy</p>
                <p class="text-3xl font-bold text-[#50E5F9] mt-2">{{ estadisticas?.hoy ?? 0 }}</p>
                <p class="text-xs text-theme-muted mt-1">Últimas 24 horas</p>
              </div>
              <div class="p-3 bg-[#50E5F9]/10 rounded-lg">
                <Icon name="clock" :size="28" class="text-[#50E5F9]" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-theme-card rounded-xl border border-theme-primary p-5 shadow-theme-sm">
          <div class="flex items-center gap-2 mb-4">
            <Icon name="filter" :size="18" class="text-theme-secondary" />
            <h3 class="text-sm font-semibold text-theme-primary uppercase tracking-wide">Filtros de Búsqueda</h3>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="relative">
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="search" :size="14" class="inline mr-1" />
                Buscar
              </label>
              <input 
                v-model="q" 
                type="text" 
                placeholder="Descripción o persona..." 
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-600/50 focus:border-red-600 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="tag" :size="14" class="inline mr-1" />
                Tipo de Incidencia
              </label>
              <select 
                v-model="tipo"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-600/50 focus:border-red-600 transition-all cursor-pointer"
              >
                <option value="">Todos los tipos</option>
                <option value="seguridad">🛡️ Seguridad</option>
                <option value="acceso">🔑 Acceso</option>
                <option value="equipamiento">🔧 Equipamiento</option>
                <option value="comportamiento">⚠️ Comportamiento</option>
                <option value="otro">📋 Otro</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="alert-circle" :size="14" class="inline mr-1" />
                Nivel de Prioridad
              </label>
              <select 
                v-model="prioridad"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-600/50 focus:border-red-600 transition-all cursor-pointer"
              >
                <option value="">Todas las prioridades</option>
                <option value="alta">🔴 Alta</option>
                <option value="media">🟡 Media</option>
                <option value="baja">🟢 Baja</option>
              </select>
            </div>

            <div class="flex items-end">
              <button 
                @click="q = ''; tipo = ''; prioridad = ''"
                class="w-full px-4 py-2.5 bg-theme-secondary text-theme-inverse rounded-lg hover:bg-theme-secondary/90 active:scale-95 transition-all flex items-center justify-center gap-2 font-medium"
              >
                <Icon name="x" :size="16" />
                Limpiar
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
                <tr v-for="i in incidencias.data" :key="i.incidenciaId" class="hover:bg-theme-secondary/5 transition-all">
                  <td class="px-4 py-4">
                    <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border', getPrioridadColor(i.prioridad)]">
                      <Icon :name="getPrioridadIcon(i.prioridad)" :size="14" />
                      <span class="capitalize">{{ i.prioridad || 'Baja' }}</span>
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-600 to-orange-500 flex items-center justify-center text-white font-bold text-base shadow-md">
                        {{ (i.acceso?.persona?.Nombre ?? 'U').charAt(0).toUpperCase() }}
                      </div>
                      <div class="min-w-0">
                        <p class="font-semibold text-theme-primary truncate">{{ i.acceso?.persona?.Nombre ?? 'Sin asignar' }}</p>
                        <p class="text-xs text-theme-secondary truncate">
                          <Icon name="user" :size="12" class="inline mr-1" />
                          {{ i.acceso?.persona?.tipo_persona || 'N/A' }}
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden md:table-cell">
                    <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border', getTipoColor(i.tipo)]">
                      <Icon :name="getTipoIcon(i.tipo)" :size="14" />
                      <span class="capitalize">{{ i.tipo || 'Otro' }}</span>
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="max-w-md">
                      <p class="text-sm text-theme-primary line-clamp-2 leading-relaxed">
                        {{ i.descripcion || 'Sin descripción' }}
                      </p>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden lg:table-cell">
                    <div class="text-sm">
                      <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#50E5F9]/10 flex items-center justify-center">
                          <Icon name="user-check" :size="14" class="text-[#50E5F9]" />
                        </div>
                        <div>
                          <p class="font-medium text-theme-primary">{{ i.reportadoPor?.nombre ?? 'Sistema' }}</p>
                          <p class="text-xs text-theme-secondary capitalize">{{ i.reportadoPor?.principalRole?.nombre ?? i.reportadoPor?.principal_role?.nombre ?? 'Automático' }}</p>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden sm:table-cell">
                    <div class="text-sm">
                      <p class="font-medium text-theme-primary flex items-center gap-1">
                        <Icon name="calendar" :size="12" class="text-theme-secondary" />
                        {{ formatDate(i.created_at).split(',')[0] }}
                      </p>
                      <p class="text-xs text-theme-secondary flex items-center gap-1 mt-1">
                        <Icon name="clock" :size="12" class="text-theme-secondary" />
                        {{ formatDate(i.created_at).split(',')[1] }}
                      </p>
                    </div>
                  </td>
                </tr>
                <tr v-if="!incidencias.data?.length">
                  <td colspan="6" class="px-4 py-16 text-center">
                    <div class="flex flex-col items-center">
                      <div class="p-4 bg-[#39A900]/10 rounded-full mb-4">
                        <Icon name="check-circle" :size="56" class="text-[#39A900]" />
                      </div>
                      <p class="text-lg text-theme-primary font-semibold mb-1">¡No hay incidencias registradas!</p>
                      <p class="text-sm text-theme-muted">Todo está funcionando correctamente en el sistema</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Paginación -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-theme-card rounded-xl border border-theme-primary p-5 shadow-theme-sm">
          <div class="flex items-center gap-2 text-sm text-theme-secondary">
            <Icon name="list" :size="16" class="text-theme-secondary" />
            <span>
              Mostrando 
              <span class="font-bold text-theme-primary">{{ incidencias.from ?? 0 }}</span> 
              a 
              <span class="font-bold text-theme-primary">{{ incidencias.to ?? 0 }}</span> 
              de 
              <span class="font-bold text-theme-primary">{{ incidencias.total ?? 0 }}</span> 
              incidencias
            </span>
          </div>
          <div class="flex flex-wrap gap-2 justify-center">
            <Link 
              v-for="link in incidencias.links" 
              :key="link.url + link.label" 
              :href="link.url || '#'" 
              class="min-w-[2.5rem] h-10 flex items-center justify-center rounded-lg px-3 text-sm font-semibold transition-all"
              :class="[
                link.active 
                  ? 'bg-red-600 text-white shadow-lg scale-105' 
                  : 'bg-theme-card border border-theme-primary text-theme-primary hover:bg-theme-secondary/10 hover:border-red-600/30 hover:text-red-600',
                !link.url && 'opacity-40 cursor-not-allowed pointer-events-none'
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
