FROM php:7.1-apache

COPY . /var/www

RUN apt-get update \
	&& apt-get install libpcre3 libpcre3-dev \
	&& pecl install oauth-2.0.2 \
	&& docker-php-ext-enable oauth \
	&& docker-php-ext-install pdo pdo_mysql
