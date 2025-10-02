<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Icon from '@/Components/Icon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

// Guard-aware helpers
const page = usePage();
const isSystem = computed(() => route().current('system.*') || page.url?.startsWith('/system'));
const displayName = computed(() => {
    const user = page.props?.auth?.user || {};
    return isSystem.value ? user.nombre : user.Nombre;
});
const secondaryText = computed(() => {
    const user = page.props?.auth?.user || {};
    return isSystem.value ? user.rol : user.correo;
});
const dashboardHref = computed(() => (isSystem.value ? route('system.celador.dashboard') ?? route('system.panel') : route('dashboard')));
const dashboardActive = computed(() => (isSystem.value ? route().current('system.celador.dashboard') : route().current('dashboard')));
const logoutHref = computed(() => (isSystem.value ? route('system.logout') : route('logout')));
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="dashboardHref">
                                    <ApplicationLogo
                                        classes="block h-9 w-auto object-contain"
                                    />
                                </Link>
                                
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-9 sm:-my-px sm:ms-12 sm:flex"
                            >
                                <!-- Links para app normal -->
                                <template v-if="!isSystem">
                                    <NavLink
                                        :href="dashboardHref"
                                        :active="dashboardActive"
                                    >
                                        Dashboard
                                    </NavLink>
                                </template>

                                <!-- Links para Sistema (Celador/Admin) -->
                                <template v-else>
                                    <NavLink :href="route('system.celador.dashboard')" :active="route().current('system.celador.dashboard')">
                                        Dashboard
                                    </NavLink>
                                    <NavLink :href="route('system.celador.accesos.index')" :active="route().current('system.celador.accesos.*')">
                                        Accesos
                                    </NavLink>
                                    <NavLink :href="route('system.celador.qr')" :active="route().current('system.celador.qr')">
                                        Verificación QR
                                    </NavLink>
                                    <NavLink :href="route('system.celador.incidencias.index')" :active="route().current('system.celador.incidencias.*')">
                                        Incidencias
                                    </NavLink>
                                    <NavLink :href="route('system.celador.historial.index')" :active="route().current('system.celador.historial.*')">
                                        Historial
                                    </NavLink>
                                    <NavLink :href="route('system.celador.personas.index')" :active="route().current('system.celador.personas.*')">
                                        Personas
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ displayName }}

                                                <Icon name="chevron-down" :size="16" class="-me-0.5 ms-2" />
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            v-if="!isSystem"
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="logoutHref"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <Icon :name="showingNavigationDropdown ? 'x' : 'menu'" :size="24" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <!-- Responsive links para app normal -->
                        <template v-if="!isSystem">
                            <ResponsiveNavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                        </template>

                        <!-- Responsive links para sistema -->
                        <template v-else>
                            <ResponsiveNavLink :href="route('system.celador.dashboard')" :active="route().current('system.celador.dashboard')">
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('system.celador.accesos.index')" :active="route().current('system.celador.accesos.*')">
                                Accesos
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('system.celador.qr')" :active="route().current('system.celador.qr')">
                                Verificación QR
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('system.celador.incidencias.index')" :active="route().current('system.celador.incidencias.*')">
                                Incidencias
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('system.celador.historial.index')" :active="route().current('system.celador.historial.*')">
                                Historial
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('system.celador.personas.index')" :active="route().current('system.celador.personas.*')">
                                Personas
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ displayName }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ secondaryText }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink v-if="!isSystem" :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="logoutHref"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
