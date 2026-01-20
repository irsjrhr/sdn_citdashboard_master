# ===============================
# Base Image
# ===============================
FROM php:8.3-apache

# ===============================
# Install system dependencies
# ===============================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libldap2-dev \
    zip \
    curl \
    gnupg \
    ca-certificates \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# Install SQL Server ODBC drivers
# ===============================
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc > /etc/apt/trusted.gpg.d/microsoft.asc \
    && echo "deb [arch=amd64] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y \
        unixodbc \
        unixodbc-dev \
        msodbcsql18 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# Install PHP extensions including SQL Server
# ===============================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure ldap \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
        ldap \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# ===============================
# Enable Apache Rewrite
# ===============================
RUN a2enmod rewrite

# ===============================
# Set working directory
# ===============================
WORKDIR /var/www/html

# ===============================
# Install Composer
# ===============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ===============================
# Copy ALL project files (pastikan helper ikut)
# ===============================
COPY . .

# ===============================
# Fix git safe directory (opsional tapi aman)
# ===============================
RUN git config --global --add safe.directory /var/www/html

# ===============================
# Install Laravel dependencies
# ===============================
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ===============================
# Permissions for Laravel
# ===============================
RUN chown -R www-data:www-data storage bootstrap/cache

# ===============================
# Apache config for Laravel
# ===============================
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# ===============================
# Expose port
# ===============================
EXPOSE 80

# ===============================
# Start Apache
# ===============================
CMD ["apache2-foreground"]