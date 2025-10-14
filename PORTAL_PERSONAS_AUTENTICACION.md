# Portal de Personas - Sistema de Autenticación

## 📋 Descripción

Sistema completo de autenticación para personas en CTAccess, implementado siguiendo principios SOLID, buenas prácticas de programación y arquitectura limpia con Laravel + Vue.js + Inertia.js.

## 🏗️ Arquitectura

### Backend (Laravel)

#### Controllers
Siguiendo el **Single Responsibility Principle (SRP)**:

1. **`AuthenticatedSessionController`** (`app/Http/Controllers/Personas/Auth/`)
   - ✅ Responsabilidad única: Gestionar sesiones de autenticación
   - `create()`: Muestra vista de login
   - `store()`: Autentica persona
   - `destroy()`: Cierra sesión
   - Incluye logs de auditoría

2. **`DashboardController`** (`app/Http/Controllers/Personas/`)
   - ✅ Responsabilidad única: Gestionar dashboard/home de personas
   - `index()`: Muestra home con información completa
   - Carga relaciones (portátiles, vehículos, accesos)
   - Calcula estadísticas en tiempo real

3. **`ProfileController`** (`app/Http/Controllers/Personas/`)
   - ✅ Responsabilidad única: Gestionar perfil de persona
   - `show()`: Muestra perfil con QR code y datos completos

#### Request Validation
Siguiendo el **Dependency Inversion Principle (DIP)**:

- **`LoginRequest`** (`app/Http/Requests/Personas/Auth/`)
  - ✅ Validación centralizada y reutilizable
  - ✅ Rate limiting (5 intentos)
  - ✅ Mensajes de error personalizados en español
  - ✅ Protección contra ataques de fuerza bruta

#### Models
- **`Persona`** extiende `Authenticatable`
  - ✅ Implementa `Notifiable`
  - ✅ Usa guard 'web'
  - ✅ Campo de autenticación: `correo`
  - ✅ Campo de contraseña: `contraseña` (hasheada)
  - ✅ Relaciones: portatiles, vehiculos, accesos

### Frontend (Vue.js + Inertia)

#### Layouts
1. **`PersonaLayout.vue`** (`resources/js/Layouts/`)
   - ✅ Navegación responsive
   - ✅ Menú de usuario con dropdown
   - ✅ Theme switcher (light/dark)
   - ✅ Mobile-first design

#### Pages

1. **Login** (`resources/js/Pages/Personas/Auth/Login.vue`)
   - ✅ Diseño corporativo SENA
   - ✅ Animaciones fluidas
   - ✅ Validación en tiempo real
   - ✅ Manejo de errores
   - ✅ Remember me
   - ✅ Theme switcher

2. **Home/Dashboard** (`resources/js/Pages/Personas/Index.vue`)
   - ✅ Bienvenida personalizada
   - ✅ Información personal
   - ✅ Estadísticas de accesos
   - ✅ Lista de portátiles
   - ✅ Lista de vehículos
   - ✅ Historial de accesos recientes

3. **Profile** (`resources/js/Pages/Personas/Profile.vue`)
   - ✅ QR Code visible
   - ✅ Información completa
   - ✅ Portátiles y vehículos registrados

## 🔐 Configuración de Autenticación

### Guard Configurado
En `config/auth.php`:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'personas',
    ],
    'system' => [
        'driver' => 'session',
        'provider' => 'system_users',
    ],
],

'providers' => [
    'personas' => [
        'driver' => 'eloquent',
        'model' => App\Models\Persona::class,
    ],
],
```

## 🛣️ Rutas

### Portal de Personas (`/personas/*`)

#### Rutas Públicas (guest:web)
```php
GET  /personas/login       -> Formulario de login
POST /personas/login       -> Autenticar persona
```

#### Rutas Protegidas (auth:web)
```php
GET  /personas/home        -> Dashboard/Home
GET  /personas/perfil      -> Perfil de la persona
POST /personas/logout      -> Cerrar sesión
```

## 🎨 Características UI/UX

### Diseño
- ✅ **Responsive**: Mobile-first design
- ✅ **Dark Mode**: Theme switcher integrado
- ✅ **Animaciones**: Transiciones fluidas
- ✅ **Accesibilidad**: ARIA labels, keyboard navigation
- ✅ **Colores Corporativos SENA**: Verde SENA + Cyan

### Componentes
- ✅ Partículas animadas de fondo
- ✅ Efectos de luz (blob animations)
- ✅ Iconos consistentes (lucide-vue-next)
- ✅ Loading states
- ✅ Error handling visual

## 🔒 Seguridad

### Implementado
1. ✅ **Rate Limiting**: 5 intentos de login por minuto
2. ✅ **CSRF Protection**: Token validation
3. ✅ **Password Hashing**: Bcrypt automático
4. ✅ **Session Security**: 
   - Regeneración de token en login
   - Invalidación en logout
5. ✅ **Auditoría**: Logs de login/logout
6. ✅ **SQL Injection Protection**: Eloquent ORM
7. ✅ **XSS Protection**: Vue.js auto-escaping

## 📊 Base de Datos

### Tabla: `personas`
```sql
- idPersona (PK)
- documento
- Nombre
- TipoPersona
- correo (UNIQUE) -> campo de autenticación
- contraseña (HASHED) -> bcrypt
- qrCode
- remember_token
- timestamps
```

## 🧪 Testing

### Datos de Prueba (Seeders)
```php
Correo: juan@empresa.com
Contraseña: password123

Correo: maria@visitante.com
Contraseña: password123

Correo: carlos@contratista.com
Contraseña: password123

Correo: ana@empresa.com
Contraseña: password123
```

## 📝 Principios SOLID Aplicados

### 1. Single Responsibility Principle (SRP) ✅
- Cada controlador tiene una única responsabilidad
- `AuthenticatedSessionController`: solo autenticación
- `DashboardController`: solo dashboard
- `ProfileController`: solo perfil

### 2. Open/Closed Principle (OCP) ✅
- Controllers extensibles sin modificación
- Layouts reutilizables
- Componentes modulares

### 3. Liskov Substitution Principle (LSP) ✅
- `Persona` extiende `Authenticatable` correctamente
- Implementa todas las interfaces necesarias

### 4. Interface Segregation Principle (ISP) ✅
- Request validation separado
- Layouts específicos para cada rol

### 5. Dependency Inversion Principle (DIP) ✅
- Uso de interfaces (Request classes)
- Inyección de dependencias en constructores
- Facade pattern (Auth, Log)

## 🚀 Flujo de Autenticación

### Login Flow
```
1. Usuario visita /personas/login
2. Ingresa correo y contraseña
3. LoginRequest valida datos
4. Rate limiter verifica intentos
5. Auth::guard('web')->attempt() autentica
6. Sesión se regenera
7. Log de auditoría
8. Redirect a /personas/home
```

### Home Flow
```
1. Middleware auth:web verifica sesión
2. DashboardController obtiene persona autenticada
3. Carga relaciones (eager loading)
4. Calcula estadísticas
5. Renderiza vista Index.vue con PersonaLayout
```

### Logout Flow
```
1. Usuario hace clic en "Cerrar Sesión"
2. POST a /personas/logout
3. Log de auditoría
4. Auth::guard('web')->logout()
5. Invalidar sesión
6. Regenerar token CSRF
7. Redirect a home público
```

## 📱 Características Adicionales

### Dashboard
- ✅ Estadísticas en tiempo real
- ✅ Accesos del mes actual
- ✅ Total de accesos
- ✅ Últimos 10 accesos
- ✅ QR Code descargable

### Responsive Design
- ✅ Desktop: Grid layout, sidebar navigation
- ✅ Tablet: Optimized spacing
- ✅ Mobile: Hamburger menu, vertical stack

### PWA Ready
- ✅ Service Worker
- ✅ Offline capability
- ✅ Install prompt

## 🔧 Comandos Útiles

### Desarrollo
```bash
# Compilar assets en desarrollo
npm run dev

# Compilar para producción
npm run build

# Reiniciar base de datos con seeders
php artisan migrate:fresh --seed

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Testing
```bash
# Probar login
# URL: http://localhost/personas/login
# Credenciales: Ver seeders arriba
```

## 📈 Mejoras Futuras

- [ ] Cambio de contraseña
- [ ] Recuperación de contraseña (forgot password)
- [ ] Verificación de email
- [ ] Two-Factor Authentication (2FA)
- [ ] Historial completo de accesos con filtros
- [ ] Exportar QR en diferentes formatos
- [ ] Notificaciones push
- [ ] Actualización de datos personales

## 👨‍💻 Buenas Prácticas Implementadas

### Laravel
✅ Request validation separada
✅ Resource controllers
✅ Eloquent ORM
✅ Named routes
✅ Middleware apropiado
✅ Service layer (PersonaService)
✅ Eager loading (N+1 prevention)
✅ Log auditing

### Vue.js
✅ Composition API
✅ Component reusability
✅ Props validation
✅ Computed properties
✅ Reactive state management
✅ Event handling
✅ Conditional rendering

### Inertia.js
✅ Shared data
✅ Proper redirects
✅ Form handling
✅ Error handling
✅ Progress indicators

## 📞 Soporte

Para soporte o dudas sobre el sistema:
- Revisar logs: `storage/logs/laravel.log`
- Verificar configuración: `config/auth.php`
- Validar rutas: `php artisan route:list | grep personas`
- Ver migraciones: `php artisan migrate:status`

---

**Versión:** 2.0  
**Framework:** Laravel 11 + Vue 3 + Inertia.js  
**Autor:** Sistema CTAccess  
**Fecha:** Octubre 2025
