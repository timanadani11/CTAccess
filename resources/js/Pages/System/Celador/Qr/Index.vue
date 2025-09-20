<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Verificación QR" />

    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Verificación QR</h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
        <div class="rounded-lg border bg-white p-6">
          <p class="text-gray-600">Pantalla preparada para integrar escáner de QR (por ejemplo, con la cámara del dispositivo). Aquí podrás validar personas y elementos rápidamente.</p>
          <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="aspect-video rounded-lg bg-gray-100/60 ring-1 ring-gray-200 flex items-center justify-center text-gray-400">
              Video / Cámara
            </div>
            <div class="space-y-3">
              <label class="block text-sm font-medium text-gray-700">Ingresar código manual</label>
              <input type="text" placeholder="Pega o escribe el código" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
              <button class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">Verificar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
