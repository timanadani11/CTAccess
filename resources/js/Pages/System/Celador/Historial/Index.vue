<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
  registros: Object,
  filters: Object,
  estadisticas: Object,
})

// Filtros de fecha
const fechaDesde = ref(props.filters?.fecha_desde ?? '')
const fechaHasta = ref(props.filters?.fecha_hasta ?? '')
const periodo = ref(props.filters?.periodo ?? '')
const q = ref(props.filters?.q ?? '')
const generandoPDF = ref(false)

// Aplicar filtros cuando cambien
watch([fechaDesde, fechaHasta, periodo, q], ([desde, hasta, per, search]) => {
  router.get(route('system.celador.historial.index'), 
    { 
      fecha_desde: desde, 
      fecha_hasta: hasta, 
      periodo: per,
      q: search
    }, 
    { preserveState: true, replace: true }
  )
}, { debounce: 300 })

// Configurar período predefinido
const setPeriodo = (periodoSeleccionado) => {
  const hoy = new Date()
  let desde, hasta

  switch(periodoSeleccionado) {
    case 'hoy':
      desde = hoy.toISOString().split('T')[0]
      hasta = hoy.toISOString().split('T')[0]
      break
    case 'ayer':
      const ayer = new Date(hoy)
      ayer.setDate(ayer.getDate() - 1)
      desde = ayer.toISOString().split('T')[0]
      hasta = ayer.toISOString().split('T')[0]
      break
    case 'semana':
      const inicioSemana = new Date(hoy)
      inicioSemana.setDate(hoy.getDate() - hoy.getDay())
      desde = inicioSemana.toISOString().split('T')[0]
      hasta = hoy.toISOString().split('T')[0]
      break
    case 'mes':
      desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0]
      hasta = hoy.toISOString().split('T')[0]
      break
    case 'mes_anterior':
      const mesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1)
      const finMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0)
      desde = mesAnterior.toISOString().split('T')[0]
      hasta = finMesAnterior.toISOString().split('T')[0]
      break
  }

  fechaDesde.value = desde
  fechaHasta.value = hasta
  periodo.value = periodoSeleccionado
}

// Generar PDF
const generarPDF = async () => {
  generandoPDF.value = true
  
  try {
    const params = new URLSearchParams({
      fecha_desde: fechaDesde.value || '',
      fecha_hasta: fechaHasta.value || '',
      q: q.value || ''
    })
    
    // Abrir en nueva pestaña
    window.open(
      route('system.celador.historial.export-pdf') + '?' + params.toString(),
      '_blank'
    )
  } catch (error) {
    console.error('Error al generar PDF:', error)
  } finally {
    generandoPDF.value = false
  }
}

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
  const diff = Math.floor((end - start) / 1000 / 60)
  
  if (diff < 60) return `${diff}m`
  const hours = Math.floor(diff / 60)
  const mins = diff % 60
  return `${hours}h ${mins}m`
}

const periodoActivo = computed(() => {
  if (!fechaDesde.value && !fechaHasta.value) return 'Todos los registros'
  if (periodo.value === 'hoy') return 'Hoy'
  if (periodo.value === 'ayer') return 'Ayer'
  if (periodo.value === 'semana') return 'Esta semana'
  if (periodo.value === 'mes') return 'Este mes'
  if (periodo.value === 'mes_anterior') return 'Mes anterior'
  if (fechaDesde.value && fechaHasta.value) {
    return `${fechaDesde.value} al ${fechaHasta.value}`
  }
  return 'Período personalizado'
})
</script>

<template>
  <SystemLayout>
    <Head title="Reportes de Accesos" />

    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Icon name="file-text" :size="24" class="text-[#50E5F9]" />
          <h2 class="text-xl font-semibold text-theme-primary">Reportes de Accesos</h2>
        </div>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl space-y-6">
        
        <!-- Banner de Información -->
        <div class="bg-gradient-to-r from-[#50E5F9]/10 to-[#39A900]/10 border border-[#50E5F9]/30 rounded-xl p-6">
          <div class="flex items-start gap-4">
            <div class="p-3 bg-[#50E5F9]/20 rounded-lg">
              <Icon name="info" :size="24" class="text-[#50E5F9]" />
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-theme-primary mb-2">
                Sistema de Reportes de Accesos
              </h3>
              <p class="text-sm text-theme-secondary">
                Genera reportes detallados en PDF con toda la información de accesos. 
                Selecciona un período predefinido o personaliza las fechas. 
                Los reportes incluyen estadísticas, gráficos y listado completo de movimientos.
              </p>
            </div>
          </div>
        </div>

        <!-- Estadísticas del Período -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Total Accesos</p>
                <p class="text-2xl font-bold text-theme-primary mt-1">{{ estadisticas?.total ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#50E5F9]/10 rounded-lg">
                <Icon name="users" :size="24" class="text-[#50E5F9]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Con Portátil</p>
                <p class="text-2xl font-bold text-[#39A900] mt-1">{{ estadisticas?.con_portatil ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#39A900]/10 rounded-lg">
                <Icon name="laptop" :size="24" class="text-[#39A900]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Con Vehículo</p>
                <p class="text-2xl font-bold text-[#FDC300] mt-1">{{ estadisticas?.con_vehiculo ?? 0 }}</p>
              </div>
              <div class="p-3 bg-[#FDC300]/10 rounded-lg">
                <Icon name="car" :size="24" class="text-[#FDC300]" />
              </div>
            </div>
          </div>

          <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-theme-secondary">Personas Únicas</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ estadisticas?.personas_unicas ?? 0 }}</p>
              </div>
              <div class="p-3 bg-purple-600/10 rounded-lg">
                <Icon name="user-check" :size="24" class="text-purple-600" />
              </div>
            </div>
          </div>
        </div>

        <!-- Períodos Predefinidos -->
        <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <h3 class="text-sm font-semibold text-theme-primary mb-3 flex items-center gap-2">
            <Icon name="calendar" :size="16" />
            Períodos Rápidos
          </h3>
          <div class="flex flex-wrap gap-2">
            <button 
              @click="setPeriodo('hoy')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                periodo === 'hoy' 
                  ? 'bg-[#50E5F9] text-white shadow-lg' 
                  : 'bg-theme-secondary/10 text-theme-primary hover:bg-theme-secondary/20 border border-theme-primary'
              ]"
            >
              <Icon name="clock" :size="14" class="inline mr-1" />
              Hoy
            </button>
            <button 
              @click="setPeriodo('ayer')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                periodo === 'ayer' 
                  ? 'bg-[#50E5F9] text-white shadow-lg' 
                  : 'bg-theme-secondary/10 text-theme-primary hover:bg-theme-secondary/20 border border-theme-primary'
              ]"
            >
              <Icon name="history" :size="14" class="inline mr-1" />
              Ayer
            </button>
            <button 
              @click="setPeriodo('semana')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                periodo === 'semana' 
                  ? 'bg-[#50E5F9] text-white shadow-lg' 
                  : 'bg-theme-secondary/10 text-theme-primary hover:bg-theme-secondary/20 border border-theme-primary'
              ]"
            >
              <Icon name="calendar" :size="14" class="inline mr-1" />
              Esta Semana
            </button>
            <button 
              @click="setPeriodo('mes')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                periodo === 'mes' 
                  ? 'bg-[#50E5F9] text-white shadow-lg' 
                  : 'bg-theme-secondary/10 text-theme-primary hover:bg-theme-secondary/20 border border-theme-primary'
              ]"
            >
              <Icon name="calendar" :size="14" class="inline mr-1" />
              Este Mes
            </button>
            <button 
              @click="setPeriodo('mes_anterior')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                periodo === 'mes_anterior' 
                  ? 'bg-[#50E5F9] text-white shadow-lg' 
                  : 'bg-theme-secondary/10 text-theme-primary hover:bg-theme-secondary/20 border border-theme-primary'
              ]"
            >
              <Icon name="history" :size="14" class="inline mr-1" />
              Mes Anterior
            </button>
          </div>
        </div>

        <!-- Filtros Personalizados -->
        <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <h3 class="text-sm font-semibold text-theme-primary mb-3 flex items-center gap-2">
            <Icon name="filter" :size="16" />
            Filtros Personalizados
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                Fecha Desde
              </label>
              <input 
                v-model="fechaDesde" 
                type="date"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#50E5F9] transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                Fecha Hasta
              </label>
              <input 
                v-model="fechaHasta" 
                type="date"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#50E5F9] transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-theme-secondary mb-2">
                <Icon name="search" :size="14" class="inline mr-1" />
                Buscar Persona
              </label>
              <input 
                v-model="q" 
                type="text"
                placeholder="Nombre o documento"
                class="w-full rounded-lg border-theme-primary bg-theme-primary text-theme-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#50E5F9] transition-all"
              />
            </div>

            <div class="flex items-end">
              <button 
                @click="fechaDesde = ''; fechaHasta = ''; periodo = ''; q = ''"
                class="w-full px-4 py-2.5 bg-theme-secondary text-theme-inverse rounded-lg hover:bg-theme-secondary/80 transition-all flex items-center justify-center gap-2"
              >
                <Icon name="x" :size="16" />
                Limpiar
              </button>
            </div>
          </div>
        </div>

        <!-- Botón de Generar PDF -->
        <div class="bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
              <p class="text-sm text-theme-secondary">
                Período: <span class="font-semibold text-theme-primary">{{ periodoActivo }}</span>
                <span v-if="estadisticas?.total" class="text-theme-muted">
                  • {{ estadisticas.total }} registros
                </span>
              </p>
            </div>
            <button 
              @click="generarPDF"
              :disabled="generandoPDF || !estadisticas?.total"
              class="px-6 py-2.5 bg-[#39A900] text-white rounded-lg font-medium text-sm hover:bg-[#39A900]/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
            >
              <Icon :name="generandoPDF ? 'loader' : 'download'" :size="16" :class="generandoPDF && 'animate-spin'" />
              {{ generandoPDF ? 'Generando...' : 'Descargar PDF' }}
            </button>
          </div>
        </div>

        <!-- Preview de Datos -->
        <div class="bg-theme-card rounded-xl border border-theme-primary overflow-hidden shadow-theme-md">
          <div class="bg-theme-secondary/5 px-6 py-4 border-b border-theme-primary">
            <h3 class="text-lg font-semibold text-theme-primary flex items-center gap-2">
              <Icon name="eye" :size="20" />
              Vista Previa de Datos
            </h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-primary">
              <thead class="bg-theme-secondary/5">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary">
                    <Icon name="user" :size="14" class="inline mr-1" />
                    Persona
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
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden lg:table-cell">
                    <Icon name="clock" :size="14" class="inline mr-1" />
                    Duración
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-theme-secondary hidden xl:table-cell">
                    Recursos
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-theme-primary">
                <tr v-for="r in registros.data" :key="r.id" class="hover:bg-theme-secondary/5 transition-colors">
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#50E5F9] to-[#39A900] flex items-center justify-center text-white font-semibold text-sm">
                        {{ (r.persona?.Nombre ?? 'U').charAt(0).toUpperCase() }}
                      </div>
                      <div>
                        <p class="font-medium text-theme-primary">{{ r.persona?.Nombre ?? '—' }}</p>
                        <p class="text-xs text-theme-secondary hidden sm:block">{{ r.persona?.numero_documento ?? '' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden md:table-cell">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#50E5F9]/10 text-[#50E5F9]">
                      {{ r.persona?.tipo_persona ?? '—' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm">
                      <p class="font-medium text-theme-primary">{{ formatDate(r.fecha_entrada).split(',')[0] }}</p>
                      <p class="text-xs text-theme-secondary">{{ formatDate(r.fecha_entrada).split(',')[1] }}</p>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden sm:table-cell">
                    <div v-if="r.fecha_salida" class="text-sm">
                      <p class="font-medium text-theme-primary">{{ formatDate(r.fecha_salida).split(',')[0] }}</p>
                      <p class="text-xs text-theme-secondary">{{ formatDate(r.fecha_salida).split(',')[1] }}</p>
                    </div>
                    <span v-else class="text-theme-muted">—</span>
                  </td>
                  <td class="px-4 py-4 text-sm text-theme-secondary hidden lg:table-cell">
                    <span class="font-mono">{{ calcularDuracion(r.fecha_entrada, r.fecha_salida) }}</span>
                  </td>
                  <td class="px-4 py-4 hidden xl:table-cell">
                    <div class="flex items-center gap-2">
                      <span v-if="r.portatil" class="inline-flex items-center gap-1 text-xs text-[#39A900]">
                        <Icon name="laptop" :size="14" />
                      </span>
                      <span v-if="r.vehiculo" class="inline-flex items-center gap-1 text-xs text-[#FDC300]">
                        <Icon name="car" :size="14" />
                      </span>
                      <span v-if="!r.portatil && !r.vehiculo" class="text-theme-muted">—</span>
                    </div>
                  </td>
                </tr>
                <tr v-if="!registros.data?.length">
                  <td colspan="6" class="px-4 py-12 text-center">
                    <Icon name="inbox" :size="48" class="mx-auto text-theme-muted mb-3" />
                    <p class="text-theme-secondary font-medium">No hay registros en este período</p>
                    <p class="text-sm text-theme-muted mt-1">Selecciona un rango de fechas diferente</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Paginación -->
        <div v-if="registros.data?.length" class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-theme-card rounded-xl border border-theme-primary p-4 shadow-theme-sm">
          <div class="text-sm text-theme-secondary">
            Mostrando <span class="font-semibold text-theme-primary">{{ registros.from ?? 0 }}</span> - 
            <span class="font-semibold text-theme-primary">{{ registros.to ?? 0 }}</span> de 
            <span class="font-semibold text-theme-primary">{{ registros.total ?? 0 }}</span> registros
          </div>
          <div class="flex flex-wrap gap-2 justify-center">
            <Link 
              v-for="link in registros.links" 
              :key="link.url + link.label" 
              :href="link.url || '#'" 
              class="min-w-[2.5rem] h-10 flex items-center justify-center rounded-lg px-3 text-sm font-medium transition-all"
              :class="[
                link.active 
                  ? 'bg-[#50E5F9] text-white shadow-lg' 
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
