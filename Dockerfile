FROM php:8.4-apache
WORKDIR /var/www/html

RUN docker-php-ext-install pdo

RUN docker-php-ext-install pdo_mysql

RUN apt-get update && apt-get install -y libicu-dev && docker-php-ext-install intl

RUN a2enmod rewrite

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 80
CMD ["apache2-foreground"]