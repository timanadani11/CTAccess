<script setup>
import { usePage, router, Head } from '@inertiajs/vue3'
import SystemSidebar from '@/Components/System/SystemSidebar.vue'
import SystemNavbar from '@/Components/System/SystemNavbar.vue'

const page = usePage()
const role = page.props.auth?.role
const menus = page.props.menus?.system ?? []
</script>

<template>
  <div class="min-h-screen bg-slate-100">
    <SystemNavbar :user="page.props.auth?.user" :role="role" />
    <div class="flex">
      <SystemSidebar :menus="menus" :role="role" />
      <main class="flex-1 p-6">
        <header v-if="$slots.header" class="mb-6 border-b border-gray-200 pb-3">
          <slot name="header" />
        </header>
        <slot />
      </main>
    </div>
  </div>
</template>
