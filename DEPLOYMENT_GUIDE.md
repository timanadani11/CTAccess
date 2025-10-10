# 🚀 GUÍA DE DESPLIEGUE - CTAccess PWA

## 📋 OPCIÓN 1: HOSTING COMPARTIDO (cPanel) - MÁS COMÚN

### Requisitos:
- PHP 8.1 o superior
- MySQL/MariaDB
- Acceso FTP (FileZilla)
- cPanel (opcional pero ayuda)

### Pasos:

#### 1. Preparar Archivos Localmente
```bash
# En tu PC:
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 2. Estructura en el Servidor
```
public_html/
├── tusubdominio/       (o carpeta raíz)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/         ← IMPORTANTE: Apuntar dominio aquí
│   │   ├── build/      ← Archivos PWA
│   │   ├── icons/
│   │   ├── index.php
│   │   └── .htaccess
│   ├── resources/
│   ├── routes/
│   ├── storage/        ← Dar permisos 755
│   ├── vendor/         ← Subir o instalar con composer
│   ├── .env            ← Configurar para producción
│   ├── artisan
│   └── composer.json
```

#### 3. Configurar el Dominio
En cPanel:
- Domains → Manage
- Apuntar el subdominio a: `/public_html/tusubdominio/public`
- NO a `/public_html/tusubdominio`

#### 4. Subir Archivos con FileZilla
```
Conexión:
- Host: ftp.tudominio.com
- Usuario: tu_usuario
- Contraseña: tu_contraseña
- Puerto: 21

Subir:
✅ TODO excepto: node_modules/, .git/, tests/
```

#### 5. Configurar .env en el Servidor
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tusubdominio.com
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_contraseña_db
```

#### 6. Permisos (muy importante)
```bash
# Via SSH (si tienes acceso) o File Manager cPanel:
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### 7. Generar APP_KEY
```bash
# Si tienes SSH:
php artisan key:generate

# Si NO tienes SSH:
# Ve a https://generate-random.org/laravel-key-generator
# Copia el key y ponlo en .env
```

#### 8. Migraciones
```bash
# Si tienes SSH:
php artisan migrate --force

# Si NO tienes SSH:
# Sube el database.sqlite o usa phpMyAdmin
```

---

## 📋 OPCIÓN 2: VPS CON SSH (Mejor opción)

### Pasos Rápidos:

```bash
# 1. Conectar por SSH
ssh usuario@tuservidor.com

# 2. Clonar o subir proyecto
cd /var/www/
git clone tu-repositorio ctaccess
# O sube por FTP/SFTP

# 3. Instalar dependencias
cd ctaccess
composer install --optimize-autoloader --no-dev
npm run build

# 4. Configurar
cp .env.example .env
nano .env  # Editar configuración
php artisan key:generate

# 5. Permisos
chmod -R 755 storage bootstrap/cache

# 6. Nginx/Apache
# Apuntar a /var/www/ctaccess/public

# 7. Migraciones
php artisan migrate --force
```

---

## 📋 OPCIÓN 3: HOSTING ESPECÍFICO LARAVEL (Forge, Vapor, etc.)

- Más fácil, todo automatizado
- Solo conectas repo Git
- Deploy automático

---

## ⚠️ IMPORTANTE PARA PWA:

1. **HTTPS es obligatorio** ✅ (ya lo tienes con subdominio)
2. **Verificar rutas en .htaccess:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

3. **Verificar que public/build/ sea accesible**
```
https://tusubdominio.com/build/manifest.webmanifest
https://tusubdominio.com/build/sw.js
https://tusubdominio.com/icons/icon-192x192.png
```

---

## 🎯 DESPUÉS DEL DEPLOY:

1. **Probar desde celular:**
   ```
   https://tusubdominio.com
   ```

2. **Instalar PWA:**
   - Android: Menú → "Instalar aplicación"
   - iOS: Compartir → "Añadir a pantalla"

3. **Verificar en DevTools:**
   - F12 → Application → Manifest ✅
   - F12 → Application → Service Workers ✅

---

## 🆘 SOLUCIÓN DE PROBLEMAS:

### Error 500
- Revisar storage/logs/laravel.log
- Verificar permisos 755 en storage/
- Verificar APP_KEY en .env

### Rutas no funcionan
- Verificar mod_rewrite habilitado
- Verificar .htaccess en public/
- Apuntar dominio a /public (no raíz)

### PWA no instala
- Verificar HTTPS ✅
- Verificar manifest: /build/manifest.webmanifest
- Verificar SW: /build/sw.js
- Limpiar caché del navegador

---

## 📞 INFORMACIÓN NECESARIA DE TU HOSTING:

1. ¿Tienes acceso SSH?
2. ¿Versión de PHP?
3. ¿Tienes composer en el servidor?
4. ¿Puedes ejecutar comandos artisan?
5. ¿Qué panel de control? (cPanel, Plesk, otro)
