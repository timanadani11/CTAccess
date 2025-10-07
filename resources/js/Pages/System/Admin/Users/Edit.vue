<script setup>
import SystemLayout from '@/Layouts/System/SystemLayout.vue'
import { Head, useForm, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const props = defineProps({
  user: { type: Object, required: true },
  roles: { type: Array, default: () => [] },
})

const form = useForm({
  UserName: props.user.UserName,
  password: '',
  nombre: props.user.nombre,
  activo: props.user.activo,
  roles: props.user.roles || [],
  rol_principal_id: props.user.rol_principal_id,
})

const submit = () => form.put(route('system.admin.users.update', props.user.id))
const canSave = computed(() => form.UserName && form.nombre)
</script>

<template>
  <SystemLayout>
    <Head title="Editar Usuario" />

    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-forest-800 dark:text-forest-200">Editar Usuario #{{ props.user.id }}</h2>
    </template>

    <form class="space-y-6" @submit.prevent="submit">
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-sage-700 dark:text-sage-300">Usuario</label>
          <input v-model="form.UserName" class="mt-1 w-full rounded-md border border-forest-200 dark:border-sage-700 px-3 py-2 text-sm bg-white dark:bg-sage-700 text-sage-700 dark:text-sage-300 placeholder-sage-500 dark:placeholder-sage-400 focus:ring-2 focus:ring-forest-500 dark:focus:ring-forest-400 focus:border-transparent" />
          <div v-if="form.errors.UserName" class="mt-1 text-xs text-red-600">{{ form.errors.UserName }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-sage-700 dark:text-sage-300">Contraseña (opcional)</label>
          <input type="password" v-model="form.password" class="mt-1 w-full rounded-md border border-forest-200 dark:border-sage-700 px-3 py-2 text-sm bg-white dark:bg-sage-700 text-sage-700 dark:text-sage-300 placeholder-sage-500 dark:placeholder-sage-400 focus:ring-2 focus:ring-forest-500 dark:focus:ring-forest-400 focus:border-transparent" />
          <div v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-sage-700 dark:text-sage-300">Nombre</label>
          <input v-model="form.nombre" class="mt-1 w-full rounded-md border border-forest-200 dark:border-sage-700 px-3 py-2 text-sm bg-white dark:bg-sage-700 text-sage-700 dark:text-sage-300 placeholder-sage-500 dark:placeholder-sage-400 focus:ring-2 focus:ring-forest-500 dark:focus:ring-forest-400 focus:border-transparent" />
          <div v-if="form.errors.nombre" class="mt-1 text-xs text-red-600">{{ form.errors.nombre }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-sage-700 dark:text-sage-300">Activo</label>
          <select v-model="form.activo" class="mt-1 w-full rounded-md border border-forest-200 dark:border-sage-700 px-3 py-2 text-sm bg-white dark:bg-sage-700 text-sage-700 dark:text-sage-300 focus:ring-2 focus:ring-forest-500 dark:focus:ring-forest-400 focus:border-transparent">
            <option :value="true">Sí</option>
            <option :value="false">No</option>
          </select>
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-sage-700 dark:text-sage-300">Roles</label>
          <div class="mt-2 grid grid-cols-1 gap-2">
            <label v-for="r in props.roles" :key="r.id" class="flex items-center gap-2 text-sm text-sage-700 dark:text-sage-300">
              <input type="checkbox" :value="r.id" v-model="form.roles" class="rounded border-forest-200 dark:border-sage-700 text-forest-600 focus:ring-forest-500 dark:focus:ring-forest-400 bg-white dark:bg-sage-700" />
              <span>{{ r.nombre }}</span>
            </label>
          </div>
          <div v-if="form.errors.roles" class="mt-1 text-xs text-red-600">{{ form.errors.roles }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium text-sage-700 dark:text-sage-300">Rol principal</label>
          <select v-model="form.rol_principal_id" class="mt-1 w-full rounded-md border border-forest-200 dark:border-sage-700 px-3 py-2 text-sm bg-white dark:bg-sage-700 text-sage-700 dark:text-sage-300 focus:ring-2 focus:ring-forest-500 dark:focus:ring-forest-400 focus:border-transparent">
            <option :value="null">—</option>
            <option v-for="r in props.roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
          </select>
          <div class="mt-1 text-xs text-sage-500 dark:text-sage-400">Se incluirá automáticamente en la lista de roles.</div>
        </div>
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" class="rounded-md border border-forest-200 dark:border-sage-700 px-3 py-1.5 text-sm text-sage-700 dark:text-sage-300 hover:bg-forest-50 dark:hover:bg-sage-700" @click="router.visit(route('system.admin.users.index'))">Cancelar</button>
        <button :disabled="!canSave || form.processing" class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50">Guardar</button>
      </div>
    </form>
  </SystemLayout>
</template>
