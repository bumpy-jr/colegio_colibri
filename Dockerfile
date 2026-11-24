FROM php:8.3-apache

# Extensión para MySQL + módulos de Apache + ajustes de PHP
RUN docker-php-ext-install pdo_mysql \
 && a2enmod rewrite headers \
 && printf "date.timezone=America/Bogota\nexpose_php=Off\ndisplay_errors=Off\nlog_errors=On\nerror_log=/dev/stderr\n" > /usr/local/etc/php/conf.d/app.ini \
 && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

# La carpeta pública es la raíz web; includes/, scripts/ y database/ quedan fuera
ENV APACHE_DOCUMENT_ROOT=/var/www/html/publico
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Render inyecta PORT en tiempo de ejecución (por defecto 10000)
ENV PORT=10000
RUN sed -ri 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
 && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 10000

# Crea las tablas si no existen y arranca Apache (si la BD falla, el sitio igual arranca)
CMD ["sh", "-c", "php /var/www/html/scripts/migrar.php; exec apache2-foreground"]
