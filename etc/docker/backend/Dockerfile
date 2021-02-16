FROM php:7.4

RUN apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
		git \
        unzip \
        libicu-dev \
        zlib1g-dev \
        libssl-dev \
        pkg-config \
        libpq-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN set -xe \
    && docker-php-ext-configure \
        intl \
    && docker-php-ext-install \
        intl \
        opcache \
        zip \
        pdo \
        pdo_mysql \
        pdo_pgsql

COPY php.ini /usr/local/etc/php/php.ini

RUN curl https://get.symfony.com/cli/installer | bash

RUN mv ~/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /app

CMD ["symfony","serve","--dir=.","--no-tls","--allow-http","--document-root=public/","--port=8088","-q"]