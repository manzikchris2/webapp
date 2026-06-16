FROM php:8.2-apache

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (better for caching)
COPY composer.json composer.lock* ./

# Install dependencies (REMOVED the error hiding)
RUN composer install --no-interaction --no-progress --no-suggest

# Copy all files
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/vendor

CMD ["apache2-foreground"]