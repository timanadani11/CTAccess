<script setup>
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
  menus: { type: Array, default: () => [] },
  role: { type: String, default: null },
  collapsed: { type: Boolean, default: false },
})

const currentRoute = computed(() => route().current())

const go = (routeName) => {
  if (routeName) router.visit(route(routeName))
}

const isActiveRoute = (routeName) => {
  if (!routeName) return false
  return currentRoute.value === routeName || currentRoute.value?.startsWith(routeName)
}

const getIconName = (label) => {
  const iconMap = {
    'Dashboard': 'layout-dashboard',
    'Personas': 'users',
    'Accesos': 'key-round',
    'Verificación QR': 'qr-code',
    'Incidencias': 'alert-triangle',
    'Historial': 'clock',
    'Gestión de Usuarios': 'user-cog'
  }
  return iconMap[label] || 'circle'
}
</script>

<template>
  <aside :class="[
    'hidden lg:flex lg:shrink-0 lg:flex-col lg:border-r border-theme-primary bg-theme-sidebar shadow-theme-sm transition-all duration-300',
    collapsed ? 'lg:w-16' : 'lg:w-64'
  ]">
    <!-- Rol Badge -->
    <div v-if="!collapsed" class="flex items-center gap-3 border-b border-theme-primary bg-theme-secondary p-4">
      <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-600">
        <Icon name="user" :size="16" class="text-white" />
      </div>
      <div>
        <div class="text-xs font-medium text-theme-secondary uppercase tracking-wide">Rol Actual</div>
        <div class="text-sm font-semibold text-theme-primary">{{ role || '—' }}</div>
      </div>
    </div>

    <!-- Collapsed role indicator -->
    <div v-if="collapsed" class="flex justify-center border-b border-theme-primary bg-theme-secondary p-4">
      <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-600" :title="`Rol: ${role || '—'}`">
        <Icon name="user" :size="16" class="text-white" />
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 space-y-1 p-3">
      <button
        v-for="item in menus"
        :key="item.route"
        :class="[
          'group flex w-full items-center rounded-lg text-left text-sm font-medium transition-all duration-200',
          collapsed ? 'px-2 py-2.5 justify-center' : 'px-3 py-2.5',
          isActiveRoute(item.route)
            ? 'bg-theme-tertiary text-theme-primary shadow-theme-sm' + (collapsed ? '' : ' border-l-4 border-green-600')
            : 'text-theme-secondary hover:bg-theme-secondary hover:text-theme-primary'
        ]"
        @click="go(item.route)"
        :title="collapsed ? item.label : undefined"
      >
        <div :class="['flex items-center', collapsed ? '' : 'gap-3']">
          <div :class="[
            'flex h-8 w-8 items-center justify-center rounded-md transition-colors',
            isActiveRoute(item.route)
              ? 'bg-green-600 text-white'
              : 'bg-theme-tertiary text-theme-muted group-hover:bg-green-200 group-hover:text-green-700'
          ]">
            <Icon :name="getIconName(item.label)" :size="16" />
          </div>
          <span v-if="!collapsed" class="font-medium">{{ item.label }}</span>
        </div>
      </button>
    </nav>

    <!-- Footer -->
    <div class="border-t border-theme-primary p-3">
      <div v-if="!collapsed" class="text-xs text-theme-muted text-center">
        CTAccess v2.0
      </div>
      <div v-else class="flex justify-center">
        <div class="text-xs text-theme-muted" title="CTAccess v2.0">
          CT
        </div>
      </div>
    </div>
  </aside>

  <!-- Mobile Sidebar Placeholder -->
  <div class="lg:hidden">
    <!-- Este espacio se puede usar para un sidebar móvil desplegable en el futuro -->
  </div>
</template>
