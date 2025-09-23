<script setup>
import { router } from '@inertiajs/vue3'
const props = defineProps({
  user: { type: Object, default: null },
  role: { type: String, default: null },
})

const logout = () => router.post(route('system.logout'))
</script>

<template>
  <header class="sticky top-0 z-30 w-full border-b border-gray-200 bg-white/80 backdrop-blur">
    <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4">
      <div class="flex items-center gap-3">
        <div class="h-8 w-8 rounded bg-emerald-600" />
        <div class="text-sm font-semibold text-gray-800">CTAccess • System</div>
        <div class="text-xs text-gray-500">/ {{ role || '—' }}</div>
      </div>
      <div class="flex items-center gap-3">
        <div class="text-sm text-gray-700">{{ user?.Nombre || user?.name || 'Usuario' }}</div>
        <button class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700" @click="logout">Salir</button>
      </div>
    </div>
  </header>
</template>
