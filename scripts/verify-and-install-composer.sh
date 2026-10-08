#!/usr/bin/env bash
set -euo pipefail

# Pinned Composer 2.2.30 installer and phar checksums (verified at suite setup).
EXPECTED_INSTALLER_SHA384='c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47'
EXPECTED_COMPOSER_PHAR_SHA256='8c2b4478b64f8f7cdf1574838fdb0033b29049ca821dad452db7a3dcfcdbffc2'
COMPOSER_VERSION='2.2.30'

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "copy('https://composer.github.io/installer.sig', 'composer-setup.sig');"
ACTUAL_INSTALLER_SHA384="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
REMOTE_SIG="$(php -r "echo trim(file_get_contents('composer-setup.sig'));")"

if [ "$ACTUAL_INSTALLER_SHA384" != "$EXPECTED_INSTALLER_SHA384" ]; then
  echo "Installer SHA-384 mismatch (local hash vs pinned literal)" >&2
  exit 1
fi
if [ "$ACTUAL_INSTALLER_SHA384" != "$REMOTE_SIG" ]; then
  echo "Installer SHA-384 mismatch (local hash vs installer.sig)" >&2
  exit 1
fi

php composer-setup.php --version="${COMPOSER_VERSION}" --filename=composer.phar
rm -f composer-setup.php composer-setup.sig

ACTUAL_PHAR_SHA256="$(sha256sum composer.phar | awk '{print $1}')"
if [ "$ACTUAL_PHAR_SHA256" != "$EXPECTED_COMPOSER_PHAR_SHA256" ]; then
  echo "composer.phar SHA-256 mismatch" >&2
  exit 1
fi

php composer.phar install --no-interaction --prefer-dist
