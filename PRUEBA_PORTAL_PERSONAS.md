# Prueba del Sistema de Autenticación de Personas

## ✅ Pasos para Probar el Sistema

### 1. Preparar Base de Datos
```bash
php artisan migrate:fresh --seed
```

### 2. Compilar Assets
```bash
npm run build
# o para desarrollo:
npm run dev
```

### 3. Iniciar Servidor
```bash
php artisan serve
```

### 4. Probar Login

#### Acceder a la vista de login:
```
http://localhost:8000/personas/login
```

#### Credenciales de Prueba:

**Persona 1 - Empleado**
- Correo: `juan@empresa.com`
- Contraseña: `password123`

**Persona 2 - Visitante**
- Correo: `maria@visitante.com`
- Contraseña: `password123`

**Persona 3 - Contratista**
- Correo: `carlos@contratista.com`
- Contraseña: `password123`

**Persona 4 - Empleado**
- Correo: `ana@empresa.com`
- Contraseña: `password123`

### 5. Verificar Flujo Completo

#### ✅ Login Exitoso
1. Ir a `/personas/login`
2. Ingresar correo y contraseña
3. Hacer clic en "Iniciar Sesión"
4. **Resultado Esperado**: Redirect a `/personas/home`
5. Verificar que aparece mensaje de bienvenida con el nombre

#### ✅ Home/Dashboard
Después del login, verificar:
- ✅ Mensaje de bienvenida personalizado
- ✅ Información personal visible
- ✅ Estadísticas de accesos
- ✅ Lista de portátiles (si tiene)
- ✅ Lista de vehículos (si tiene)
- ✅ Historial de accesos recientes

#### ✅ Perfil
1. Hacer clic en el menú de usuario (arriba derecha)
2. Seleccionar "Mi Perfil"
3. **Resultado Esperado**: Ver página de perfil
4. Verificar:
   - ✅ QR Code visible
   - ✅ Información completa
   - ✅ Portátiles registrados
   - ✅ Vehículos registrados

#### ✅ Logout
1. Hacer clic en el menú de usuario
2. Seleccionar "Cerrar Sesión"
3. **Resultado Esperado**: Redirect a página home pública
4. Verificar que no puede acceder a `/personas/home` sin autenticarse

### 6. Probar Casos de Error

#### ❌ Login con Credenciales Incorrectas
1. Ir a `/personas/login`
2. Ingresar correo: `test@test.com`
3. Ingresar contraseña: `wrongpassword`
4. Hacer clic en "Iniciar Sesión"
5. **Resultado Esperado**: Mensaje de error "Las credenciales proporcionadas son incorrectas"

#### ❌ Rate Limiting
1. Intentar login con credenciales incorrectas 5 veces seguidas
2. **Resultado Esperado**: Mensaje de "Demasiados intentos"
3. Esperar 1 minuto para poder intentar de nuevo

#### ❌ Acceso sin Autenticación
1. Cerrar sesión si está autenticado
2. Intentar acceder directamente a: `http://localhost:8000/personas/home`
3. **Resultado Esperado**: Redirect automático a `/personas/login`

### 7. Verificar Responsive Design

#### Desktop (>1024px)
- ✅ Navegación horizontal
- ✅ Menú de usuario con dropdown
- ✅ Grid layout en dashboard

#### Tablet (768px - 1024px)
- ✅ Navegación adaptada
- ✅ Grid responsive

#### Mobile (<768px)
- ✅ Menú hamburguesa
- ✅ Layout vertical
- ✅ Touch-friendly buttons

### 8. Verificar Dark Mode
1. Hacer clic en el botón de tema (sol/luna)
2. **Resultado Esperado**: Cambio inmediato de tema
3. Verificar que el tema persiste al recargar página

### 9. Verificar Logs de Auditoría

```bash
tail -f storage/logs/laravel.log
```

Después de hacer login/logout, verificar que aparecen entradas como:
```
[INFO] Persona autenticada {persona_id: 1, nombre: "Juan Pérez", ip: "127.0.0.1"}
[INFO] Persona cerró sesión {persona_id: 1, nombre: "Juan Pérez"}
```

## 🐛 Troubleshooting

### Problema: "CSRF token mismatch"
**Solución:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Problema: "Target class does not exist"
**Solución:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Problema: "Vite manifest not found"
**Solución:**
```bash
npm run build
```

### Problema: Error 500 al hacer login
**Verificar:**
1. Revisar `storage/logs/laravel.log`
2. Verificar que la tabla `personas` tiene registros con contraseñas hasheadas
3. Ejecutar: `php artisan migrate:fresh --seed`

### Problema: No redirige después del login
**Verificar:**
1. Que las rutas estén bien definidas en `routes/web.php`
2. Ejecutar: `php artisan route:list | grep personas`

## ✨ Características a Verificar

### Seguridad
- [x] Contraseñas hasheadas (bcrypt)
- [x] CSRF protection
- [x] Rate limiting (5 intentos/minuto)
- [x] Session regeneration en login
- [x] Session invalidation en logout
- [x] Logs de auditoría

### UX/UI
- [x] Animaciones fluidas
- [x] Loading states
- [x] Error messages claros
- [x] Dark mode
- [x] Responsive design
- [x] Iconos consistentes

### Funcionalidad
- [x] Login funcional
- [x] Logout funcional
- [x] Remember me
- [x] Dashboard con datos reales
- [x] Perfil con QR code
- [x] Navegación entre páginas

## 📊 Datos de Base de Datos

Después de ejecutar `php artisan migrate:fresh --seed`, tendrás:

### Personas Creadas
- 4 personas con contraseñas hasheadas
- Cada una con correo y contraseña `password123`
- Diferentes tipos: Empleado, Visitante, Contratista
- QR Codes generados

### Verificar en Base de Datos
```sql
-- Ver personas
SELECT idPersona, Nombre, correo, TipoPersona FROM personas;

-- Verificar que contraseñas están hasheadas
SELECT idPersona, Nombre, LENGTH(contraseña) as hash_length FROM personas;
-- El hash debe tener 60 caracteres (bcrypt)
```

## 🎯 Checklist Final

Antes de considerar completa la implementación:

- [ ] Login funciona correctamente
- [ ] Redirect a home después de login exitoso
- [ ] Home muestra datos personalizados
- [ ] Perfil muestra QR code
- [ ] Logout funciona correctamente
- [ ] Rate limiting protege contra ataques
- [ ] CSRF protection activo
- [ ] Logs de auditoría se registran
- [ ] Dark mode funciona
- [ ] Responsive en mobile
- [ ] No hay errores en consola del navegador
- [ ] No hay errores en logs de Laravel

## 📝 Notas Importantes

1. **Seguridad**: Las contraseñas de prueba son simples (`password123`). En producción, usar contraseñas fuertes.

2. **Guards**: El sistema usa el guard `web` para personas, separado del guard `system` para usuarios del sistema.

3. **Middleware**: Las rutas protegidas usan `auth:web` para verificar autenticación.

4. **Inertia.js**: Los datos se pasan como props desde el controller a la vista Vue.

5. **Session**: Por defecto, Laravel usa session storage en archivos. En producción, considerar Redis.

---

**Documento de Prueba**  
**Sistema:** CTAccess Portal de Personas  
**Versión:** 2.0  
**Fecha:** Octubre 2025
