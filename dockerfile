FROM php:8.2-apache

# Install dependencies and PHP extensions in one RUN
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy and install dependencies
COPY composer.json ./
RUN composer install --no-interaction 2>/dev/null || true

# Copy all files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]