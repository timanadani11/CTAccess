<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Icon from '@/Components/Icon.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
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
  <header class="sticky top-0 z-30 w-full border-b border-theme-primary bg-theme-navbar/95 backdrop-blur-sm shadow-theme-sm">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-3">
        <!-- Mobile menu button -->
        <button
          @click="toggleSidebar"
          class="lg:hidden rounded-md p-2 text-theme-muted hover:bg-theme-tertiary hover:text-theme-secondary focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          <Icon :name="sidebarOpen ? 'x' : 'menu'" :size="24" />
        </button>

        <!-- Desktop sidebar toggle button -->
        <button
          @click="toggleSidebarCollapse"
          class="hidden lg:block rounded-md p-2 text-theme-muted hover:bg-theme-tertiary hover:text-theme-secondary focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          <Icon :name="sidebarCollapsed ? 'chevron-right' : 'chevron-left'" :size="20" />
        </button>

        <ApplicationLogo classes="h-10 w-auto object-contain" alt="CTAccess Logo" />
        <div class="hidden sm:block">
          <div class="text-xs text-theme-secondary">Sistema • {{ role || '—' }}</div>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <!-- Theme toggle button -->
        <button
          @click="toggleTheme"
          class="rounded-md p-2 text-theme-muted hover:bg-theme-tertiary hover:text-theme-secondary focus:outline-none focus:ring-2 focus:ring-green-500"
          :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
        >
          <Icon :name="isDark ? 'sun' : 'moon'" :size="20" />
        </button>

        <div class="hidden sm:block text-sm font-medium text-theme-secondary">{{ user?.Nombre || user?.name || 'Usuario' }}</div>
        <button class="rounded-lg bg-gradient-to-r from-forest-600 to-forest-700 px-4 py-2 text-sm font-medium text-white shadow-md transition-all hover:from-forest-700 hover:to-forest-800 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-forest-500 focus:ring-offset-2" @click="logout">
          <span class="hidden sm:inline">Cerrar Sesión</span>
          <span class="sm:hidden">Salir</span>
        </button>
      </div>
    </div>
  </header>
</template>
