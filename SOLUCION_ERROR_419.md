# 🔧 Solución: Error 419 PAGE EXPIRED

## 🚨 Problema
Aparece el error **419 | PAGE EXPIRED** cada vez que navegas en la aplicación.

## 🎯 Causa
El problema ocurre porque:
1. **Desajuste de dominio**: Accedes con `127.0.0.1:8000` pero `APP_URL` está configurado como `localhost`
2. **Cookies de sesión**: Las cookies de sesión no se comparten entre `localhost` y `127.0.0.1`
3. **CSRF Token**: El token expira porque la sesión no se mantiene

## ✅ Solución Inmediata (Recomendada)

### **Opción 1: Usa la URL correcta en el navegador**

En tu navegador, **cambia de:**
```
http://127.0.0.1:8000/system/login
```

**A:**
```
http://localhost:8000/system/login
```

✅ Cierra todas las pestañas del navegador  
✅ Abre una nueva y usa `localhost` en lugar de `127.0.0.1`  
✅ El problema desaparecerá inmediatamente

---

## 🔧 Solución Permanente (Configuración)

Si prefieres seguir usando `127.0.0.1`, modifica estos archivos:

### 1. Actualizar `.env`
```env
# Cambiar de:
APP_URL=http://localhost

# A:
APP_URL=http://127.0.0.1:8000

# Y agregar después de SESSION_DOMAIN:
SESSION_DOMAIN=127.0.0.1
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000,localhost:8000
```

### 2. Limpiar caché
```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

### 3. Reiniciar el servidor
```bash
# Detener con Ctrl+C y volver a iniciar:
php artisan serve --host=127.0.0.1 --port=8000
```

---

## 🔍 Verificación

### Comprobar que funciona:
1. Cierra todas las pestañas del navegador
2. Abre `http://127.0.0.1:8000/system/login` (o `localhost:8000`)
3. Inicia sesión
4. Navega por diferentes secciones
5. ✅ **No debería aparecer el error 419**

### Si persiste el problema:
```bash
# Ejecutar en terminal:
php artisan tinker

# Y dentro de tinker:
DB::table('sessions')->delete();
exit
```

---

## 📚 Explicación Técnica

### ¿Por qué pasa esto?

**Cookies de sesión:**
- Laravel usa cookies para mantener la sesión activa
- Las cookies se vinculan a un dominio específico
- `localhost` y `127.0.0.1` son dominios DIFERENTES para el navegador
- Si la cookie se crea en `localhost`, no funciona en `127.0.0.1`

**Token CSRF:**
- Se almacena en la sesión
- Si la sesión expira o no se encuentra, el token no es válido
- Resultado: Error 419

### Configuración actual:
- ✅ `SESSION_DRIVER=database` - Sesiones en base de datos
- ✅ `SESSION_LIFETIME=480` - 8 horas de duración
- ✅ Token CSRF configurado en `app.blade.php`
- ✅ Axios configurado con CSRF token en `bootstrap.js`

---

## ⚡ Resumen

**La solución más rápida:**
```
Usa http://localhost:8000 en lugar de http://127.0.0.1:8000
```

**Si quieres usar 127.0.0.1:**
1. Actualiza `APP_URL` en `.env` a `http://127.0.0.1:8000`
2. Agrega `SESSION_DOMAIN=127.0.0.1`
3. Ejecuta `php artisan config:clear`
4. Reinicia el servidor

---

## 🎉 Resultado Esperado

✅ No más errores 419  
✅ Sesión se mantiene activa durante 8 horas  
✅ Navegación fluida por toda la aplicación  
✅ CSRF tokens funcionando correctamente

---

**Fecha de creación:** 14/10/2025  
**Última actualización:** 14/10/2025
