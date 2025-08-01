FROM dunglas/frankenphp:php8.4 AS app

ARG WWWGROUP
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Europe/London
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www/html

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

EXPOSE 8000
EXPOSE 443

COPY docker/php.ini /usr/local/etc/php/php.ini

RUN apt-get update \
     && apt-get install -y htop tree nano procps supervisor wget gpg cron \
        zlib1g-dev libpng-dev libjpeg-dev libzip-dev zip libonig-dev libmagickwand-dev\
        libfreetype6-dev libfontconfig1-dev libicu-dev \
     && docker-php-ext-configure gd --with-jpeg \
     && install-php-extensions zip \
     && docker-php-ext-configure pcntl --enable-pcntl \
        && install-php-extensions pcntl \
     && docker-php-ext-configure intl \
     && install-php-extensions intl \
     && install-php-extensions pdo gd mbstring \
     && apt-get clean

# composer
RUN curl -sS https://getcomposer.org/installer | php -- \
  --install-dir=/usr/bin --filename=composer

COPY --chown=www-data:www-data . /var/www/html
RUN ls -la /var/www/html

# Cronjob
COPY ./docker/crontab /etc/crontab
RUN chmod 644 /etc/crontab
RUN crontab /etc/crontab

# Supervisor
COPY ./docker/php/entrypoint.sh /entrypoint.sh

ENV NODE_VERSION=22

RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null \
    && apt-get update \
    && apt-get install -y nodejs yarn

RUN apt-get update  \
    && apt-get install -y chromium-headless-shell \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*