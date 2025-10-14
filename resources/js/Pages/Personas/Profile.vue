<script setup>
import { Head } from '@inertiajs/vue3';
import PersonaLayout from '@/Layouts/PersonaLayout.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    persona: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Mi Perfil - Portal de Personas" />

    <PersonaLayout :persona="persona">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mi Perfil</h1>
                <p class="text-gray-600 dark:text-gray-400">Información detallada de tu cuenta</p>
            </div>

            <!-- QR Code Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex items-center mb-4">
                    <Icon name="qr-code" :size="24" class="text-sena-green-600 dark:text-cyan-400 mr-3" />
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tu Código QR</h2>
                </div>
                
                <div class="flex flex-col items-center justify-center py-8">
                    <div v-if="persona.qrCode" class="bg-white p-4 rounded-lg shadow-lg">
                        <img :src="persona.qrCode" alt="QR Code" class="w-64 h-64" />
                    </div>
                    <div v-else class="text-center">
                        <Icon name="qr-code" :size="64" class="mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">No tienes un código QR generado</p>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 text-center">
                        Usa este código QR para registrar tu entrada y salida
                    </p>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex items-center mb-6">
                    <Icon name="user" :size="24" class="text-sena-green-600 dark:text-cyan-400 mr-3" />
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Información Personal</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre Completo</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ persona.Nombre }}</p>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ persona.documento || 'No registrado' }}</p>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Persona</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-sena-green-100 dark:bg-cyan-900/30 text-sena-green-700 dark:text-cyan-400">
                                {{ persona.TipoPersona }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Correo Electrónico</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ persona.correo }}</p>
                    </div>
                </div>
            </div>

            <!-- Portátiles -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex items-center mb-4">
                    <Icon name="laptop" :size="24" class="text-purple-600 dark:text-purple-400 mr-3" />
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Portátiles Registrados</h2>
                </div>
                
                <div v-if="persona.portatiles && persona.portatiles.length > 0" class="space-y-3">
                    <div
                        v-for="portatil in persona.portatiles"
                        :key="portatil.portatil_id"
                        class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ portatil.marca }} {{ portatil.modelo }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Serial: {{ portatil.serial }}</p>
                            </div>
                            <Icon name="laptop" :size="32" class="text-purple-400 dark:text-purple-500" />
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <Icon name="laptop" :size="48" class="mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                    <p class="text-gray-500 dark:text-gray-400">No tienes portátiles registrados</p>
                </div>
            </div>

            <!-- Vehículos -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <Icon name="truck" :size="24" class="text-orange-600 dark:text-orange-400 mr-3" />
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Vehículos Registrados</h2>
                </div>
                
                <div v-if="persona.vehiculos && persona.vehiculos.length > 0" class="space-y-3">
                    <div
                        v-for="vehiculo in persona.vehiculos"
                        :key="vehiculo.vehiculo_id"
                        class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ vehiculo.tipo }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Placa: {{ vehiculo.placa }}</p>
                                <p v-if="vehiculo.marca" class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ vehiculo.marca }} {{ vehiculo.modelo }} • {{ vehiculo.color }}
                                </p>
                            </div>
                            <Icon name="truck" :size="32" class="text-orange-400 dark:text-orange-500" />
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <Icon name="truck" :size="48" class="mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                    <p class="text-gray-500 dark:text-gray-400">No tienes vehículos registrados</p>
                </div>
            </div>
        </div>
    </PersonaLayout>
</template>
