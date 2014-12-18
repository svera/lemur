FROM php:5.4-apache
ENV LEMUR_ENV devel
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN apt-get update && apt-get install -y zlib1g-dev git
RUN pecl install mongo
RUN pecl install zip
RUN echo "extension=mongo.so\nextension=zip.so" >> /usr/local/etc/php/php.ini
RUN mkdir /var/www/lemur
ADD ./app /var/www/lemur
COPY apache2.conf /etc/apache2/apache2.conf
WORKDIR /var/www/lemur
RUN composer install
RUN ln -s /code/vendor/phpunit/phpunit/phpunit /usr/local/bin/phpunit