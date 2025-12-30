FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    curl \
    gnupg \
    unixodbc \
    unixodbc-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Microsoft SQL Server drivers + PHP extensions
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/apt/keyrings/microsoft.gpg \
    && echo "deb [arch=amd64 signed-by=/etc/apt/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && rm -rf /var/lib/apt/lists/* /etc/apt/sources.list.d/mssql-release.list /etc/apt/keyrings/microsoft.gpg

# Install Composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies
COPY composer.json ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts

# Copy the rest of the application
COPY . .
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-progress \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
