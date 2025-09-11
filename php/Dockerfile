FROM php:8.1-fpm

# Add build arguments to receive the UID and GID
ARG UID
ARG GID

# Copy custom php.ini file
COPY php.ini /usr/local/etc/php/conf.d/

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Create a new group and user
RUN addgroup --gid ${GID} --system gemini
# Create user and add to the group, creating a home directory at /home/gemini
RUN adduser -u ${UID} --shell /bin/sh --disabled-password --ingroup gemini --gecos "" gemini

# Tell PHP-FPM to run as our new gemini user
RUN sed -i 's/user = www-data/user = gemini/g' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/group = www-data/group = gemini/g' /usr/local/etc/php-fpm.d/www.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Change ownership of the workspace directory to the new user
RUN chown -R gemini:gemini /var/www/html

# Set environment variables for the user's home
ENV HOME=/home/gemini
ENV USER=gemini

# Switch to the new user
USER gemini
