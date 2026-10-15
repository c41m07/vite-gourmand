FROM php:8.4.15-apache

RUN a2enmod rewrite

RUN apt update -y && apt upgrade -y && apt install -y sudo tzdata libzip-dev zip libicu-dev libpng-dev libxslt-dev\
    && apt clean

# Configurer timezone
ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Installer Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls
RUN mv composer.phar /usr/local/bin/composer

# Installer Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash && sudo apt install symfony-cli -y

# Install npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
RUN sudo apt-get install -y nodejs

RUN docker-php-ext-install pdo_mysql zip gd xsl

RUN docker-php-ext-configure intl && docker-php-ext-install intl

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/html/
CMD ["apache2-foreground"]