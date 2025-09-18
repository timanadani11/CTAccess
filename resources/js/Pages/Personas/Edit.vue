<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Editar Persona</h1>
      <Link 
        :href="route('personas.show', persona.id)"
        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
      >
        Cancelar
      </Link>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Información personal -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Información Personal</h2>
        
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">
              Documento
            </label>
            <input
              id="documento"
              v-model="form.documento"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent"
              :class="{ 'border-red-500': form.errors.documento }"
            >
            <div v-if="form.errors.documento" class="text-red-600 text-sm mt-1">
              {{ form.errors.documento }}
            </div>
          </div>

          <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
              Nombre *
            </label>
            <input
              id="nombre"
              v-model="form.nombre"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent"
              :class="{ 'border-red-500': form.errors.nombre }"
            >
            <div v-if="form.errors.nombre" class="text-red-600 text-sm mt-1">
              {{ form.errors.nombre }}
            </div>
          </div>

          <div>
            <label for="tipoPersona" class="block text-sm font-medium text-gray-700 mb-1">
              Tipo de Persona *
            </label>
            <select
              id="tipoPersona"
              v-model="form.tipoPersona"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent"
              :class="{ 'border-red-500': form.errors.tipoPersona }"
            >
              <option value="">Seleccionar tipo</option>
              <option value="Estudiante">Estudiante</option>
              <option value="Docente">Docente</option>
              <option value="Administrativo">Administrativo</option>
              <option value="Visitante">Visitante</option>
              <option value="Contratista">Contratista</option>
            </select>
            <div v-if="form.errors.tipoPersona" class="text-red-600 text-sm mt-1">
              {{ form.errors.tipoPersona }}
            </div>
          </div>

          <div>
            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
              Correo Electrónico
            </label>
            <input
              id="correo"
              v-model="form.correo"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#39A900] focus:border-transparent"
              :class="{ 'border-red-500': form.errors.correo }"
            >
            <div v-if="form.errors.correo" class="text-red-600 text-sm mt-1">
              {{ form.errors.correo }}
            </div>
          </div>
        </div>
      </div>

      <!-- Portátiles -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Portátiles</h2>
          <button
            type="button"
            @click="addPortatil"
            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
          >
            Agregar Portátil
          </button>
        </div>

        <div v-if="form.portatiles.length === 0" class="text-gray-500 text-center py-4">
          No hay portátiles registrados
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="(portatil, index) in form.portatiles"
            :key="index"
            class="border border-gray-200 rounded-lg p-4"
          >
            <div class="flex justify-between items-start mb-3">
              <h3 class="font-medium text-gray-800">Portátil {{ index + 1 }}</h3>
              <button
                type="button"
                @click="removePortatil(index)"
                class="text-red-600 hover:text-red-800 text-sm"
              >
                Eliminar
              </button>
            </div>
            
            <div class="grid md:grid-cols-3 gap-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial *</label>
                <input
                  v-model="portatil.serial"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#39A900]"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                <input
                  v-model="portatil.marca"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#39A900]"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                <input
                  v-model="portatil.modelo"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#39A900]"
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehículos -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Vehículos</h2>
          <button
            type="button"
            @click="addVehiculo"
            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
          >
            Agregar Vehículo
          </button>
        </div>

        <div v-if="form.vehiculos.length === 0" class="text-gray-500 text-center py-4">
          No hay vehículos registrados
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="(vehiculo, index) in form.vehiculos"
            :key="index"
            class="border border-gray-200 rounded-lg p-4"
          >
            <div class="flex justify-between items-start mb-3">
              <h3 class="font-medium text-gray-800">Vehículo {{ index + 1 }}</h3>
              <button
                type="button"
                @click="removeVehiculo(index)"
                class="text-red-600 hover:text-red-800 text-sm"
              >
                Eliminar
              </button>
            </div>
            
            <div class="grid md:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select
                  v-model="vehiculo.tipo"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#39A900]"
                >
                  <option value="">Seleccionar tipo</option>
                  <option value="Automóvil">Automóvil</option>
                  <option value="Motocicleta">Motocicleta</option>
                  <option value="Bicicleta">Bicicleta</option>
                  <option value="Camioneta">Camioneta</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                <input
                  v-model="vehiculo.placa"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#39A900]"
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="flex justify-end gap-3">
        <Link
          :href="route('personas.show', persona.id)"
          class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
        >
          Cancelar
        </Link>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-6 py-2 bg-[#39A900] text-white rounded-lg hover:bg-[#007832] disabled:opacity-50"
        >
          <span v-if="form.processing">Actualizando...</span>
          <span v-else>Actualizar Persona</span>
        </button>
      </div>

      <!-- Errores generales -->
      <div v-if="form.errors.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="text-red-800">{{ form.errors.error }}</div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  persona: Object,
})

// Inicializar formulario con datos existentes
const form = useForm({
  documento: props.persona.documento || '',
  nombre: props.persona.nombre || '',
  tipoPersona: props.persona.tipoPersona || '',
  correo: props.persona.correo || '',
  portatiles: props.persona.portatiles || [],
  vehiculos: props.persona.vehiculos || [],
})

// Funciones para agregar/remover portátiles
const addPortatil = () => {
  form.portatiles.push({
    serial: '',
    marca: '',
    modelo: ''
  })
}

const removePortatil = (index) => {
  form.portatiles.splice(index, 1)
}

// Funciones para agregar/remover vehículos
const addVehiculo = () => {
  form.vehiculos.push({
    tipo: '',
    placa: ''
  })
}

const removeVehiculo = (index) => {
  form.vehiculos.splice(index, 1)
}

// Enviar formulario
const submit = () => {
  form.put(route('personas.update', props.persona.id))
}
</script>
