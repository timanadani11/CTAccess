<script setup>
import { usePage, router, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import SystemSidebar from '@/Components/System/SystemSidebar.vue'
import SystemNavbar from '@/Components/System/SystemNavbar.vue'
import PWAInstallPrompt from '@/Components/System/PWAInstallPrompt.vue'

const page = usePage()
const role = page.props.auth?.role
const menus = page.props.menus?.system ?? []
const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const toggleSidebarCollapse = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
  localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value.toString())
}

const closeSidebar = () => {
  sidebarOpen.value = false
}

// Load saved sidebar state
const loadSidebarState = () => {
  const saved = localStorage.getItem('sidebarCollapsed')
  if (saved !== null) {
    sidebarCollapsed.value = saved === 'true'
  }
}

// Initialize sidebar state on mount
onMounted(() => {
  loadSidebarState()
})
</script>

<template>
  <div class="min-h-screen bg-forest-50 dark:bg-sage-950">
    <SystemNavbar
      :user="page.props.auth?.user"
      :role="role"
      :sidebar-open="sidebarOpen"
      :sidebar-collapsed="sidebarCollapsed"
      @toggle-sidebar="toggleSidebar"
      @toggle-sidebar-collapse="toggleSidebarCollapse"
    />

    <div class="flex">
      <!-- Desktop Sidebar -->
      <SystemSidebar :menus="menus" :role="role" :collapsed="sidebarCollapsed" />

      <!-- Mobile Sidebar Overlay -->
      <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="closeSidebar">
        <div class="absolute inset-0 bg-sage-600 opacity-75"></div>
      </div>

      <!-- Mobile Sidebar -->
      <div :class="[
        'fixed inset-y-0 left-0 z-50 w-64 transform bg-white dark:bg-sage-900 shadow-xl transition duration-300 ease-in-out lg:hidden',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]">
        <div class="flex h-16 items-center justify-between border-b border-forest-200 dark:border-sage-700 px-4">
          <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-forest-600 to-forest-700">
              <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div class="text-sm font-bold text-forest-800 dark:text-forest-200">CTAccess</div>
          </div>
          <button @click="closeSidebar" class="rounded-md p-2 text-sage-400 dark:text-sage-500 hover:bg-sage-100 dark:hover:bg-sage-800 hover:text-sage-600 dark:hover:text-sage-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Menu Items -->
        <nav class="flex-1 space-y-1 p-3">
          <button
            v-for="item in menus"
            :key="item.route"
            class="group flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-medium text-sage-700 dark:text-sage-300 transition-all duration-200 hover:bg-forest-50 dark:hover:bg-sage-800 hover:text-forest-700 dark:hover:text-forest-400"
            @click="() => { router.visit(route(item.route)); closeSidebar(); }"
          >
            <div class="flex items-center gap-3">
              <div class="flex h-8 w-8 items-center justify-center rounded-md bg-sage-100 dark:bg-sage-700 text-sage-600 dark:text-sage-400 transition-colors group-hover:bg-forest-200 dark:group-hover:bg-forest-700 group-hover:text-forest-700 dark:group-hover:text-forest-300">
                <!-- Dashboard Icon -->
                <svg v-if="item.label === 'Dashboard'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <!-- Personas Icon -->
                <svg v-else-if="item.label === 'Personas'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <!-- Accesos Icon -->
                <svg v-else-if="item.label === 'Accesos'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <!-- QR Icon -->
                <svg v-else-if="item.label === 'Verificación QR'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <!-- Incidencias Icon -->
                <svg v-else-if="item.label === 'Incidencias'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <!-- Historial Icon -->
                <svg v-else-if="item.label === 'Historial'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <!-- Gestión de Usuarios Icon -->
                <svg v-else-if="item.label === 'Gestión de Usuarios'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <!-- Default Icon -->
                <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <span class="font-medium">{{ item.label }}</span>
            </div>
          </button>
        </nav>

        <!-- Mobile Footer -->
        <div class="border-t border-forest-100 dark:border-sage-700 p-3">
          <div class="text-xs text-sage-500 dark:text-sage-400 text-center">CTAccess v2.0</div>
        </div>
      </div>

      <!-- Main Content -->
      <main class="flex-1 min-w-0 p-4 sm:p-6">
        <header v-if="$slots.header" class="mb-6 border-b border-forest-200 dark:border-sage-700 pb-4">
          <slot name="header" />
        </header>
        <slot />
      </main>
    </div>

    <!-- PWA Install Prompt -->
    <PWAInstallPrompt />
  </div>
</template>
