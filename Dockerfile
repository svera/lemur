FROM php:5.4-cli
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN pecl install mongo
RUN pecl install zip
RUN echo "extension=mongo.so\nextension=zip.so" > /usr/local/lib/php.ini
ADD . /code
WORKDIR /code
RUN composer install
