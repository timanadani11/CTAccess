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
  <div class="min-h-screen bg-theme-primary">
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
        <div class="absolute inset-0 bg-black opacity-50"></div>
      </div>

      <!-- Mobile Sidebar -->
      <div :class="[
        'fixed inset-y-0 left-0 z-50 w-64 transform bg-theme-sidebar shadow-theme-lg transition duration-300 ease-in-out lg:hidden',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]">
        <div class="flex h-16 items-center justify-between border-b border-theme-primary px-4">
          <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-green-600 to-green-700">
              <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div class="text-sm font-bold text-theme-primary">CTAccess</div>
          </div>
          <button @click="closeSidebar" class="rounded-md p-2 text-theme-muted hover:bg-theme-tertiary hover:text-theme-secondary">
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
            class="group flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm font-medium text-theme-secondary transition-all duration-200 hover:bg-theme-secondary hover:text-theme-primary"
            @click="() => { router.visit(route(item.route)); closeSidebar(); }"
          >
            <div class="flex items-center gap-3">
              <div class="flex h-8 w-8 items-center justify-center rounded-md bg-theme-tertiary text-theme-muted transition-colors group-hover:bg-green-200 group-hover:text-green-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <span class="font-medium">{{ item.label }}</span>
            </div>
          </button>
        </nav>

        <!-- Mobile Footer -->
        <div class="border-t border-theme-primary p-3">
          <div class="text-xs text-theme-muted text-center">CTAccess v2.0</div>
        </div>
      </div>

      <!-- Main Content -->
      <main class="flex-1 min-w-0 p-4 sm:p-6">
        <header v-if="$slots.header" class="mb-6 border-b border-theme-primary pb-4">
          <slot name="header" />
        </header>
        <slot />
      </main>
    </div>

    <!-- PWA Install Prompt -->
    <PWAInstallPrompt />
  </div>
</template>
