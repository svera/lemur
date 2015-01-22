FROM php:5.4-apache
ENV LEMUR_ENV prod
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN apt-get update && apt-get install -y zlib1g-dev git
RUN pecl install mongo
RUN pecl install zip
RUN mkdir /var/www/lemur
RUN mkdir -p -m 777 /var/cache/doctrine/odm/mongodb/Proxy
RUN mkdir -p -m 777 /var/cache/doctrine/odm/mongodb/Hydrator
ADD app /var/www/lemur
ADD apache2.conf /etc/apache2/apache2.conf
ADD prod-build/php.ini /usr/local/etc/php/php.ini
WORKDIR /var/www/lemur
RUN composer install --no-dev
