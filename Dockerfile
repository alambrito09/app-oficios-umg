FROM php:8.2-apache

# Instalar extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    gd \
    soap \
    && a2enmod rewrite

# Copiar configuración personalizada de Apache con permisos de acceso
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar script de corrección de permisos
COPY docker/fix-apache.sh /usr/local/bin/fix-apache.sh
RUN chmod +x /usr/local/bin/fix-apache.sh

# Establecer ServerName para eliminar warning
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar el código de la aplicación
COPY . /var/www/html/

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
