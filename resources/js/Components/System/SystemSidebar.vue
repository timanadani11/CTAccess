<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  menus: { type: Array, default: () => [] },
  role: { type: String, default: null },
})

const go = (routeName) => {
  if (routeName) router.visit(route(routeName))
}
</script>

<template>
  <aside class="w-64 shrink-0 border-r border-gray-200 bg-white">
    <div class="p-4 text-sm text-gray-500">Rol: <span class="font-medium text-gray-700">{{ role || '—' }}</span></div>
    <nav class="px-2 pb-4 space-y-1">
      <button
        v-for="item in menus"
        :key="item.route"
        class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100"
        @click="go(item.route)"
      >
        <span>{{ item.label }}</span>
      </button>
    </nav>
  </aside>
</template>
