FROM php:8.4-fpm

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive
ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN for i in $(seq 1 8); do mkdir -p "/usr/share/man/man$i"; done

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install selected extensions and other stuff
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y bash-completion build-essential git libjpeg-dev libfreetype6-dev libpng-dev libwebp-dev nano nodejs rsync unzip
#    && pecl install xdebug
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && docker-php-ext-install gd
RUN install-php-extensions bcmath calendar ctype curl dom exif ffi filter gettext hash http iconv imagick intl libxml mbstring openssl pcntl pcov pcre pdo_pgsql pgsql phar posix reflection session simplexml tokenizer xdebug xml xmlwriter xsl zip
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN addgroup --gid ${GID} --system laravel
RUN adduser  --gid ${GID} --system --disabled-password --shell /bin/sh -u ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf

USER laravel
