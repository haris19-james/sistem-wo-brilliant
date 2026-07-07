#!/bin/sh
set -e

if command -v php >/dev/null 2>&1; then
  PHP_BIN=php
elif command -v php8.2 >/dev/null 2>&1; then
  PHP_BIN=php8.2
elif command -v php8.1 >/dev/null 2>&1; then
  PHP_BIN=php8.1
else
  echo "PHP executable not found in Vercel build environment" >&2
  exit 127
fi

echo "Using PHP binary: $PHP_BIN"

npm run build

curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
"$PHP_BIN" /tmp/composer-setup.php --install-dir=/tmp --filename=composer
"$PHP_BIN" /tmp/composer install --no-dev --optimize-autoloader --no-interaction
