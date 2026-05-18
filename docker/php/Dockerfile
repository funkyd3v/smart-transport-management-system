FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    mariadb-client \
    unzip \
    zip \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        zip \
    && apk del --no-network $PHPIZE_DEPS \
    && rm -rf /tmp/* /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install opcache
RUN docker-php-ext-install -j"$(nproc)" opcache

# Install Redis PECL extension
RUN apk add --no-cache $PHPIZE_DEPS && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    apk del --no-network $PHPIZE_DEPS && \
    rm -rf /tmp/* /var/cache/apk/*

# Copy PHP configuration
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/php.ini

# Create non-root user
RUN addgroup -g 1000 laravel && adduser -D -u 1000 -G laravel laravel

WORKDIR /var/www/html

# Copy application code
COPY --chown=laravel:laravel . /var/www/html

    # Set proper permissions
    RUN chown -R laravel:laravel /var/www/html

    # Switch to non-root user
    USER laravel

    # Health check for PHP-FPM
    HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
        CMD php -v || exit 1
