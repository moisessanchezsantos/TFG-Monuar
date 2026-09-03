FROM php:8.4-apache

# Extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
        libicu-dev \
        libzip-dev \
        zip \
        unzip \
        default-mysql-client \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        intl \
        zip \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Activar mod_rewrite de Apache
RUN a2enmod rewrite

# Apuntar DocumentRoot a /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
        /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' \
        >> /etc/apache2/sites-available/000-default.conf

# Script de arranque
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
