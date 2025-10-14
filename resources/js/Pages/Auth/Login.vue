<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Icon from '@/Components/Icon.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTheme } from '@/composables/useTheme';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    correo: '',
    contraseña: '',
    remember: false,
});

const { isDark, toggleTheme } = useTheme();

const submit = () => {
    form.post(route('personas.login.store'), {
        onFinish: () => form.reset('contraseña'),
        onError: (errors) => {
            // Si hay error de token CSRF, recargar la página
            if (errors.message && (errors.message.includes('CSRF') || errors.message.includes('expired'))) {
                window.location.reload();
            }
        },
        preserveScroll: true,
        preserveState: false,
    });
};
</script>

<template>
    <div class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 overflow-hidden bg-gradient-to-br from-sena-green-50 via-white to-cyan-50 dark:from-gray-900 dark:via-sena-blue-950 dark:to-gray-900 transition-colors duration-500">
        <Head title="Iniciar Sesión - CTAccess" />
        
        <!-- Partículas animadas de fondo -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div
            v-for="n in 50"
            :key="n"
            class="absolute rounded-full animate-float bg-gradient-to-br from-sena-green-400 to-cyan-400 dark:from-cyan-500 dark:to-blue-500 opacity-20 dark:opacity-10"
            :style="{
              left: Math.random() * 100 + '%',
              top: Math.random() * 100 + '%',
              width: (Math.random() * 4 + 2) + 'px',
              height: (Math.random() * 4 + 2) + 'px',
              animationDuration: (Math.random() * 20 + 15) + 's',
              animationDelay: (Math.random() * 5) + 's',
            }"
          ></div>
        </div>

        <!-- Efectos de luz de fondo -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-sena-green-400 dark:bg-cyan-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-20 dark:opacity-10 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-cyan-400 dark:bg-blue-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-20 dark:opacity-10 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/3 w-96 h-96 bg-yellow-300 dark:bg-cyan-600 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-20 dark:opacity-10 animate-blob animation-delay-4000"></div>

        <!-- Botón de tema -->
        <button
          @click="toggleTheme"
          class="absolute top-6 right-6 z-20 group rounded-xl p-3 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-sena-green-200 dark:border-cyan-700 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
          :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
        >
          <Icon :name="isDark ? 'sun' : 'moon'" :size="24" class="text-sena-green-600 dark:text-cyan-400 group-hover:rotate-180 transition-transform duration-500" />
        </button>
        
        <div class="relative z-10 max-w-md w-full space-y-8">

            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="/icons/icon-192x192.png" alt="CTAccess Logo" class="mx-auto h-32 w-32 object-contain drop-shadow-2xl transform transition-all duration-500 hover:scale-110" />
            </div>

            <!-- Formulario -->
            <div class="relative bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-sena-green-100 dark:border-cyan-900 transition-all duration-500 hover:shadow-sena-green-500/20 dark:hover:shadow-cyan-500/20">
                
                <div v-if="status" class="mb-6 p-4 rounded-lg text-sm bg-sena-green-50 dark:bg-cyan-900/20 border border-sena-green-200 dark:border-cyan-700 text-sena-green-700 dark:text-cyan-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="correo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Icon name="mail" :size="20" class="text-gray-400 dark:text-gray-500" />
                            </div>
                            <input
                                id="correo"
                                type="email"
                                v-model="form.correo"
                                required
                                autofocus
                                autocomplete="username"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-sena-green-500 dark:focus:ring-cyan-500 shadow-sm"
                                placeholder="tu@email.com"
                            />
                        </div>
                        <InputError class="mt-2 text-red-500 dark:text-red-400" :message="form.errors.correo" />
                    </div>

                    <div>
                        <label for="contraseña" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Icon name="lock" :size="20" class="text-gray-400 dark:text-gray-500" />
                            </div>
                            <input
                                id="contraseña"
                                type="password"
                                v-model="form.contraseña"
                                required
                                autocomplete="current-password"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-sena-green-500 dark:focus:ring-cyan-500 shadow-sm"
                                placeholder="••••••••"
                            />
                        </div>
                        <InputError class="mt-2 text-red-500 dark:text-red-400" :message="form.errors.contraseña" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 transition-all duration-200 text-sena-green-600 dark:text-cyan-500 focus:ring-sena-green-500 dark:focus:ring-cyan-500"
                            />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors">Recordarme</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium text-sena-green-600 dark:text-cyan-400 hover:text-sena-green-700 dark:hover:text-cyan-300 transition-colors duration-200"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group relative w-full flex items-center justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 shadow-lg hover:shadow-xl focus:ring-sena-green-500 dark:focus:ring-cyan-500 uppercase tracking-wider bg-gradient-to-r from-sena-green-600 to-sena-green-700 dark:from-blue-600 dark:to-blue-700 hover:from-sena-green-700 hover:to-sena-green-800 dark:hover:from-blue-700 dark:hover:to-blue-800 hover:scale-105 active:scale-95"
                        >
                            <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span v-if="!form.processing">Iniciar Sesión</span>
                            <span v-else>Ingresando...</span>
                        </button>
                    </div>
                </form>

                <!-- Enlaces adicionales -->
                <div class="mt-6 text-center">
                    <Link
                        :href="route('home')"
                        class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-sena-green-600 dark:hover:text-cyan-400 transition-colors duration-200 inline-flex items-center gap-2"
                    >
                        <Icon name="arrow-left" :size="16" />
                        Volver al inicio
                    </Link>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    Sistema de Control de Acceso
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                    CTAccess v2.0 • SENA
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(-10px) translateX(-10px);
  }
  75% {
    transform: translateY(-30px) translateX(5px);
  }
}

@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  25% {
    transform: translate(20px, -50px) scale(1.1);
  }
  50% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  75% {
    transform: translate(50px, 50px) scale(1.05);
  }
}

@keyframes gradient-x {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.animate-float {
  animation: float linear infinite;
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradient-x 3s ease infinite;
}
</style>
