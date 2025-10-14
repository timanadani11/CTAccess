<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-sena-green-600 dark:border-cyan-500 text-start text-base font-medium text-sena-green-700 dark:text-cyan-400 bg-sena-green-50 dark:bg-cyan-900/20 focus:outline-none focus:text-sena-green-800 dark:focus:text-cyan-300 focus:bg-sena-green-100 dark:focus:bg-cyan-900/30 focus:border-sena-green-700 dark:focus:border-cyan-400 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-800 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
