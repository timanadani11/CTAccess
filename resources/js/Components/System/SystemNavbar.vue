<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Icon from '@/Components/Icon.vue'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  user: { type: Object, default: null },
  role: { type: String, default: null },
  sidebarOpen: { type: Boolean, default: false },
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
          <Icon :name="sidebarOpen ? 'x' : 'menu'" :size="24" />
        </button>

        <!-- Desktop sidebar toggle button -->
        <button
          @click="toggleSidebarCollapse"
          class="hidden lg:block rounded-md p-2 text-sage-400 dark:text-sage-500 hover:bg-sage-100 dark:hover:bg-sage-800 hover:text-sage-600 dark:hover:text-sage-400 focus:outline-none focus:ring-2 focus:ring-forest-500"
        >
          <Icon :name="sidebarCollapsed ? 'chevron-right' : 'chevron-left'" :size="20" />
        </button>

        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-forest-600 to-forest-700 shadow-md">
          <Icon name="shield" :size="24" class="text-white" />
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
          <Icon :name="isDark ? 'sun' : 'moon'" :size="20" />
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
