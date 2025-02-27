# Usa a imagem oficial do PHP com FPM
FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    wkhtmltopdf \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql gd zip opcache

# Instalar Xdebug para cobertura de código
RUN pecl install xdebug \
    && echo "zend_extension=xdebug" > /usr/local/etc/php/conf.d/xdebug.ini

# Copiar configurações do PHP
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do Symfony
RUN composer install --no-scripts --no-autoloader --no-interaction

# Instalar PHPUnit e Symfony PHPUnit Bridge
RUN composer require --dev phpunit/phpunit symfony/phpunit-bridge

# Criar diretório para cobertura de código
RUN mkdir -p /var/www/html/var/coverage && chmod -R 777 /var/www/html/var/coverage

# Definir permissões corretas
RUN chown -R www-data:www-data /var/www/html

# Copiar e aplicar entrypoint
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expor porta do PHP-FPM
EXPOSE 9000

# Definir o entrypoint
ENTRYPOINT ["/entrypoint.sh"]
