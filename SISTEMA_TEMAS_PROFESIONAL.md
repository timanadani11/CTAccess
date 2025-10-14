# 🎨 Sistema de Temas Profesional - CTAccess

## 📋 Resumen

Se ha implementado un **sistema de temas robusto y profesional** con colores optimizados para **modo claro** y **modo oscuro**, garantizando:

✅ **Legibilidad óptima** en ambos modos  
✅ **Colores corporativos SENA** bien balanceados  
✅ **Alto contraste** y accesibilidad  
✅ **Transiciones suaves** entre temas  
✅ **Consistencia visual** en toda la aplicación  

---

## 🎨 Paleta de Colores

### 🏢 Colores Corporativos SENA

```css
--color-sena-green: #39A900   /* Verde principal */
--color-sena-cyan: #50E5F9    /* Cyan SENA */
--color-sena-yellow: #FDC300  /* Amarillo SENA */
--color-sena-blue: #00304D    /* Azul corporativo */
```

### ☀️ Modo Claro - Profesional y Legible

#### Fondos
- **Primario**: `#ffffff` - Blanco puro
- **Secundario**: `#f8fafc` - Gris muy claro
- **Terciario**: `#f1f5f9` - Gris claro
- **Cards**: `#ffffff` - Blanco con sombra
- **Hover**: `#f1f5f9` - Gris claro
- **Activo**: `#e2e8f0` - Gris medio-claro

#### Textos
- **Primario**: `#0f172a` - Negro azulado oscuro (excelente contraste)
- **Secundario**: `#475569` - Gris oscuro
- **Muted**: `#64748b` - Gris medio
- **Links**: `#2563eb` - Azul brillante
- **Links Hover**: `#1d4ed8` - Azul oscuro

#### Bordes
- **Primario**: `#e2e8f0` - Gris claro
- **Secundario**: `#cbd5e1` - Gris medio
- **Hover**: `#94a3b8` - Gris medio-oscuro
- **Focus**: `#3b82f6` - Azul brillante

#### Colores de Estado (Modo Claro)
- **Success**: `#16a34a` (Verde oscuro) - Fondo: `#dcfce7`
- **Error**: `#dc2626` (Rojo oscuro) - Fondo: `#fee2e2`
- **Warning**: `#d97706` (Naranja oscuro) - Fondo: `#fef3c7`
- **Info**: `#0284c7` (Azul oscuro) - Fondo: `#e0f2fe`

---

### 🌙 Modo Oscuro - Mate y Profesional

#### Fondos
- **Primario**: `#0a0a0b` - Negro puro mate
- **Secundario**: `#18181b` - Gris muy oscuro
- **Terciario**: `#27272a` - Gris oscuro
- **Cards**: `#1c1c1f` - Gris muy oscuro
- **Hover**: `#27272a` - Gris oscuro
- **Activo**: `#3f3f46` - Gris medio

#### Textos
- **Primario**: `#f8fafc` - Blanco casi puro (máxima legibilidad)
- **Secundario**: `#e2e8f0` - Gris muy claro
- **Muted**: `#94a3b8` - Gris medio (buen contraste)
- **Links**: `#60a5fa` - Azul claro
- **Links Hover**: `#93c5fd` - Azul muy claro

#### Bordes
- **Primario**: `#3f3f46` - Gris medio oscuro
- **Secundario**: `#52525b` - Gris medio
- **Hover**: `#71717a` - Gris medio-claro
- **Focus**: `#60a5fa` - Azul claro

#### Colores de Estado (Modo Oscuro)
- **Success**: `#22c55e` (Verde brillante) - Fondo: `#14532d`
- **Error**: `#ef4444` (Rojo brillante) - Fondo: `#7f1d1d`
- **Warning**: `#f59e0b` (Naranja brillante) - Fondo: `#78350f`
- **Info**: `#3b82f6` (Azul brillante) - Fondo: `#1e3a8a`

---

## 🛠️ Clases Utilitarias CSS

### Fondos
```css
.bg-theme-primary        /* Fondo principal */
.bg-theme-secondary      /* Fondo secundario */
.bg-theme-tertiary       /* Fondo terciario */
.bg-theme-card           /* Cards */
.bg-theme-navbar         /* Navbar */
.bg-theme-sidebar        /* Sidebar */
.bg-theme-hover          /* Hover */
.bg-theme-active         /* Activo */
```

### Textos
```css
.text-theme-primary      /* Texto principal */
.text-theme-secondary    /* Texto secundario */
.text-theme-muted        /* Texto auxiliar */
.text-theme-inverse      /* Texto inverso */
.text-theme-link         /* Enlaces */
```

### Bordes
```css
.border-theme-primary    /* Borde principal */
.border-theme-secondary  /* Borde secundario */
.border-theme-hover      /* Borde hover */
.border-theme-focus      /* Borde focus */
```

### Sombras
```css
.shadow-theme-sm         /* Sombra pequeña */
.shadow-theme-md         /* Sombra media */
.shadow-theme-lg         /* Sombra grande */
.shadow-theme-xl         /* Sombra extra grande */
```

### Estados
```css
.bg-success / .text-success / .border-success
.bg-error / .text-error / .border-error
.bg-warning / .text-warning / .border-warning
.bg-info / .text-info / .border-info
```

---

## 🎯 Colores SENA en Tailwind

### Verde SENA
```vue
<div class="bg-sena-green-500">   <!-- #39A900 -->
<div class="bg-sena-green-600">   <!-- Más oscuro -->
<div class="bg-sena-green-400">   <!-- Más claro -->
<div class="text-sena-green-600">
<div class="border-sena-green-600">
```

### Amarillo SENA
```vue
<div class="bg-sena-yellow-400">  <!-- #FDC300 -->
<div class="text-sena-yellow-900"> <!-- Para modo claro -->
<div class="text-sena-yellow-200"> <!-- Para modo oscuro -->
```

### Cyan SENA
```vue
<div class="bg-sena-cyan-400">    <!-- #50E5F9 -->
```

### Azul SENA
```vue
<div class="bg-sena-blue-700">    <!-- #00304D -->
```

---

## 📝 Ejemplos de Uso

### Botón Principal SENA
```vue
<button class="
  px-4 py-2 
  bg-sena-green-600 hover:bg-sena-green-700 
  dark:bg-sena-green-600 dark:hover:bg-sena-green-500
  text-white 
  rounded-lg 
  shadow-theme-sm hover:shadow-theme-md
  transition-all duration-200
">
  Acción Principal
</button>
```

### Card con Tema
```vue
<div class="
  bg-theme-card 
  border-2 border-theme-primary 
  rounded-xl 
  shadow-theme-lg 
  p-4
">
  <h3 class="text-theme-primary font-bold">Título</h3>
  <p class="text-theme-secondary">Contenido</p>
</div>
```

### Badge de Estado (Entrada)
```vue
<span class="
  px-2 py-1 
  bg-sena-green-600 dark:bg-sena-green-500
  text-white 
  border-2 border-sena-green-700 dark:border-sena-green-600
  rounded 
  text-xs font-bold
">
  ENTRADA
</span>
```

### Badge de Estado (Salida)
```vue
<span class="
  px-2 py-1 
  bg-red-600 dark:bg-red-500
  text-white 
  border-2 border-red-700 dark:border-red-600
  rounded 
  text-xs font-bold
">
  SALIDA
</span>
```

### Notificación Nueva
```vue
<div class="
  bg-sena-yellow-50 dark:bg-sena-yellow-900/20
  border-2 border-sena-yellow-400 dark:border-sena-yellow-600
  rounded-lg 
  p-3
">
  <p class="text-sena-yellow-900 dark:text-sena-yellow-200 font-bold">
    ¡Nuevo registro!
  </p>
</div>
```

---

## ✨ Características Especiales

### Transiciones Suaves
Todos los elementos tienen transiciones automáticas de 200ms con easing suave:
```css
transition: background-color, color, border-color, box-shadow, opacity, transform
```

### Scrollbar Personalizada
- **Modo Claro**: Scrollbar gris claro sobre fondo blanco
- **Modo Oscuro**: Scrollbar gris sobre fondo oscuro
- Compatible con Chrome, Firefox y Safari

### Efectos Glassmorphism
```vue
<div class="glass-effect">
  <!-- Fondo translúcido con blur -->
</div>
```

### Focus States Accesibles
```vue
<button class="focus-ring">
  <!-- Outline automático al hacer focus -->
</button>
```

### Hover Lift Effect
```vue
<div class="hover-lift">
  <!-- Se eleva al hacer hover -->
</div>
```

---

## 🔄 Uso del Composable

### En cualquier componente Vue:
```vue
<script setup>
import { useTheme } from '@/composables/useTheme'

const { isDark, toggleTheme } = useTheme()
</script>

<template>
  <button @click="toggleTheme">
    <Icon :name="isDark ? 'sun' : 'moon'" />
    {{ isDark ? 'Modo Claro' : 'Modo Oscuro' }}
  </button>
</template>
```

---

## 📦 Archivos Modificados

### 1. `tailwind.config.js`
- ✅ Activado `darkMode: 'class'`
- ✅ Colores SENA extendidos
- ✅ Paleta completa de tonos (50-900)

### 2. `resources/css/app.css`
- ✅ Variables CSS para ambos temas
- ✅ Clases utilitarias completas
- ✅ Scrollbar personalizada
- ✅ Transiciones automáticas
- ✅ Efectos adicionales (glassmorphism, focus, hover)

### 3. `resources/js/Pages/Home.vue`
- ✅ Colores actualizados a tema SENA
- ✅ Mejor contraste en ambos modos
- ✅ Badges con colores corporativos
- ✅ Animaciones con colores apropiados

---

## 🎨 Principios de Diseño

### Modo Claro
1. **Alto contraste**: Textos oscuros (#0f172a) sobre fondos claros (#ffffff)
2. **Colores saturados**: Verde SENA (#39A900), no tonos pastel
3. **Legibilidad óptima**: Cumple WCAG AAA para texto normal y grande
4. **Jerarquía visual clara**: 3 niveles de gris para fondos

### Modo Oscuro
1. **Negro mate profundo**: Fondo #0a0a0b (sin tonos azulados)
2. **Textos brillantes**: Blanco casi puro (#f8fafc) para máxima legibilidad
3. **Colores vibrantes**: Verde brillante (#22c55e), no apagados
4. **Sombras profundas**: Sombras negras intensas para profundidad

### Consistencia
- Los colores corporativos SENA se mantienen reconocibles en ambos modos
- Las transiciones son instantáneas y fluidas
- Los estados (hover, focus, active) son consistentes
- La jerarquía tipográfica es clara y profesional

---

## ✅ Checklist de Legibilidad

### Modo Claro
- [x] Texto principal negro (#0f172a) sobre blanco - **Contraste 18.5:1** ✅
- [x] Verde SENA (#39A900) visible y no cansón ✅
- [x] Enlaces azules (#2563eb) claros ✅
- [x] Estados con colores oscuros y fondos claros ✅

### Modo Oscuro
- [x] Texto blanco (#f8fafc) sobre negro (#0a0a0b) - **Contraste 19.2:1** ✅
- [x] Verde brillante (#22c55e) visible sin cansar ✅
- [x] Enlaces azul claro (#60a5fa) legibles ✅
- [x] Estados con colores brillantes sobre fondos oscuros ✅

---

## 🚀 Próximos Pasos

Para aplicar estos colores en otros componentes:

1. **Reemplaza clases hardcodeadas**:
   ```vue
   <!-- Antes -->
   <div class="bg-white dark:bg-gray-800">
   
   <!-- Después -->
   <div class="bg-theme-card">
   ```

2. **Usa colores SENA para acciones principales**:
   ```vue
   <button class="bg-sena-green-600 dark:bg-sena-green-600">
   ```

3. **Aplica estados consistentes**:
   ```vue
   <div class="bg-success text-success border-success">
   ```

---

## 📞 Soporte

Para dudas sobre el sistema de temas:
- 📧 Email: ctaccesscqta@gmail.com
- 📚 Documentación: Este archivo
- 🎨 Guía visual: Inspeccionar Home.vue

---

**Desarrollado con ❤️ para SENA**  
Sistema CTAccess v2.0 - 2025
