#!/bin/bash
# Script de corrección de permisos de Apache
# Se ejecuta dentro del contenedor si es necesario

echo "Corrigiendo permisos de Apache..."

# Asegurar que el directorio raíz tenga permisos correctos
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Verificar que mod_rewrite esté habilitado
a2enmod rewrite 2>/dev/null || true

# Verificar configuración de Apache
apache2ctl configtest 2>&1 || true

echo "Corrección completada."
echo "Puedes acceder a: http://localhost:8080/"
