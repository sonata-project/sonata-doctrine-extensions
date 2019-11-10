#!/usr/bin/env bash
set -ev

PHP_INI_DIR="$HOME/.phpenv/versions/$(phpenv version-name)/etc/conf.d/"
TRAVIS_INI_FILE="$PHP_INI_DIR/travis.ini"
echo "memory_limit=3072M" >> "$TRAVIS_INI_FILE"

echo "extension=mongodb.so" >> "$TRAVIS_INI_FILE"

# https://github.com/composer/composer/issues/5030
composer config "platform.ext-mongo" "1.6.16"
# Backwards compatibility with old mongo extension
composer require "alcaeus/mongo-php-adapter"

sed --in-place "s/\"dev-master\":/\"dev-${TRAVIS_COMMIT}\":/" composer.json

