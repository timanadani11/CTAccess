<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import { useTheme } from '@/composables/useTheme';

defineProps({
    persona: {
        type: Object,
        required: true,
    },
});

const { isDark, toggleTheme } = useTheme();
const showingNavigationDropdown = ref(false);
const showingUserMenu = ref(false);

const logout = () => {
    router.post(route('personas.logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <Link :href="route('personas.home')" class="flex items-center space-x-3">
                            <img src="/icons/icon-192x192.png" alt="CTAccess" class="h-10 w-10" />
                            <div class="flex flex-col">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">CTAccess</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Portal de Personas</span>
                            </div>
                        </Link>

                        <!-- Navigation Links - Desktop -->
                        <div class="hidden md:flex md:ml-10 space-x-4">
                            <Link
                                :href="route('personas.home')"
                                :class="[
                                    'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                    route().current('personas.home')
                                        ? 'bg-sena-green-50 dark:bg-cyan-900/30 text-sena-green-700 dark:text-cyan-400'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                                ]"
                            >
                                <Icon name="home" :size="18" class="mr-2" />
                                Inicio
                            </Link>
                            
                            <Link
                                :href="route('personas.profile')"
                                :class="[
                                    'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                    route().current('personas.profile')
                                        ? 'bg-sena-green-50 dark:bg-cyan-900/30 text-sena-green-700 dark:text-cyan-400'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                                ]"
                            >
                                <Icon name="user" :size="18" class="mr-2" />
                                Mi Perfil
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button
                            @click="toggleTheme"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200"
                            :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                        >
                            <Icon :name="isDark ? 'sun' : 'moon'" :size="20" />
                        </button>

                        <!-- User Menu - Desktop -->
                        <div class="hidden md:flex items-center relative">
                            <button
                                @click="showingUserMenu = !showingUserMenu"
                                class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200"
                            >
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gradient-to-br from-sena-green-600 to-cyan-600 text-white font-bold">
                                    {{ persona.Nombre.charAt(0).toUpperCase() }}
                                </div>
                                <span class="max-w-[150px] truncate">{{ persona.Nombre }}</span>
                                <Icon name="chevron-down" :size="16" :class="{ 'rotate-180': showingUserMenu }" class="transition-transform" />
                            </button>

                            <!-- Dropdown -->
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div
                                    v-show="showingUserMenu"
                                    @click="showingUserMenu = false"
                                    class="absolute right-0 top-full mt-2 w-56 rounded-xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                                >
                                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ persona.Nombre }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ persona.correo }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ persona.TipoPersona }}</p>
                                    </div>
                                    
                                    <Link
                                        :href="route('personas.profile')"
                                        class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    >
                                        <Icon name="user" :size="18" class="mr-3" />
                                        Mi Perfil
                                    </Link>
                                    
                                    <button
                                        @click="logout"
                                        class="w-full flex items-center px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                    >
                                        <Icon name="log-out" :size="18" class="mr-3" />
                                        Cerrar Sesión
                                    </button>
                                </div>
                            </Transition>
                        </div>

                        <!-- Hamburger - Mobile -->
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="md:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <Icon :name="showingNavigationDropdown ? 'x' : 'menu'" :size="24" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 -translate-y-2"
                enter-to-class="transform opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="transform opacity-100 translate-y-0"
                leave-to-class="transform opacity-0 -translate-y-2"
            >
                <div v-show="showingNavigationDropdown" class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="px-4 py-3 space-y-1">
                        <!-- User Info -->
                        <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-gradient-to-br from-sena-green-600 to-cyan-600 text-white font-bold text-lg">
                                    {{ persona.Nombre.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ persona.Nombre }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ persona.correo }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ persona.TipoPersona }}</p>
                        </div>

                        <!-- Navigation Links -->
                        <Link
                            :href="route('personas.home')"
                            :class="[
                                'flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                route().current('personas.home')
                                    ? 'bg-sena-green-50 dark:bg-cyan-900/30 text-sena-green-700 dark:text-cyan-400'
                                    : 'text-gray-600 dark:text-gray-300'
                            ]"
                        >
                            <Icon name="home" :size="18" class="mr-3" />
                            Inicio
                        </Link>
                        
                        <Link
                            :href="route('personas.profile')"
                            :class="[
                                'flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                route().current('personas.profile')
                                    ? 'bg-sena-green-50 dark:bg-cyan-900/30 text-sena-green-700 dark:text-cyan-400'
                                    : 'text-gray-600 dark:text-gray-300'
                            ]"
                        >
                            <Icon name="user" :size="18" class="mr-3" />
                            Mi Perfil
                        </Link>

                        <button
                            @click="logout"
                            class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                        >
                            <Icon name="log-out" :size="18" class="mr-3" />
                            Cerrar Sesión
                        </button>
                    </div>
                </div>
            </Transition>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>CTAccess v2.0 - Sistema de Control de Acceso • SENA</p>
                    <p class="mt-1 text-xs">Portal de Personas</p>
                </div>
            </div>
        </footer>
    </div>
</template>
