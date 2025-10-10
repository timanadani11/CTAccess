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
    <div class="flex h-16 items-center justify-between px-2 sm:px-4 lg:px-6">
      <div class="flex items-center gap-2 sm:gap-3">
        <!-- Mobile menu button - Más a la izquierda e interactivo -->
        <button
          @click="toggleSidebar"
          class="lg:hidden group relative overflow-hidden rounded-xl p-2.5 text-theme-muted hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-300 hover:scale-110"
          :class="sidebarOpen ? 'bg-red-500 hover:bg-red-600' : 'bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700'"
        >
          <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <Icon :name="sidebarOpen ? 'x' : 'menu'" :size="24" class="relative z-10 transition-transform duration-300 group-hover:rotate-180" />
          <span class="sr-only">{{ sidebarOpen ? 'Cerrar menú' : 'Abrir menú' }}</span>
        </button>

        <!-- Desktop sidebar toggle button - Más a la izquierda e interactivo -->
        <button
          @click="toggleSidebarCollapse"
          class="hidden lg:flex group relative overflow-hidden rounded-xl p-2.5 bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-300 hover:scale-110 shadow-md hover:shadow-lg"
        >
          <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <Icon
            :name="sidebarCollapsed ? 'chevron-right' : 'chevron-left'"
            :size="22"
            class="relative z-10 transition-transform duration-300"
            :class="sidebarCollapsed ? 'group-hover:translate-x-0.5' : 'group-hover:-translate-x-0.5'"
          />
          <span class="sr-only">{{ sidebarCollapsed ? 'Expandir menú' : 'Contraer menú' }}</span>

          <!-- Tooltip -->
          <div class="absolute left-full ml-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none shadow-lg">
            {{ sidebarCollapsed ? 'Expandir menú lateral' : 'Contraer menú lateral' }}
            <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-gray-900"></div>
          </div>
        </button>

        <div class="hidden sm:flex items-center gap-3 ml-2">
          <ApplicationLogo classes="h-10 w-auto object-contain" alt="CTAccess Logo" />
          <div class="hidden md:block">
            <div class="text-xs text-theme-secondary">Sistema • {{ role || '—' }}</div>
          </div>
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
