FROM php:8.3-apache

# El proyecto utiliza CSV; no requiere motor de base de datos ni extensiones PDO.
RUN a2enmod rewrite headers \
    && docker-php-ext-install mbstring \
    && printf "date.timezone=America/Bogota\nexpose_php=Off\ndisplay_errors=Off\nlog_errors=On\nerror_log=/dev/stderr\n" > /usr/local/etc/php/conf.d/colegio.ini

# Apache publica exclusivamente /publico; includes/ y datos/ quedan fuera del DocumentRoot.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/publico
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . /var/www/html/
RUN mkdir -p /var/www/html/datos \
    && chown -R www-data:www-data /var/www/html/datos \
    && chmod 770 /var/www/html/datos \
    && chown -R root:root /var/www/html/includes /var/www/html/publico \
    && chmod -R 755 /var/www/html/includes /var/www/html/publico

EXPOSE 80
CMD ["apache2-foreground"]
