# CTAccess - Sistema de Control de Acceso QR 🏢🔐

**CTAccess** es un sistema completo de control de acceso basado en códigos QR desarrollado con tecnologías modernas de vanguardia. Combina Laravel 12, Inertia.js y Vue 3 para ofrecer una experiencia de usuario fluida y una arquitectura robusta.

## 🚀 Características Principales

### ✅ Funcionalidades Core
- **Control de Acceso QR**: Sistema completo de registro de entradas/salidas mediante códigos QR
- **Gestión de Personas**: CRUD completo con relaciones a portátiles y vehículos
- **Sistema de Roles**: Doble autenticación (usuarios web + usuarios del sistema)
- **Dashboard Interactivo**: Panel de control para celadores con estadísticas en tiempo real
- **Sistema de Temas**: Modo claro/oscuro con colores corporativos
- **PWA Completo**: Aplicación web progresiva instalable como app nativa

### ✅ Características Avanzadas
- **Registro Instantáneo**: Opción de registro automático sin confirmación
- **Historial y Reportes**: Seguimiento completo de accesos con filtros y exportación PDF
- **Gestión de Incidencias**: Sistema para reportar y gestionar problemas de acceso
- **Búsqueda Avanzada**: Búsqueda en tiempo real con filtros por tipo de persona
- **Modal de Detalles**: Vista rápida de información de personas sin navegación
- **Iconografía Moderna**: Sistema de iconos Lucide Vue optimizado

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 12** - Framework PHP moderno
- **PHP 8.2+** - Lenguaje de programación
- **Inertia.js** - Puente entre Laravel y Vue.js (sin APIs REST)
- **Laravel Sanctum** - Autenticación segura
- **MySQL** - Base de datos relacional

### Frontend
- **Vue 3** - Framework JavaScript progresivo
- **Inertia.js** - Integración SPA sin APIs tradicionales
- **Tailwind CSS** - Framework CSS utilitario
- **Lucide Vue** - Iconografía moderna y consistente
- **Vite** - Herramienta de construcción rápida

### Características Técnicas
- **PWA** - Aplicación web progresiva instalable
- **Sistema de Temas** - Modo claro/oscuro automático
- **Responsive Design** - Compatible con móviles y tablets
- **Composición API** - Vue 3 Composition API
- **TypeScript Ready** - Configurado para TypeScript

## 📋 Requisitos del Sistema

- **PHP 8.2** o superior
- **Composer** - Gestor de dependencias PHP
- **Node.js 18+** - Entorno JavaScript
- **MySQL 8.0+** - Base de datos
- **Git** - Control de versiones

## 🚀 Instalación y Configuración

### 1. Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/ctaccess.git
cd ctaccess
```

### 2. Instalar Dependencias PHP
```bash
composer install
```

### 3. Instalar Dependencias JavaScript
```bash
npm install
```

### 4. Configurar Variables de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar Base de Datos
```bash
# Crear base de datos MySQL
# Configurar credenciales en .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ctaccess
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Ejecutar Migraciones y Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 7. Compilar Assets
```bash
npm run dev
```

### 8. Servir la Aplicación
```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## 👥 Usuarios de Prueba

### Sistema de Personas (Autenticación Web)
- **juan@empresa.com** / `password123` (Empleado)
- **maria@visitante.com** / `password123` (Visitante)
- **carlos@contratista.com** / `password123` (Contratista)
- **ana@empresa.com** / `password123` (Empleado)

### Sistema Interno (Celador/Admin)
- **admin** / `admin12345` (Administrador General)
- **celador** / `celador12345` (Celador Principal)

## 🎯 Uso del Sistema

### Como Visitante/Empleado
1. **Registro**: Crear cuenta en la página principal
2. **Inicio de Sesión**: Acceder con credenciales
3. **Panel Personal**: Ver información personal y accesos

### Como Celador
1. **Inicio de Sesión**: `/system/login`
2. **Dashboard**: Panel principal con estadísticas
3. **Gestión de Personas**: Buscar y ver detalles de personas
4. **Control QR**: Escanear códigos QR para registrar accesos
5. **Historial**: Ver accesos del día y generar reportes

### Como Administrador
1. **Inicio de Sesión**: `/system/login`
2. **Panel Admin**: Gestión completa del sistema
3. **Usuarios del Sistema**: Crear y gestionar celadores
4. **Reportes**: Vista general de todas las operaciones

## 📱 Características PWA

- **Instalación**: Se puede instalar como aplicación nativa
- **Offline**: Funciona sin conexión (limitado)
- **Responsive**: Diseño adaptativo para móviles
- **Notificaciones**: Sistema de notificaciones push
- **Tema Corporativo**: Colores consistentes con la marca

## 🎨 Sistema de Temas

El sistema incluye un completo sistema de temas con:

- **Tema Claro**: Diseño limpio y profesional
- **Tema Oscuro**: Reducción de fatiga visual
- **Colores Corporativos**: Verde (#39A900), Azul (#50E5F9), Amarillo (#FDC300)
- **Persistencia**: Recordar preferencias del usuario
- **Transiciones Suaves**: Animaciones fluidas entre temas

## 🔧 Desarrollo

### Comandos Útiles

```bash
# Desarrollo
npm run dev              # Servidor de desarrollo
php artisan serve        # Servidor PHP

# Producción
npm run build           # Construir para producción
php artisan optimize    # Optimizar aplicación

# Base de Datos
php artisan migrate     # Ejecutar migraciones
php artisan db:seed     # Cargar datos de prueba
php artisan tinker      # Consola interactiva

# Utilidades
php artisan test:email  # Probar envío de emails
php artisan pail        # Monitor de logs
```

### Estructura del Proyecto

```
ctaccess/
├── app/                    # Código de la aplicación
│   ├── Http/Controllers/   # Controladores
│   ├── Models/             # Modelos Eloquent
│   ├── Services/           # Lógica de negocio
│   └── ...
├── database/               # Migraciones y seeders
├── public/                 # Archivos públicos
├── resources/              # Vistas y assets
│   ├── css/               # Estilos
│   ├── js/                # Código JavaScript
│   │   ├── Components/    # Componentes Vue
│   │   ├── Pages/         # Páginas Vue
│   │   └── Layouts/       # Layouts Vue
│   └── views/             # Vistas Blade
├── routes/                 # Definición de rutas
├── storage/                # Archivos temporales
└── tests/                  # Tests automatizados
```

## 🔒 Seguridad

- **Autenticación Robusta**: Doble sistema de autenticación
- **Autorización**: Control de acceso basado en roles
- **Validación**: Validación estricta de datos
- **CSRF Protection**: Protección contra ataques CSRF
- **Rate Limiting**: Límites de intentos de login
- **Logging**: Registro completo de operaciones

## 📊 Funcionalidades Específicas

### Sistema QR
- **Escáner Integrado**: Cámara del dispositivo
- **Entrada Manual**: Ingreso manual de códigos
- **Validación Automática**: Detección de formato correcto
- **Registro Dual**: Persona + portátil opcional
- **Estados de Acceso**: Activo, Finalizado, Incidencia

### Gestión de Personas
- **Información Completa**: Datos personales y recursos asignados
- **Relaciones**: Portátiles y vehículos asociados
- **Búsqueda Avanzada**: Por nombre, documento, QR
- **Filtros**: Por tipo de persona
- **Vista Detallada**: Modal con información completa

### Dashboard Celador
- **Estadísticas Tiempo Real**: Accesos activos, historial del día
- **Módulos Especializados**: Personas, QR, Incidencias, Historial
- **Navegación Intuitiva**: Sidebar organizado por funciones
- **Responsive**: Funciona perfectamente en móviles

## 🌐 Internacionalización

- **Español**: Idioma principal del sistema
- **Laravel Localization**: Soporte para múltiples idiomas
- **UTF-8**: Soporte completo para caracteres especiales

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema CTAccess, contactar al equipo de desarrollo.

---

**Desarrollado con ❤️ usando Laravel 12 + Vue 3 + Inertia.js**

*Este sistema representa una solución completa y moderna para el control de acceso empresarial, integrando las mejores prácticas de desarrollo web actual.*
