<script setup>
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

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
</script>

<template>
  <aside :class="[
    'hidden lg:flex lg:shrink-0 lg:flex-col lg:border-r lg:border-forest-200 dark:lg:border-sage-700 lg:bg-white dark:lg:bg-sage-900 lg:shadow-sm transition-all duration-300',
    collapsed ? 'lg:w-16' : 'lg:w-64'
  ]">
    <!-- Rol Badge -->
    <div v-if="!collapsed" class="flex items-center gap-3 border-b border-forest-100 dark:border-sage-700 bg-forest-50 dark:bg-sage-800 p-4">
      <div class="flex h-8 w-8 items-center justify-center rounded-full bg-forest-600">
        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </div>
      <div>
        <div class="text-xs font-medium text-forest-600 dark:text-forest-400 uppercase tracking-wide">Rol Actual</div>
        <div class="text-sm font-semibold text-forest-800 dark:text-forest-200">{{ role || '—' }}</div>
      </div>
    </div>

    <!-- Collapsed role indicator -->
    <div v-if="collapsed" class="flex justify-center border-b border-forest-100 dark:border-sage-700 bg-forest-50 dark:bg-sage-800 p-4">
      <div class="flex h-8 w-8 items-center justify-center rounded-full bg-forest-600" :title="`Rol: ${role || '—'}`">
        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
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
            ? 'bg-forest-100 dark:bg-forest-800 text-forest-800 dark:text-forest-200 shadow-sm' + (collapsed ? '' : ' border-l-4 border-forest-600')
            : 'text-sage-700 dark:text-sage-300 hover:bg-forest-50 dark:hover:bg-sage-800 hover:text-forest-700 dark:hover:text-forest-400'
        ]"
        @click="go(item.route)"
        :title="collapsed ? item.label : undefined"
      >
        <div :class="['flex items-center', collapsed ? '' : 'gap-3']">
          <div :class="[
            'flex h-8 w-8 items-center justify-center rounded-md transition-colors',
            isActiveRoute(item.route)
              ? 'bg-forest-600 text-white'
              : 'bg-sage-100 dark:bg-sage-700 text-sage-600 dark:text-sage-400 group-hover:bg-forest-200 dark:group-hover:bg-forest-700 group-hover:text-forest-700 dark:group-hover:text-forest-300'
          ]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <span v-if="!collapsed" class="font-medium">{{ item.label }}</span>
        </div>
      </button>
    </nav>

    <!-- Footer -->
    <div class="border-t border-forest-100 dark:border-sage-700 p-3">
      <div v-if="!collapsed" class="text-xs text-sage-500 dark:text-sage-400 text-center">
        CTAccess v2.0
      </div>
      <div v-else class="flex justify-center">
        <div class="text-xs text-sage-500 dark:text-sage-400" title="CTAccess v2.0">
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
