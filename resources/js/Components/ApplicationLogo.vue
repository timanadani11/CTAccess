<template>
    <img
      :src="logoUrl"
      :alt="alt"
      :class="classes"
      loading="eager"
      decoding="sync"
      class="object-contain"
    />
  </template>
  
  <script setup>
  import logoUrl from '../assets/logo.png' // relativo a este componente
  
  defineProps({
    alt: { type: String, default: 'CTAccess Logo' },
    // Ajusta clases por defecto según tu diseño (Tailwind u otras clases)
    classes: { type: String, default: 'h-10 w-auto object-contain' },
  })
  </script>