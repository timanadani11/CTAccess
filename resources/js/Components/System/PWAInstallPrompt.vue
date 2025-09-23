<script setup>
import { usePWA } from '@/composables/usePWA'

const { isInstallable, showInstallPrompt, installApp, dismissInstallPrompt } = usePWA()
</script>

<template>
  <div
    v-if="showInstallPrompt"
    class="fixed bottom-4 left-4 right-4 z-50 mx-auto max-w-sm transform transition-all duration-300 sm:left-auto sm:right-6 sm:max-w-sm"
  >
    <div class="overflow-hidden rounded-2xl border border-forest-200 dark:border-sage-700 bg-white dark:bg-sage-800 p-4 shadow-xl">
      <div class="flex items-start gap-3">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-forest-600 to-forest-700">
          <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-semibold text-forest-800 dark:text-forest-200">Instalar CTAccess</h3>
          <p class="text-xs text-sage-600 dark:text-sage-400 mt-1">
            Instala la aplicación en tu dispositivo para acceso rápido y offline.
          </p>
        </div>
        <button
          @click="dismissInstallPrompt"
          class="flex-shrink-0 rounded-lg p-1.5 text-sage-400 dark:text-sage-500 hover:bg-sage-100 dark:hover:bg-sage-700 hover:text-sage-600 dark:hover:text-sage-400"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="mt-4 flex gap-2">
        <button
          @click="installApp"
          class="flex-1 rounded-lg bg-gradient-to-r from-forest-600 to-forest-700 px-3 py-2 text-xs font-medium text-white transition-all hover:from-forest-700 hover:to-forest-800 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:ring-offset-2"
        >
          Instalar
        </button>
        <button
          @click="dismissInstallPrompt"
          class="flex-1 rounded-lg border border-sage-300 dark:border-sage-600 px-3 py-2 text-xs font-medium text-sage-700 dark:text-sage-300 transition-colors hover:bg-sage-50 dark:hover:bg-sage-700 focus:outline-none focus:ring-2 focus:ring-sage-500 focus:ring-offset-2"
        >
          Ahora no
        </button>
      </div>
    </div>
  </div>
</template>