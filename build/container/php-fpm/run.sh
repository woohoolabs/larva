#!/bin/sh
set -e

/code/build/container/php-fpm/schema.sh

php-fpm
