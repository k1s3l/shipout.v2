FROM php:7.4-fpm

RUN apt update && apt install -y libfreetype6-dev sendmail libpng-dev wget g++ git libzip-dev libjpeg-dev  libjpeg62-turbo-dev libicu63 libicu-dev firebird-dev libmagick++-dev gcc musl-dev make pear-channels supervisor cron libc6 libpq-dev

RUN docker-php-ext-configure gd

RUN docker-php-ext-install \
        gd \
        pdo \
        pdo_pgsql \
        tokenizer \
        opcache \
        exif \
        intl \
        zip

RUN pecl install imagick
RUN docker-php-ext-enable imagick

RUN docker-php-ext-install opcache

ADD php.ini /usr/local/etc/

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet
