#!/bin/bash
set -e

echo "=== Iniciando container PHP ==="

# Exibir configurações do PHP e Xdebug
echo "PHP Configurations:"
php -i | grep "php.ini"

echo "Xdebug Configurations:"
php -m | grep xdebug || echo "Xdebug não está ativado"

rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Garantir que apenas um processo do PHP-FPM seja iniciado corretamente
exec php-fpm -F
