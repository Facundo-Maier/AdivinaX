FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

RUN printf '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' \
    > /etc/apache2/conf-available/adivinatwit.conf \
    && a2enconf adivinatwit

COPY . /var/www/html/