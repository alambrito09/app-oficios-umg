#!/bin/bash
# Espera a que MySQL esté disponible antes de continuar
set -e

echo "Esperando a que MySQL esté disponible..."
until mysqladmin ping -h "db" -u "${DB_USER}" -p"${DB_PASSWORD}" --silent 2>/dev/null; do
  echo "MySQL no está listo aún... reintentando en 2s"
  sleep 2
done

echo "MySQL está disponible. Importando esquema si es necesario..."
# El esquema se importa automáticamente por MySQL mediante /docker-entrypoint-initdb.d/
# Este script es un punto de extensión para futuras migraciones o seeds

echo "Inicialización de BD completada."
