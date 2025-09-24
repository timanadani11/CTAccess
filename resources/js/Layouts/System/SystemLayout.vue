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
                <!-- Dynamic icons for mobile menu -->
                <svg v-if="item.icon === 'heroicon-m-home'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <svg v-else-if="item.icon === 'heroicon-m-user-group'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <svg v-else-if="item.icon === 'heroicon-m-users'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <!-- Add other icons as needed... -->
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
