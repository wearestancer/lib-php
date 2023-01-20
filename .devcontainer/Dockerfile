
ARG VERSION

FROM php:${VERSION}

RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y --no-install-recommends ca-certificates git libzip-dev unzip zip \
 && apt-get autoremove -y

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
 && sed -i 's/^;*memory_limit =.*/memory_limit = 512M/' $PHP_INI_DIR/php.ini \
 && sed -i 's/^;*curl.cainfo =.*/curl.cainfo = \/etc\/ssl\/certs\/ca-certificates.crt/' $PHP_INI_DIR/php.ini \
 && sed -i 's/^;*date.timezone =.*/date.timezone = UTC/' $PHP_INI_DIR/php.ini

# Add PHP extensions
RUN pecl install xdebug zip \
 && docker-php-ext-enable xdebug zip

# Configure xdebug
RUN echo 'xdebug.mode=coverage' >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

# Add composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php -r "copy('https://composer.github.io/installer.sig', 'checksum');" \
 && php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('checksum')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php checksum
