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

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Registrar Persona</h1>
      <Link :href="route('personas.create')" class="hidden"></Link>
    </div>

    <!-- Steps indicator -->
    <div class="mb-4 flex items-center justify-center gap-2 text-sm">
      <div :class="['px-3 py-1 rounded-full', step === 1 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600']">1. Persona</div>
      <div :class="['px-3 py-1 rounded-full', step === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600']">2. Portátiles</div>
      <div :class="['px-3 py-1 rounded-full', step === 3 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600']">3. Vehículos</div>
      <div :class="['px-3 py-1 rounded-full', step === 4 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600']">4. Resumen</div>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Step 1: Persona -->
      <div v-show="step === 1" class="space-y-6">
        <div>
          <InputLabel for="documento" value="Documento de identidad" />
          <TextInput
            id="documento"
            type="text"
            class="mt-1 block w-full"
            v-model="form.documento"
            autocomplete="off"
          />
          <InputError class="mt-2" :message="errors.documento" />
        </div>

        <div>
          <InputLabel for="nombre" value="Nombre" />
          <TextInput
            id="nombre"
            type="text"
            class="mt-1 block w-full"
            v-model="form.nombre"
            required
            autocomplete="name"
          />
          <InputError class="mt-2" :message="errors.nombre" />
        </div>

        <div>
          <InputLabel for="correo" value="Correo (opcional)" />
          <TextInput
            id="correo"
            type="email"
            class="mt-1 block w-full"
            v-model="form.correo"
            autocomplete="email"
            placeholder="correo@dominio.com"
          />
          <InputError class="mt-2" :message="errors.correo" />
        </div>

        <div>
          <InputLabel for="tipoPersona" value="Tipo de Persona" />
          <select
            id="tipoPersona"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            v-model="form.tipoPersona"
            required
          >
            <option value="Aprendiz">Aprendiz</option>
            <option value="Instructor">Instructor</option>
            <option value="Visitante">Visitante</option>
          </select>
          <InputError class="mt-2" :message="errors.tipoPersona" />
        </div>

        <!-- Persona QR Preview -->
        <div class="space-y-2">
          <span class="text-sm text-gray-600">QR de la persona (contenido: documento)</span>
          <div class="rounded-md border p-3 flex items-center gap-4">
            <img v-if="personaQrUrl" :src="personaQrUrl" alt="QR Persona" class="h-24 w-24" />
            <div class="text-sm text-gray-600" v-else>Ingrese el documento para generar el QR</div>
            <div class="text-xs text-gray-500">Se guardará el texto del QR en la columna <code>qrCode</code>.</div>
          </div>
          <InputError class="mt-2" :message="errors.qrCode" />
        </div>
      </div>

      <!-- Step 2: Portátiles -->
      <div v-show="step === 2" class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium">Portátiles</h2>
          <button type="button" @click="addPortatil" class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700">Agregar</button>
        </div>
        <div v-if="!form.portatiles.length" class="text-sm text-gray-500">No hay portátiles agregados.</div>
        <div v-for="(p, idx) in form.portatiles" :key="idx" class="rounded-md border p-4 space-y-3">
          <div class="grid gap-4 md:grid-cols-3">
            <div>
              <InputLabel :for="`serial_${idx}`" value="Serial" />
              <TextInput :id="`serial_${idx}`" type="text" class="mt-1 block w-full" v-model="p.serial" placeholder="Serial del portátil" />
            </div>
            <div>
              <InputLabel :for="`marca_${idx}`" value="Marca" />
              <TextInput :id="`marca_${idx}`" type="text" class="mt-1 block w-full" v-model="p.marca" />
            </div>
            <div>
              <InputLabel :for="`modelo_${idx}`" value="Modelo" />
              <TextInput :id="`modelo_${idx}`" type="text" class="mt-1 block w-full" v-model="p.modelo" />
            </div>
          </div>
          <!-- QR preview for each portátil based on its serial (qrCode field) -->
          <div class="flex items-center gap-4">
            <img v-if="p.serial" :src="`https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(p.serial)}`" alt="QR Portátil" class="h-20 w-20" />
            <div class="text-xs text-gray-500">El QR del portátil guardará el texto del campo QR Code (serial).</div>
          </div>
          <div class="flex justify-end">
            <button type="button" @click="removePortatil(idx)" class="text-sm text-red-600 hover:underline">Quitar</button>
          </div>
        </div>
        <InputError class="mt-2" :message="errors['portatiles.*'] || errors['portatiles']" />
      </div>

      <!-- Step 3: Vehículos -->
      <div v-show="step === 3" class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium">Vehículos</h2>
          <button type="button" @click="addVehiculo" class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700">Agregar</button>
        </div>
        <div v-if="!form.vehiculos.length" class="text-sm text-gray-500">No hay vehículos agregados.</div>
        <div v-for="(v, idx) in form.vehiculos" :key="idx" class="rounded-md border p-4 space-y-3">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <InputLabel :for="`tipo_${idx}`" value="Tipo" />
              <TextInput :id="`tipo_${idx}`" type="text" class="mt-1 block w-full" v-model="v.tipo" placeholder="Carro / Moto / etc" />
            </div>
            <div>
              <InputLabel :for="`placa_${idx}`" value="Placa" />
              <TextInput :id="`placa_${idx}`" type="text" class="mt-1 block w-full" v-model="v.placa" />
            </div>
          </div>
          <div class="flex justify-end">
            <button type="button" @click="removeVehiculo(idx)" class="text-sm text-red-600 hover:underline">Quitar</button>
          </div>
        </div>
        <InputError class="mt-2" :message="errors['vehiculos.*'] || errors['vehiculos']" />
      </div>

      <!-- Step 4: Resumen -->
      <div v-show="step === 4" class="space-y-4">
        <div class="rounded-md border p-4">
          <h2 class="mb-3 text-lg font-medium">Resumen</h2>
          <ul class="text-sm space-y-1">
            <li><strong>Documento:</strong> {{ form.documento || '—' }}</li>
            <li><strong>Nombre:</strong> {{ form.nombre }}</li>
            <li><strong>Tipo de Persona:</strong> {{ form.tipoPersona }}</li>
            <li><strong>Correo:</strong> {{ form.correo || '—' }}</li>
            <li><strong>QR Persona:</strong> {{ form.documento ? 'Generado' : '—' }}</li>
          </ul>
        </div>

        <div class="rounded-md border p-4">
          <h3 class="mb-2 font-medium">Portátiles ({{ form.portatiles.length }})</h3>
          <ul class="list-disc pl-6 text-sm">
            <li v-for="(p, idx) in form.portatiles" :key="`sum-p-${idx}`">
              {{ p.qrCode || 'QR ?' }} · {{ p.marca || 'Marca ?' }} · {{ p.modelo || 'Modelo ?' }}
            </li>
            <li v-if="!form.portatiles.length" class="list-none text-gray-500">Ninguno</li>
          </ul>
        </div>

        <div class="rounded-md border p-4">
          <h3 class="mb-2 font-medium">Vehículos ({{ form.vehiculos.length }})</h3>
          <ul class="list-disc pl-6 text-sm">
            <li v-for="(v, idx) in form.vehiculos" :key="`sum-v-${idx}`">
              {{ v.tipo || 'Tipo ?' }} · {{ v.placa || 'Placa ?' }}
            </li>
            <li v-if="!form.vehiculos.length" class="list-none text-gray-500">Ninguno</li>
          </ul>
        </div>
      </div>

      <!-- Alerts -->
      <div v-if="errors.general" class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ errors.general }}
      </div>

      <div v-if="successMessage" class="rounded border border-green-200 bg-green-50 p-3 text-sm text-green-700">
        {{ successMessage }}
      </div>

      <!-- Navigation buttons -->
      <div class="flex items-center justify-between">
        <button type="button" @click="prevStep" :disabled="step === 1" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50">Atrás</button>
        <div class="flex items-center gap-2">
          <button v-if="step < 4" type="button" @click="nextStep" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">Siguiente</button>
          <PrimaryButton v-else :class="{ 'opacity-50': loading }" :disabled="loading">
            {{ loading ? 'Guardando...' : 'Guardar' }}
          </PrimaryButton>
        </div>
      </div>
    </form>
  </GuestLayout>
</template>
