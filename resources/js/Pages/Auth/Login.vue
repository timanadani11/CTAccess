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
    form.post(route('login'), {
        onFinish: () => form.reset('contraseña'),
        onError: (errors) => {
            // Si hay error de token CSRF, recargar la página
            if (errors.message && errors.message.includes('CSRF') || errors.message && errors.message.includes('expired')) {
                window.location.reload();
            }
        },
        preserveScroll: true,
        preserveState: false,
    });
};
</script>

<template>
    <div class="min-h-screen bg-theme-primary text-theme-primary flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <Head title="Iniciar Sesión - CTAccess" />
        
        <div class="max-w-md w-full space-y-8">

            <!-- Logo y título -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 rounded-xl flex items-center justify-center mb-4 shadow-theme-lg" 
                     style="background: linear-gradient(135deg, #39A900, #50E5F9);">
                    <Icon name="lock" :size="32" class="text-white" />
                </div>
                <h2 class="text-3xl font-bold text-theme-primary mb-2">CTAccess</h2>
                <p class="text-theme-secondary text-sm">Sistema de Control de Accesos</p>
            </div>

            <!-- Formulario -->
            <div class="bg-theme-card backdrop-blur-lg rounded-2xl shadow-theme-lg p-8 border border-theme-primary">
                <div v-if="status" class="mb-6 p-4 rounded-lg text-sm" 
                     style="background-color: rgba(57, 169, 0, 0.1); border: 1px solid rgba(57, 169, 0, 0.3); color: #39A900;">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="correo" class="block text-sm font-medium text-theme-primary mb-2">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Icon name="mail" :size="20" class="text-theme-muted" />
                            </div>
                            <input
                                id="correo"
                                type="email"
                                v-model="form.correo"
                                required
                                autofocus
                                autocomplete="username"
                                class="block w-full pl-10 pr-3 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-green-500"
                                placeholder="tu@email.com"
                            />
                        </div>
                        <InputError class="mt-2 text-red-500" :message="form.errors.correo" />
                    </div>

                    <div>
                        <label for="contraseña" class="block text-sm font-medium text-theme-primary mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Icon name="lock" :size="20" class="text-theme-muted" />
                            </div>
                            <input
                                id="contraseña"
                                type="password"
                                v-model="form.contraseña"
                                required
                                autocomplete="current-password"
                                class="block w-full pl-10 pr-3 py-3 border border-theme-primary rounded-lg bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 focus:ring-green-500"
                                placeholder="••••••••"
                            />
                        </div>
                        <InputError class="mt-2 text-red-500" :message="form.errors.contraseña" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 rounded border-theme-primary bg-theme-secondary focus:ring-2 transition-all duration-200 text-green-500 focus:ring-green-500"
                            />
                            <span class="ml-2 text-sm text-theme-secondary">Recordarme</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-theme-secondary hover:text-theme-primary transition-colors duration-200"
                            style="color: #50E5F9;"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-theme-md hover:shadow-theme-lg focus:ring-green-500"
                            style="background: linear-gradient(135deg, #39A900, #2d7a00);"
                        >
                            <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <Icon name="loader" :size="20" class="text-white animate-spin" />
                            </span>
                            {{ form.processing ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
                        </button>
                    </div>
                </form>

                <!-- Enlaces adicionales -->
                <div class="mt-6 text-center">
                    <Link
                        :href="route('home')"
                        class="text-sm text-theme-secondary hover:text-theme-primary transition-colors duration-200"
                        style="color: #50E5F9;"
                    >
                        ← Volver al inicio
                    </Link>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-theme-muted">
                    © 2024 CTAccess. Sistema de Control de Accesos
                </p>
            </div>
        </div>
    </div>
</template>
