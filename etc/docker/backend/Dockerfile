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

RUN mv ~/.symfony5/bin/symfony /usr/local/bin/symfony

COPY enter /usr/local/bin/enter

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["/usr/local/bin/enter"]