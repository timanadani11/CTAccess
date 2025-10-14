# 🎨 Actualización del Sistema de Temas - Modo Corporativo

## 📋 Resumen de Cambios

Se ha actualizado el sistema de temas para usar **colores corporativos diferenciados**:

- **☀️ Modo Claro**: Verde SENA oscuro (#2d8700) - Profesional y legible
- **🌙 Modo Oscuro**: Azul Corporativo (#00304D y variantes) - Elegante y distintivo

---

## 🎨 Nuevos Colores por Modo

### ☀️ MODO CLARO - Verde SENA Oscuro

#### Color Principal
```css
--color-primary-light: #2d8700        /* Verde oscuro principal */
--color-primary-hover-light: #216500  /* Verde más oscuro para hover */
```

#### Uso en Componentes
- ✅ Botones principales: Verde oscuro saturado (#2d8700)
- ✅ Enlaces y accents: Verde oscuro visible
- ✅ Iconos de entrada: Verde SENA (#39A900 a #2d8700)
- ✅ Headers y títulos importantes: Verde corporativo
- ✅ Badges de entrada: Verde oscuro con buen contraste

**Características**:
- Alto contraste sobre fondos blancos
- No es un verde pastel, es profesional
- Fácil de leer y no cansa la vista
- Mantiene identidad SENA

---

### 🌙 MODO OSCURO - Azul Corporativo

#### Color Principal
```css
--color-primary-dark: #2b7bbf         /* Azul corporativo claro */
--color-primary-hover-dark: #3b8fd8   /* Azul más brillante para hover */
```

#### Fondos Azulados
```css
--bg-primary: #0a0f14        /* Negro con toque azul */
--bg-secondary: #10181f      /* Azul muy oscuro */
--bg-tertiary: #1a2633       /* Azul oscuro */
--bg-card: #141d26           /* Cards azul muy oscuro */
--bg-navbar: #0f1821         /* Navbar negro azul */
--bg-hover: #1f2d3d          /* Hover azul oscuro */
--bg-active: #2a3f54         /* Activo azul medio */
```

#### Textos con Tonos Azulados
```css
--text-primary: #f8fafc      /* Blanco puro (sin cambios) */
--text-secondary: #d1e3f5    /* Gris azulado claro */
--text-muted: #8ba5bf        /* Gris azulado medio */
--text-link: #50a8e8         /* Cyan corporativo */
--text-link-hover: #7fc4f5   /* Cyan más claro */
```

#### Bordes Azulados
```css
--border-primary: #2a3f54    /* Azul oscuro medio */
--border-secondary: #3d5468  /* Azul medio */
--border-hover: #4d6b85      /* Azul medio-claro */
--border-focus: #50a8e8      /* Cyan corporativo */
```

#### Uso en Componentes
- ✅ Botones principales: Azul corporativo (#2b7bbf)
- ✅ Enlaces y accents: Cyan SENA (#50E5F9, #50a8e8)
- ✅ Iconos de entrada: Cyan brillante (#22d3ee)
- ✅ Headers y títulos importantes: Azul oscuro corporativo
- ✅ Badges de entrada: Cyan corporativo con buen contraste
- ✅ Reloj digital: Glow cyan (#50E5F9)

**Características**:
- Fondos negro-azulados sutiles (no grises neutros)
- Textos con excelente contraste (19:1+)
- Cyan SENA como color de accent principal
- Atmósfera corporativa profesional y distintiva
- No cansa la vista, es elegante

---

## 🎯 Colores de Estado Actualizados

### Modo Oscuro - Estados con Base Azulada

```css
/* Success - Cyan SENA (en lugar de verde) */
--color-success: #50E5F9
--color-success-bg: #0a2e3a
--color-success-border: #1e5a8e

/* Warning - Amarillo SENA */
--color-warning: #FDC300
--color-warning-bg: #3d2f0a
--color-warning-border: #d97706

/* Info - Cyan corporativo */
--color-info: #50a8e8
--color-info-bg: #0d2538
--color-info-border: #2b7bbf

/* Error - Rojo (sin cambios mayores) */
--color-error: #ef4444
--color-error-bg: #3d1a1a
--color-error-border: #dc2626
```

---

## 🔄 Componentes Actualizados en Home.vue

### 1. Botón "Registrarse"
```vue
<!-- MODO CLARO: Verde oscuro SENA -->
<!-- MODO OSCURO: Azul corporativo -->
<Link class="
  bg-sena-green-600 hover:bg-sena-green-700 
  dark:bg-blue-600 dark:hover:bg-blue-500
">
  Registrarse
</Link>
```

### 2. Reloj Digital
```vue
<!-- Icono -->
<Icon class="text-sena-green-600 dark:text-cyan-400" />

<!-- Glow del reloj digital -->
Modo claro: Verde oscuro (#2d8700)
Modo oscuro: Cyan SENA (#50E5F9)
```

### 3. Avatar de Entrada
```vue
<div class="
  bg-sena-green-600 border-sena-green-700 
  dark:bg-cyan-600 dark:border-cyan-500
">
  <Icon name="log-in" />
</div>
```

### 4. Badge "IN" (Entrada)
```vue
<div class="
  bg-sena-green-600 border-sena-green-700 
  dark:bg-cyan-600 dark:border-cyan-500
">
  IN
</div>
```

### 5. Título "Actividad Reciente"
```vue
<div class="
  bg-sena-green-700 dark:bg-blue-900 
  border-sena-green-600 dark:border-blue-800
">
  <!-- Indicador Live -->
  <div class="bg-cyan-400">...</div>
  
  <!-- Contador de registros -->
  <span class="text-gray-200 dark:text-cyan-200">
    {{ recentActivity.length }} registros
  </span>
</div>
```

---

## 🆕 Nuevas Clases CSS Disponibles

### Accent Corporativo (Auto-adaptable)
```css
/* Verde en modo claro, Azul en modo oscuro */
.accent-corporate             /* Color de texto */
.bg-accent-corporate          /* Fondo */
.border-accent-corporate      /* Borde */
.hover-accent-corporate       /* Hover state */
```

### Ejemplo de Uso
```vue
<button class="accent-corporate border-accent-corporate hover-accent-corporate">
  <!-- Verde oscuro en claro, Azul en oscuro -->
  Botón Adaptable
</button>
```

### Glow Corporativo
```css
.glow-corporate  /* Verde en claro, Azul en oscuro */
```

### Glassmorphism Actualizado
```css
.glass-effect
/* Modo claro: Blanco translúcido */
/* Modo oscuro: Azul corporativo translúcido */
```

---

## 📊 Comparación Visual

### Modo Claro (Verde SENA)
```
Fondos:     Blanco (#ffffff) → Gris claro (#f1f5f9)
Textos:     Negro (#0f172a) → Gris oscuro (#475569)
Accent:     Verde SENA oscuro (#2d8700)
Botones:    Verde oscuro (#2d8700) hover (#216500)
Estados:    Verde oscuro, Amarillo, Rojo, Azul
```

### Modo Oscuro (Azul Corporativo)
```
Fondos:     Negro azulado (#0a0f14) → Azul oscuro (#1a2633)
Textos:     Blanco puro (#f8fafc) → Gris azulado (#d1e3f5)
Accent:     Cyan SENA (#50E5F9) / Azul corporativo (#2b7bbf)
Botones:    Azul corporativo (#2b7bbf) hover (#3b8fd8)
Estados:    Cyan brillante, Amarillo SENA, Rojo, Azul info
```

---

## ✅ Beneficios del Cambio

### Modo Claro con Verde Oscuro
✅ **Profesional**: Verde oscuro saturado (#2d8700), no pastel  
✅ **Alto contraste**: Excelente legibilidad sobre blanco  
✅ **Identidad SENA**: Mantiene el verde corporativo  
✅ **No cansón**: Color bien balanceado para uso prolongado  

### Modo Oscuro con Azul Corporativo
✅ **Distintivo**: Azul corporativo (#00304D) en lugar de gris neutro  
✅ **Elegante**: Atmósfera profesional nocturna  
✅ **Corporativo**: Refuerza identidad SENA con azul oficial  
✅ **Excelente contraste**: Cyan (#50E5F9) como accent brillante  
✅ **Profesional**: Ambiente de trabajo serio y sofisticado  

---

## 🎨 Paleta Completa de Colores

### Colores Corporativos SENA (Ambos Modos)
```css
Verde Principal:    #39A900
Verde Oscuro:       #2d8700
Verde Más Oscuro:   #216500
Cyan SENA:          #50E5F9
Amarillo SENA:      #FDC300
Azul Corporativo:   #00304D
Azul Claro:         #1e5a8e
Azul Más Claro:     #2b7bbf
```

### Modo Claro - Fondos
```css
Primario:    #ffffff  (Blanco)
Secundario:  #f8fafc  (Gris muy claro)
Terciario:   #f1f5f9  (Gris claro)
Card:        #ffffff  (Blanco)
Hover:       #f1f5f9  (Gris claro)
```

### Modo Oscuro - Fondos (Azulados)
```css
Primario:    #0a0f14  (Negro azulado)
Secundario:  #10181f  (Azul muy oscuro)
Terciario:   #1a2633  (Azul oscuro)
Card:        #141d26  (Azul muy oscuro)
Hover:       #1f2d3d  (Azul oscuro)
```

---

## 🚀 Cómo Aplicar en Otros Componentes

### Botones Principales
```vue
<!-- Modo claro: Verde, Modo oscuro: Azul -->
<button class="
  bg-sena-green-600 hover:bg-sena-green-700 
  dark:bg-blue-600 dark:hover:bg-blue-500 
  text-white
">
  Acción
</button>
```

### Links / Accents
```vue
<!-- Usa la clase auto-adaptable -->
<a href="#" class="accent-corporate hover-accent-corporate">
  Enlace Corporativo
</a>
```

### Cards con Tema
```vue
<div class="bg-theme-card border-2 border-theme-primary">
  <h3 class="text-theme-primary">Título</h3>
  <p class="text-theme-secondary">Contenido</p>
</div>
```

### Iconos de Estado (Entrada)
```vue
<!-- Verde en claro, Cyan en oscuro -->
<Icon class="
  text-sena-green-600 dark:text-cyan-400
" />
```

### Badges de Éxito
```vue
<!-- Verde en claro, Cyan en oscuro -->
<span class="
  bg-sena-green-100 dark:bg-cyan-900/20 
  text-sena-green-700 dark:text-cyan-300 
  border-sena-green-300 dark:border-cyan-600
">
  Exitoso
</span>
```

---

## 📚 Archivos Modificados

1. **resources/css/app.css**
   - Variables CSS actualizadas
   - Fondos azulados para modo oscuro
   - Textos y bordes con tonos azules
   - Estados con Cyan SENA
   - Nuevas clases `.accent-corporate`, `.glow-corporate`

2. **resources/js/Pages/Home.vue**
   - Botón "Registrarse": Verde → Azul en oscuro
   - Reloj digital: Cyan en oscuro
   - Avatares de entrada: Cyan en oscuro
   - Badges IN/OUT: Cyan en oscuro
   - Título actividad: Azul corporativo en oscuro

3. **Compilado**
   - Assets regenerados con nuevos estilos
   - PWA actualizado con nuevos colores

---

## 🎯 Resultado Final

### Modo Claro ☀️
- Fondo: Blanco limpio
- Accent: **Verde SENA oscuro** (#2d8700)
- Ambiente: Profesional, corporativo, enérgico

### Modo Oscuro 🌙
- Fondo: Negro-azulado corporativo
- Accent: **Cyan SENA brillante** (#50E5F9) y **Azul corporativo** (#2b7bbf)
- Ambiente: Elegante, distintivo, profesional nocturno

---

## ✨ Próximos Pasos Recomendados

Para aplicar este esquema en todo el sistema:

1. **Dashboard**: Usar `.accent-corporate` para métricas principales
2. **Personas**: Avatares con colores corporativos
3. **Accesos**: Badges IN (verde/cyan) y OUT (rojo) consistentes
4. **Incidencias**: Estados con colores actualizados
5. **Sidebar**: Fondo azulado en modo oscuro

---

**Desarrollado para SENA**  
Sistema CTAccess v2.0 - Octubre 2025  
Identidad visual corporativa mejorada
