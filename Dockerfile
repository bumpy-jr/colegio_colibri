
FROM php:8.3-apache

# Instalar dependencias necesarias para mbstring
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libonig-dev \
        pkg-config \
    && a2enmod rewrite headers \
    && docker-php-ext-install mbstring \
    && apt-get purge -y --auto-remove pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Configuración de PHP
RUN printf "date.timezone=America/Bogota\nexpose_php=Off\ndisplay_errors=Off\nlog_errors=On\nerror_log=/dev/stderr\n" \
    > /usr/local/etc/php/conf.d/colegio.ini

# Configuración del directorio público
ENV APACHE_DOCUMENT_ROOT=/var/www/html/publico

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Copiar proyecto
COPY . /var/www/html/

# Permisos de archivos
RUN mkdir -p /var/www/html/datos \
    && chown -R www-data:www-data /var/www/html/datos \
    && chmod 770 /var/www/html/datos \
    && chown -R root:root /var/www/html/includes /var/www/html/publico \
    && chmod -R 755 /var/www/html/includes /var/www/html/publico

EXPOSE 80

CMD ["apache2-foreground"]
