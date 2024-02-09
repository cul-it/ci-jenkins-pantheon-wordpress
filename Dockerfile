docker pull arm64v8/php
FROM arm64v8/php:8.1-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install unzip and the PHP zip extension
RUN apt-get update && \
    apt-get install -y \
    unzip \
    libzip-dev && \
    docker-php-ext-install zip

RUN apt-get -y update \
&& apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

# Install git
RUN apt-get update && \
    apt-get install -y git && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Install dependencies
RUN composer install
