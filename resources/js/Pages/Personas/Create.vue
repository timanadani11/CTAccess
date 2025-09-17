<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref, computed } from 'vue';

const loading = ref(false);
const successMessage = ref('');
const errors = reactive({});

const form = reactive({
  documento: '',
  nombre: '',
  tipoPersona: 'Aprendiz',
  correo: '',
  password: '',
  password_confirmation: '',
  // Opcionales para futuro: relaciones
  portatiles: [],
  vehiculos: [],
});

// Persona QR preview (based on documento)
const personaQrUrl = computed(() =>
  form.documento
    ? `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(form.documento)}`
    : ''
);

// Wizard state
const step = ref(1); // 1: Persona, 2: Portátiles, 3: Vehículos, 4: Resumen
const maxStep = 4;

function nextStep() {
  if (step.value < maxStep) step.value += 1;
}

function prevStep() {
  if (step.value > 1) step.value -= 1;
}

// Helpers to manage dynamic lists
function addPortatil() {
  form.portatiles.push({ serial: '', marca: '', modelo: '' });
}

function removePortatil(index) {
  form.portatiles.splice(index, 1);
}

function addVehiculo() {
  form.vehiculos.push({ tipo: '', placa: '' });
}

function removeVehiculo(index) {
  form.vehiculos.splice(index, 1);
}

function clearErrors() {
  Object.keys(errors).forEach((k) => delete errors[k]);
}

async function submit() {
  clearErrors();
  successMessage.value = '';
  loading.value = true;
  try {
    // Enviar como multipart/form-data para soportar archivos
    const fd = new FormData();
    if (form.documento) fd.append('documento', form.documento);
    fd.append('nombre', form.nombre);
    fd.append('tipoPersona', form.tipoPersona);
    if (form.correo) fd.append('correo', form.correo);
    if (form.password) fd.append('password', form.password);
    if (form.password_confirmation) fd.append('password_confirmation', form.password_confirmation);
    if (form.documento) fd.append('qrCode', form.documento); // contenido del QR de la persona

    // Arrays anidados
    form.portatiles.forEach((p, i) => {
      if (p.serial || p.marca || p.modelo) {
        if (p.serial) fd.append(`portatiles[${i}][serial]`, p.serial);
        if (p.marca) fd.append(`portatiles[${i}][marca]`, p.marca);
        if (p.modelo) fd.append(`portatiles[${i}][modelo]`, p.modelo);
      }
    });
    form.vehiculos.forEach((v, i) => {
      if (v.tipo || v.placa) {
        if (v.tipo) fd.append(`vehiculos[${i}][tipo]`, v.tipo);
        if (v.placa) fd.append(`vehiculos[${i}][placa]`, v.placa);
      }
    });

    const { data } = await window.axios.post('/api/v1/personas', fd);
    successMessage.value = data?.message || 'Persona creada correctamente';
    // Redirigir al listado de personas (Inertia page existente)
    setTimeout(() => {
      router.visit('/personas');
    }, 600);
  } catch (e) {
    if (e?.response?.status === 422) {
      const resp = e.response.data;
      // Jetstream style: { message, errors: { campo: [msg] } } o nuestro json con error
      if (resp?.errors) {
        Object.entries(resp.errors).forEach(([k, v]) => {
          errors[k] = Array.isArray(v) ? v.join('\n') : String(v);
        });
      } else if (resp?.message) {
        errors.general = resp.message;
      }
    } else {
      errors.general = 'Ocurrió un error inesperado.';
    }
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <GuestLayout>
    <Head title="Registrar Persona" />

    <!-- Header with gradient background -->
    <div class="mb-8 text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg">
        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
      </div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Registrar Persona</h1>
      <p class="mt-2 text-gray-600">Complete el formulario paso a paso para registrar una nueva persona</p>
    </div>

    <!-- Modern Steps indicator -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div v-for="(stepInfo, index) in [
          { num: 1, title: 'Datos Personales', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
          { num: 2, title: 'Portátiles', icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
          { num: 3, title: 'Vehículos', icon: 'M8 17l4 4 4-4m-4-5v9' },
          { num: 4, title: 'Resumen', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }
        ]" :key="index" class="flex flex-col items-center flex-1">
          <div :class="[
            'relative flex h-12 w-12 items-center justify-center rounded-full border-2 transition-all duration-300',
            step === stepInfo.num 
              ? 'border-indigo-600 bg-indigo-600 text-white shadow-lg scale-110' 
              : step > stepInfo.num 
                ? 'border-green-500 bg-green-500 text-white' 
                : 'border-gray-300 bg-white text-gray-400'
          ]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stepInfo.icon"></path>
            </svg>
            <div v-if="index < 3" :class="[
              'absolute left-12 top-1/2 h-0.5 w-full -translate-y-1/2 transition-colors duration-300',
              step > stepInfo.num ? 'bg-green-500' : 'bg-gray-300'
            ]"></div>
          </div>
          <div class="mt-2 text-center">
            <div :class="['text-xs font-medium transition-colors duration-300', step === stepInfo.num ? 'text-indigo-600' : 'text-gray-500']">
              {{ stepInfo.title }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main form container with modern styling -->
    <div class="mx-auto max-w-2xl">
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Step 1: Persona -->
        <div v-show="step === 1" class="animate-fade-in">
          <div class="rounded-2xl bg-white p-8 shadow-xl border border-gray-100">
            <div class="mb-6">
              <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Datos Personales
              </h2>
              <p class="text-sm text-gray-600 mt-1">Ingrese la información básica de la persona</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
              <div class="md:col-span-1">
                <InputLabel for="documento" value="Documento de identidad *" />
                <TextInput
                  id="documento"
                  type="text"
                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                  v-model="form.documento"
                  autocomplete="off"
                  placeholder="Ej: 12345678"
                />
                <InputError class="mt-2" :message="errors.documento" />
              </div>

              <div class="md:col-span-1">
                <InputLabel for="nombre" value="Nombre completo *" />
                <TextInput
                  id="nombre"
                  type="text"
                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                  v-model="form.nombre"
                  required
                  autocomplete="name"
                  placeholder="Ej: Juan Pérez"
                />
                <InputError class="mt-2" :message="errors.nombre" />
              </div>

              <div class="md:col-span-1">
                <InputLabel for="correo" value="Correo electrónico" />
                <TextInput
                  id="correo"
                  type="email"
                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                  v-model="form.correo"
                  autocomplete="email"
                  placeholder="correo@ejemplo.com"
                />
                <InputError class="mt-2" :message="errors.correo" />
              </div>

              <div class="md:col-span-1">
                <InputLabel for="tipoPersona" value="Tipo de persona *" />
                <select
                  id="tipoPersona"
                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                  v-model="form.tipoPersona"
                  required
                >
                  <option value="Aprendiz">👨‍🎓 Aprendiz</option>
                  <option value="Instructor">👨‍🏫 Instructor</option>
                  <option value="Visitante">👤 Visitante</option>
                </select>
                <InputError class="mt-2" :message="errors.tipoPersona" />
              </div>

              <div class="md:col-span-1">
                <InputLabel for="password" value="Contraseña *" />
                <TextInput
                  id="password"
                  type="password"
                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                  v-model="form.password"
                  autocomplete="new-password"
                  placeholder="Mínimo 8 caracteres"
                />
                <InputError class="mt-2" :message="errors.password" />
              </div>

              <div class="md:col-span-1">
                <InputLabel for="password_confirmation" value="Confirmar contraseña *" />
                <TextInput
                  id="password_confirmation"
                  type="password"
                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                  v-model="form.password_confirmation"
                  autocomplete="new-password"
                  placeholder="Repita la contraseña"
                />
                <InputError class="mt-2" :message="errors.password_confirmation" />
              </div>
            </div>

            <!-- Persona QR Preview -->
            <div class="mt-8 rounded-lg bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border border-indigo-200">
              <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4.01M12 8h4.01"></path>
                </svg>
                Vista previa del código QR
              </h3>
              <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                  <div v-if="personaQrUrl" class="rounded-lg overflow-hidden shadow-md">
                    <img :src="personaQrUrl" alt="QR Persona" class="h-24 w-24" />
                  </div>
                  <div v-else class="h-24 w-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4.01M12 8h4.01"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-600">
                    {{ form.documento ? 'QR generado automáticamente con el documento ingresado' : 'Ingrese el documento para generar el código QR' }}
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    El contenido del QR se guardará en la base de datos para identificación
                  </p>
                </div>
              </div>
              <InputError class="mt-2" :message="errors.qrCode" />
            </div>
          </div>
        </div>

        <!-- Step 2: Portátiles -->
        <div v-show="step === 2" class="animate-fade-in">
          <div class="rounded-2xl bg-white p-8 shadow-xl border border-gray-100">
            <div class="mb-6 flex items-center justify-between">
              <div>
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  Portátiles
                </h2>
                <p class="text-sm text-gray-600 mt-1">Agregue los portátiles asociados (opcional)</p>
              </div>
              <button 
                type="button" 
                @click="addPortatil" 
                class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Agregar Portátil
              </button>
            </div>

            <div v-if="!form.portatiles.length" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <p class="mt-2 text-sm text-gray-500">No hay portátiles agregados</p>
              <p class="text-xs text-gray-400">Haga clic en "Agregar Portátil" para comenzar</p>
            </div>

            <div class="space-y-6">
              <div v-for="(p, idx) in form.portatiles" :key="idx" class="rounded-xl border border-gray-200 bg-gray-50 p-6 transition-all duration-200 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                  <h3 class="text-sm font-medium text-gray-900">Portátil {{ idx + 1 }}</h3>
                  <button 
                    type="button" 
                    @click="removePortatil(idx)" 
                    class="flex items-center gap-1 rounded-md bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100 transition-colors duration-200"
                  >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Quitar
                  </button>
                </div>
                
                <div class="grid gap-4 md:grid-cols-3 mb-4">
                  <div>
                    <InputLabel :for="`serial_${idx}`" value="Serial *" />
                    <TextInput 
                      :id="`serial_${idx}`" 
                      type="text" 
                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" 
                      v-model="p.serial" 
                      placeholder="Ej: ABC123456" 
                    />
                  </div>
                  <div>
                    <InputLabel :for="`marca_${idx}`" value="Marca" />
                    <TextInput 
                      :id="`marca_${idx}`" 
                      type="text" 
                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" 
                      v-model="p.marca" 
                      placeholder="Ej: Dell, HP, Lenovo"
                    />
                  </div>
                  <div>
                    <InputLabel :for="`modelo_${idx}`" value="Modelo" />
                    <TextInput 
                      :id="`modelo_${idx}`" 
                      type="text" 
                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" 
                      v-model="p.modelo" 
                      placeholder="Ej: Inspiron 15"
                    />
                  </div>
                </div>

                <!-- QR preview for each portátil -->
                <div v-if="p.serial" class="rounded-lg bg-white p-4 border border-indigo-200">
                  <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                      <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(p.serial)}`" alt="QR Portátil" class="h-20 w-20 rounded-lg shadow-sm" />
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900">Código QR generado</p>
                      <p class="text-xs text-gray-500 mt-1">Serial: {{ p.serial }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <InputError class="mt-4" :message="errors['portatiles.*'] || errors['portatiles']" />
          </div>
        </div>

        <!-- Step 3: Vehículos -->
        <div v-show="step === 3" class="animate-fade-in">
          <div class="rounded-2xl bg-white p-8 shadow-xl border border-gray-100">
            <div class="mb-6 flex items-center justify-between">
              <div>
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"></path>
                  </svg>
                  Vehículos
                </h2>
                <p class="text-sm text-gray-600 mt-1">Agregue los vehículos asociados (opcional)</p>
              </div>
              <button 
                type="button" 
                @click="addVehiculo" 
                class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Agregar Vehículo
              </button>
            </div>

            <div v-if="!form.vehiculos.length" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"></path>
              </svg>
              <p class="mt-2 text-sm text-gray-500">No hay vehículos agregados</p>
              <p class="text-xs text-gray-400">Haga clic en "Agregar Vehículo" para comenzar</p>
            </div>

            <div class="space-y-6">
              <div v-for="(v, idx) in form.vehiculos" :key="idx" class="rounded-xl border border-gray-200 bg-gray-50 p-6 transition-all duration-200 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                  <h3 class="text-sm font-medium text-gray-900">Vehículo {{ idx + 1 }}</h3>
                  <button 
                    type="button" 
                    @click="removeVehiculo(idx)" 
                    class="flex items-center gap-1 rounded-md bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100 transition-colors duration-200"
                  >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Quitar
                  </button>
                </div>
                
                <div class="grid gap-4 md:grid-cols-2">
                  <div>
                    <InputLabel :for="`tipo_${idx}`" value="Tipo de vehículo *" />
                    <select 
                      :id="`tipo_${idx}`" 
                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" 
                      v-model="v.tipo"
                    >
                      <option value="">Seleccione un tipo</option>
                      <option value="Carro">🚗 Carro</option>
                      <option value="Moto">🏍️ Moto</option>
                      <option value="Bicicleta">🚲 Bicicleta</option>
                      <option value="Camión">🚛 Camión</option>
                      <option value="Otro">🚐 Otro</option>
                    </select>
                  </div>
                  <div>
                    <InputLabel :for="`placa_${idx}`" value="Placa *" />
                    <TextInput 
                      :id="`placa_${idx}`" 
                      type="text" 
                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" 
                      v-model="v.placa" 
                      placeholder="Ej: ABC-123"
                      style="text-transform: uppercase;"
                    />
                  </div>
                </div>
              </div>
            </div>
            
            <InputError class="mt-4" :message="errors['vehiculos.*'] || errors['vehiculos']" />
          </div>
        </div>

        <!-- Step 4: Resumen -->
        <div v-show="step === 4" class="animate-fade-in">
          <div class="rounded-2xl bg-white p-8 shadow-xl border border-gray-100">
            <div class="mb-6">
              <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Resumen de Registro
              </h2>
              <p class="text-sm text-gray-600 mt-1">Revise la información antes de guardar</p>
            </div>

            <!-- Datos personales -->
            <div class="mb-6 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border border-indigo-200">
              <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Datos Personales
              </h3>
              <div class="grid gap-4 md:grid-cols-2">
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-600">Documento:</span>
                  <span class="text-sm text-gray-900">{{ form.documento || '—' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-600">Nombre:</span>
                  <span class="text-sm text-gray-900">{{ form.nombre || '—' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-600">Tipo:</span>
                  <span class="text-sm text-gray-900">{{ form.tipoPersona }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-600">Correo:</span>
                  <span class="text-sm text-gray-900">{{ form.correo || '—' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-600">Contraseña:</span>
                  <span class="text-sm text-gray-900">{{ form.password ? '••••••••' : '—' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium text-gray-600">QR Generado:</span>
                  <span class="text-sm text-gray-900">{{ form.documento ? '✅ Sí' : '❌ No' }}</span>
                </div>
              </div>
            </div>

            <!-- Portátiles -->
            <div class="mb-6 rounded-xl bg-gray-50 p-6 border border-gray-200">
              <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Portátiles ({{ form.portatiles.length }})
              </h3>
              <div v-if="form.portatiles.length" class="space-y-3">
                <div v-for="(p, idx) in form.portatiles" :key="`sum-p-${idx}`" class="flex items-center justify-between bg-white p-3 rounded-lg border">
                  <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                      <span class="text-xs font-medium text-indigo-600">{{ idx + 1 }}</span>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ p.serial || 'Sin serial' }}</p>
                      <p class="text-xs text-gray-500">{{ p.marca || 'Sin marca' }} - {{ p.modelo || 'Sin modelo' }}</p>
                    </div>
                  </div>
                  <div class="text-xs text-green-600 font-medium">✅ QR</div>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-sm text-gray-500">No hay portátiles agregados</p>
              </div>
            </div>

            <!-- Vehículos -->
            <div class="rounded-xl bg-gray-50 p-6 border border-gray-200">
              <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"></path>
                </svg>
                Vehículos ({{ form.vehiculos.length }})
              </h3>
              <div v-if="form.vehiculos.length" class="space-y-3">
                <div v-for="(v, idx) in form.vehiculos" :key="`sum-v-${idx}`" class="flex items-center justify-between bg-white p-3 rounded-lg border">
                  <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                      <span class="text-xs font-medium text-purple-600">{{ idx + 1 }}</span>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ v.placa || 'Sin placa' }}</p>
                      <p class="text-xs text-gray-500">{{ v.tipo || 'Sin tipo' }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-sm text-gray-500">No hay vehículos agregados</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Alerts -->
        <div v-if="errors.general" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 flex items-center gap-3">
          <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          {{ errors.general }}
        </div>

        <div v-if="successMessage" class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 flex items-center gap-3">
          <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          {{ successMessage }}
        </div>

        <!-- Modern Navigation buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
          <button 
            type="button" 
            @click="prevStep" 
            :disabled="step === 1" 
            class="flex items-center gap-2 rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Atrás
          </button>
          
          <div class="flex items-center gap-3">
            <button 
              v-if="step < 4" 
              type="button" 
              @click="nextStep" 
              class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105"
            >
              Siguiente
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <PrimaryButton 
              v-else 
              :class="{ 'opacity-50': loading }" 
              :disabled="loading"
              class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-3 text-sm font-medium text-white shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105"
            >
              <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ loading ? 'Guardando...' : 'Guardar Registro' }}
            </PrimaryButton>
          </div>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>

<style scoped>
/* Animaciones para las transiciones entre pasos */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

/* Mejoras para PWA - Responsive design */
@media (max-width: 640px) {
  .grid.md\\:grid-cols-2 {
    grid-template-columns: 1fr;
  }
  
  .grid.md\\:grid-cols-3 {
    grid-template-columns: 1fr;
  }
  
  .flex.items-center.justify-between {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .flex.items-center.justify-between .flex.items-center.gap-3 {
    justify-content: center;
  }
}

/* Mejoras para el indicador de pasos en móvil */
@media (max-width: 768px) {
  .flex.items-center.justify-between > .flex.flex-col.items-center.flex-1 {
    flex: none;
    width: auto;
  }
  
  .flex.items-center.justify-between > .flex.flex-col.items-center.flex-1 .mt-2 {
    display: none;
  }
  
  .absolute.left-12 {
    display: none;
  }
}

/* Efectos hover mejorados */
.transition-all {
  transition: all 0.2s ease-in-out;
}

.transform.hover\\:scale-105:hover {
  transform: scale(1.05);
}

/* Gradientes personalizados */
.bg-gradient-to-r.from-indigo-500.to-purple-600 {
  background: linear-gradient(to right, #6366f1, #9333ea);
}

.bg-gradient-to-r.from-indigo-600.to-purple-600 {
  background: linear-gradient(to right, #4f46e5, #7c3aed);
}

.bg-gradient-to-r.from-indigo-700.to-purple-700 {
  background: linear-gradient(to right, #4338ca, #6d28d9);
}

.bg-gradient-to-r.from-green-600.to-emerald-600 {
  background: linear-gradient(to right, #16a34a, #059669);
}

.bg-gradient-to-r.from-green-700.to-emerald-700 {
  background: linear-gradient(to right, #15803d, #047857);
}

/* Mejoras para accesibilidad */
.focus\\:ring-indigo-500:focus {
  --tw-ring-color: #6366f1;
}

.focus\\:border-indigo-500:focus {
  --tw-border-opacity: 1;
  border-color: #6366f1;
}

/* Animación del spinner de carga */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Estilos para mejor UX en PWA */
.rounded-2xl {
  border-radius: 1rem;
}

.shadow-xl {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Mejoras para el texto con gradiente */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

.text-transparent {
  color: transparent;
}

/* Estilos para dispositivos táctiles */
@media (hover: none) and (pointer: coarse) {
  .hover\\:scale-105:hover {
    transform: none;
  }
  
  .hover\\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
}

/* Mejoras para el contraste en modo oscuro */
@media (prefers-color-scheme: dark) {
  .bg-white {
    background-color: #1f2937;
    color: #f9fafb;
  }
  
  .text-gray-900 {
    color: #f9fafb;
  }
  
  .text-gray-600 {
    color: #d1d5db;
  }
  
  .border-gray-100 {
    border-color: #374151;
  }
  
  .bg-gray-50 {
    background-color: #111827;
  }
}
</style>
