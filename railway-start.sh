#!/bin/bash

echo "🚀 Iniciando despliegue en Railway..."

# Instalar dependencias de PHP si no están instaladas
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Instalar dependencias de Node.js si no están instaladas
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias de NPM..."
    npm ci
fi

# Limpiar caché
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Crear el symlink de storage
echo "🔗 Creando symlink de storage..."
php artisan storage:link

# Construir assets de Vite
echo "🏗️ Construyendo assets de Vite..."
npm run build

# Optimizar Laravel para producción
echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Iniciar servidor web
echo "✅ Iniciando servidor web..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
