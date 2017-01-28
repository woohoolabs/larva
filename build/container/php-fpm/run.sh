#!/bin/sh
set -e

cp /code/build/container/php-fpm/custom-php.ini /usr/local/etc/php/conf.d/zz-custom-php.ini

/code/build/container/php-fpm/schema.sh

php-fpm
