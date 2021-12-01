FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
libzip-dev && \
apt-get install -y libicu-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

WORKDIR /usr/src/app

COPY app/ /usr/src/app

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer
#RUN composer install --no-scripts --prefer-dist --no-interaction

# RUN chmod +x scripts/create_env_file.sh
# RUN chmod +x scripts/create_user_content_directories.sh

# RUN scripts/create_env_file.sh
# RUN scripts/create_user_content_directories.sh

# CMD bash -c "composer install --no-scripts --prefer-dist --no-interaction"
