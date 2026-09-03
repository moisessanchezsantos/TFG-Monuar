#!/bin/bash
set -e

# ── Esperar a MySQL ──────────────────────────────────────────────────────────
echo "[monar] Esperando a MySQL en ${DB_HOST}..."
until php -r "
    try {
        new PDO('mysql:host=${DB_HOST};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    sleep 2
    echo "[monar] MySQL no disponible, reintentando..."
done
echo "[monar] MySQL listo."

# ── Dependencias PHP ─────────────────────────────────────────────────────────
if [ ! -f "vendor/autoload.php" ]; then
    echo "[monar] Ejecutando composer install..."
    composer install --no-interaction --no-progress --optimize-autoloader
fi

# ── .env para Symfony (solo lo necesita doctrine:migrations) ─────────────────
if [ ! -f ".env" ]; then
    echo "APP_ENV=prod" > .env
    echo "APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')" >> .env
fi
echo "DATABASE_URL=mysql://${DB_USER}:${DB_PASS}@${DB_HOST}:3306/${DB_NAME}?serverVersion=8.0&charset=utf8mb4" >> .env

# ── Migraciones ───────────────────────────────────────────────────────────────
echo "[monar] Aplicando migraciones..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# ── Datos de prueba (solo si la tabla usuario está vacía) ─────────────────────
ROW_COUNT=$(php -r "
    \$pdo = new PDO('mysql:host=${DB_HOST};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
    echo \$pdo->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
")

if [ "$ROW_COUNT" = "0" ]; then
    echo "[monar] Cargando datos de prueba..."
    mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" \
        < /var/www/database/init_test_data.sql 2>/dev/null || true
    echo "[monar] Datos de prueba cargados."
fi

# ── Permisos ──────────────────────────────────────────────────────────────────
mkdir -p var/cache var/log public/uploads/photos
chown -R www-data:www-data var public/uploads

echo "[monar] Iniciando Apache..."
exec "$@"
