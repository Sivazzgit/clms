FROM --platform=linux/amd64 php:8.2.31-apache

# Enable Apache modules
RUN a2enmod ssl rewrite headers

# Install system packages & PHP extensions
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    netcat-openbsd \
    gzip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

# Create directory for SSL certificates
RUN mkdir -p /etc/apache2/certs

# Copy Apache configuration files
COPY docker/apache-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Enable SSL site
RUN a2ensite default-ssl.conf

# Set document root configuration via sed
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy entrypoint script
COPY docker/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/labour.ini

# Set working directory
WORKDIR /var/www/html

# Expose ports
EXPOSE 80
EXPOSE 5000

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
