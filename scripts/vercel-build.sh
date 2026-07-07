#!/bin/sh
set -e

PHP_BIN=""
for candidate in php8.3 php83 php8.2 php82 php8.1 php81 php; do
  if command -v "$candidate" >/dev/null 2>&1; then
    PHP_BIN="$candidate"
    break
  fi
done

if [ -z "$PHP_BIN" ]; then
  echo "PHP executable not found in Vercel build environment" >&2
  exit 127
fi

echo "Using PHP binary: $PHP_BIN"

npm run build

curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
"$PHP_BIN" /tmp/composer-setup.php --install-dir=/tmp --filename=composer
"$PHP_BIN" /tmp/composer install --no-dev --optimize-autoloader --no-interaction
