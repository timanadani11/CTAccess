<template>
    <img
      :src="logoUrl"
      :alt="alt"
      :class="classes"
      loading="eager"
      decoding="sync"
    />
  </template>
  
  <script setup>
  import logoUrl from '../assets/logo.png' // relativo a este componente
  
  defineProps({
    alt: { type: String, default: 'Logo' },
    // Ajusta clases por defecto según tu diseño (Tailwind u otras clases)
    classes: { type: String, default: 'h-10 w-auto' },
  })
  </script>