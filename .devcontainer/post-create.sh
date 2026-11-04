#!/usr/bin/env bash
# Préparation du projet après la création du Codespace.
# IDEMPOTENT : peut être relancé à tout moment (bash .devcontainer/post-create.sh).
set -e
umask 0002
cd /workspace/projetLaravel

echo "▶ Dépendances PHP (composer install)…"
composer install --no-interaction --prefer-dist --no-progress

if [ ! -f .env ]; then
  echo "▶ Création du fichier .env"
  cp .env.example .env
fi
if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --no-interaction
fi

echo "▶ Attente du serveur MySQL…"
for i in $(seq 1 60); do
  if php -r 'new PDO("mysql:host=mysql;port=3306", "root", "root");' 2>/dev/null; then break; fi
  sleep 2
done

echo "▶ Création des tables, des données et démarrage de l'exercice…"
php artisan exercice:demarrer

# Apache (www-data) doit pouvoir écrire dans storage/ et bootstrap/cache/
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod g+s {} +

chmod +x /workspace/lancer.sh

echo ""
echo "✅ Le centre de supervision est prêt."
bash /workspace/.devcontainer/bienvenue.sh
