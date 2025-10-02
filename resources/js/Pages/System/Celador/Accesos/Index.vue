<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
  accesos: Object,
  filters: Object,
  estadisticas: Object,
})

const q = ref(props.filters?.q ?? '')
const estado = ref(props.filters?.estado ?? '')

watch([q, estado], ([newQ, newEstado]) => {
  router.get(route('system.celador.accesos.index'), 
    { q: newQ, estado: newEstado }, 
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

const calcularDuracion = (entrada, salida) => {
  if (!entrada) return '—'
  const start = new Date(entrada)
  const end = salida ? new Date(salida) : new Date()
  const diff = Math.floor((end - start) / 1000 / 60) // minutos
  
  if (diff < 60) return `${diff}m`
  const hours = Math.floor(diff / 60)
  const mins = diff % 60
  return `${hours}h ${mins}m`
}
</script>

<template>
  <SystemLayout>
    <Head title="Accesos" />

    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Icon name="log-in" :size="24" class="text-[#39A900]" />
          <h2 class="text-xl font-semibold text-theme-primary">Accesos</h2>
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
                <p class="text-sm text-theme-secondary">Total Accesos</p>
                <p class="text-2xl font-bold text-theme-primary mt-1">{{ estadisticas?.total ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#39A900]/10 rounded-lg">
                <Icon name="users" :size="24" class="text-[#39A900]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Activos</p>
                <p class="text-2xl font-bold text-[#39A900] mt-1">{{ estadisticas?.activos ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#39A900]/10 rounded-lg">
                <Icon name="check-circle" :size="24" class="text-[#39A900]" />
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
                <Icon name="calendar" :size="24" class="text-[#50E5F9]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Finalizados</p>
                <p class="text-2xl font-bold text-theme-secondary mt-1">{{ estadisticas?.finalizados ?? 0 }}</p>
              </div>
              <div class="p-3 bg-theme-secondary/10 rounded-lg">
                <Icon name="check" :size="24" class="text-theme-secondary" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="relative">
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="search" :size="16" class="inline mr-1" />
                Buscar
              </label>
              <input 
                v-model="q" 
                type="text" 
                placeholder="Buscar por persona, documento o correo" 
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="filter" :size="16" class="inline mr-1" />
                Estado
              </label>
              <select 
                v-model="estado"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#39A900] transition-all"
              >
                <option value="">Todos los estados</option>
                <option value="activo">Activos (Dentro)</option>
                <option value="finalizado">Finalizados (Salida registrada)</option>
              </select>
            </div>

            <div class="flex items-end">
              <button 
                @click="q = ''; estado = ''"
                class="w-full sm:w-auto px-4 py-2.5 bg-theme-secondary text-theme-inverse rounded-lg hover:bg-theme-secondary/80 transition-all flex items-center justify-center gap-2"
              >
                <Icon name="x" :size="16" />
                Limpiar filtros
              </button>
            </div>
          </div>
        </div>

        <!-- Tabla de Accesos -->
        <div class="bg-theme-card rounded-xl border border-theme-primary overflow-hidden shadow-theme-md">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary">
              <thead class="bg-theme-secondary/5">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    <Icon name="user" :size="14" class="inline mr-1" />
                    Persona
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden lg:table-cell">
                    <Icon name="badge" :size="14" class="inline mr-1" />
                    Documento
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden md:table-cell">
                    <Icon name="briefcase" :size="14" class="inline mr-1" />
                    Tipo
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    <Icon name="log-in" :size="14" class="inline mr-1" />
                    Entrada
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden sm:table-cell">
                    <Icon name="log-out" :size="14" class="inline mr-1" />
                    Salida
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden xl:table-cell">
                    <Icon name="clock" :size="14" class="inline mr-1" />
                    Duración
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    Estado
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden lg:table-cell">
                    Recursos
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary">
                <tr v-for="a in accesos.data" :key="a.id" class="hover:bg-theme-secondary/5 transition-colors">
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#39A900] to-[#50E5F9] flex items-center justify-center text-white font-semibold text-sm">
                        {{ (a.persona?.Nombre ?? 'U').charAt(0).toUpperCase() }}
                      </div>
                      <div>
                        <p class="font-medium text-theme-primary">{{ a.persona?.Nombre ?? '—' }}</p>
                        <p class="text-xs text-theme-secondary hidden sm:block">{{ a.persona?.correo ?? '' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-sm text-theme-primary hidden lg:table-cell">
                    {{ a.persona?.numero_documento ?? '—' }}
                  </td>
                  <td class="px-4 py-4 hidden md:table-cell">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#50E5F9]/10 text-[#50E5F9]">
                      {{ a.persona?.tipo_persona ?? '—' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm">
                      <p class="font-medium text-theme-primary">{{ formatDate(a.fecha_entrada).split(',')[0] }}</p>
                      <p class="text-xs text-theme-secondary">{{ formatDate(a.fecha_entrada).split(',')[1] }}</p>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden sm:table-cell">
                    <div v-if="a.fecha_salida" class="text-sm">
                      <p class="font-medium text-theme-primary">{{ formatDate(a.fecha_salida).split(',')[0] }}</p>
                      <p class="text-xs text-theme-secondary">{{ formatDate(a.fecha_salida).split(',')[1] }}</p>
                    </div>
                    <span v-else class="text-theme-muted">—</span>
                  </td>
                  <td class="px-4 py-4 text-sm text-theme-secondary hidden xl:table-cell">
                    <span class="font-mono">{{ calcularDuracion(a.fecha_entrada, a.fecha_salida) }}</span>
                  </td>
                  <td class="px-4 py-4">
                    <span v-if="a.estado === 'activo'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#39A900]/10 text-[#39A900] border border-[#39A900]/20">
                      <Icon name="check-circle" :size="14" />
                      Activo
                    </span>
                    <span v-else class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-theme-secondary/10 text-theme-secondary border border-theme-secondary/20">
                      <Icon name="x-circle" :size="14" />
                      Finalizado
                    </span>
                  </td>
                  <td class="px-4 py-4 hidden lg:table-cell">
                    <div class="flex items-center gap-2">
                      <span v-if="a.portatil" class="inline-flex items-center gap-1 text-xs text-[#50E5F9]">
                        <Icon name="laptop" :size="14" />
                      </span>
                      <span v-if="a.vehiculo" class="inline-flex items-center gap-1 text-xs text-[#FDC300]">
                        <Icon name="car" :size="14" />
                      </span>
                      <span v-if="!a.portatil && !a.vehiculo" class="text-theme-muted">—</span>
                    </div>
                  </td>
                </tr>
                <tr v-if="!accesos.data?.length">
                  <td colspan="8" class="px-4 py-12 text-center">
                    <Icon name="inbox" :size="48" class="mx-auto text-theme-muted mb-3" />
                    <p class="text-theme-secondary font-medium">No hay registros de accesos</p>
                    <p class="text-sm text-theme-muted mt-1">Los accesos registrados aparecerán aquí</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Paginación -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <div class="text-sm text-theme-secondary">
            Mostrando <span class="font-semibold text-theme-primary">{{ accesos.from ?? 0 }}</span> - 
            <span class="font-semibold text-theme-primary">{{ accesos.to ?? 0 }}</span> de 
            <span class="font-semibold text-theme-primary">{{ accesos.total ?? 0 }}</span> registros
          </div>
          <div class="flex flex-wrap gap-2 justify-center">
            <Link 
              v-for="link in accesos.links" 
              :key="link.url + link.label" 
              :href="link.url || '#'" 
              class="min-w-[2.5rem] h-10 flex items-center justify-center rounded-lg px-3 text-sm font-medium transition-all"
              :class="[
                link.active 
                  ? 'bg-[#39A900] text-white shadow-lg' 
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
