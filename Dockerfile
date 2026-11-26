FROM php:8.2-apache

# Install GD extension dependencies (libpng, libjpeg, libwebp, libfreetype)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

# Enable Apache modules for .htaccess security and clean routing
RUN a2enmod rewrite headers

# Configure Apache to allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy application files
COPY . /var/www/html/

# Configure permissions for temporary image storage
RUN chown -R www-data:www-data /var/www/html/storage/temp \
    && chmod -R 775 /var/www/html/storage/temp

EXPOSE 80
