<script setup>
import { router } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  user: { type: Object, default: null },
  role: { type: String, default: null },
  sidebarOpen: { type: Boolean, default: false },
  sidebarCollapsed: { type: Boolean, default: false },
})

const emit = defineEmits(['toggle-sidebar', 'toggle-sidebar-collapse'])

const { isDark, toggleTheme } = useTheme()

const logout = () => router.post(route('system.logout'))
const toggleSidebar = () => emit('toggle-sidebar')
const toggleSidebarCollapse = () => emit('toggle-sidebar-collapse')
</script>

<template>
  <header class="sticky top-0 z-30 w-full border-b border-forest-200 dark:border-sage-700 bg-white/95 dark:bg-sage-900/95 backdrop-blur-sm shadow-sm">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-3">
        <!-- Mobile menu button -->
        <button
          @click="toggleSidebar"
          class="lg:hidden rounded-md p-2 text-sage-400 dark:text-sage-500 hover:bg-sage-100 dark:hover:bg-sage-800 hover:text-sage-600 dark:hover:text-sage-400 focus:outline-none focus:ring-2 focus:ring-forest-500"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Desktop sidebar toggle button -->
        <button
          @click="toggleSidebarCollapse"
          class="hidden lg:block rounded-md p-2 text-sage-400 dark:text-sage-500 hover:bg-sage-100 dark:hover:bg-sage-800 hover:text-sage-600 dark:hover:text-sage-400 focus:outline-none focus:ring-2 focus:ring-forest-500"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
          </svg>
        </button>

        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-forest-600 to-forest-700 shadow-md">
          <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div class="hidden sm:block">
          <div class="text-lg font-bold text-forest-800 dark:text-forest-200">CTAccess</div>
          <div class="text-xs text-forest-600 dark:text-forest-400 -mt-1">Sistema • {{ role || '—' }}</div>
        </div>
        <div class="sm:hidden text-sm font-semibold text-forest-800 dark:text-forest-200">CTAccess</div>
      </div>
      <div class="flex items-center gap-3">
        <!-- Theme toggle button -->
        <button
          @click="toggleTheme"
          class="rounded-md p-2 text-sage-400 dark:text-sage-500 hover:bg-sage-100 dark:hover:bg-sage-800 hover:text-sage-600 dark:hover:text-sage-400 focus:outline-none focus:ring-2 focus:ring-forest-500"
          :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
        >
          <svg v-if="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
        </button>

        <div class="hidden sm:block text-sm font-medium text-sage-700 dark:text-sage-300">{{ user?.Nombre || user?.name || 'Usuario' }}</div>
        <button class="rounded-lg bg-gradient-to-r from-forest-600 to-forest-700 px-4 py-2 text-sm font-medium text-white shadow-md transition-all hover:from-forest-700 hover:to-forest-800 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-forest-500 focus:ring-offset-2" @click="logout">
          <span class="hidden sm:inline">Cerrar Sesión</span>
          <span class="sm:hidden">Salir</span>
        </button>
      </div>
    </div>
  </header>
</template>
