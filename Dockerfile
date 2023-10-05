ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-fpm

# set main params
ARG BUILD_ARGUMENT_DEBUG_ENABLED=false
ENV DEBUG_ENABLED=$BUILD_ARGUMENT_DEBUG_ENABLED
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=$BUILD_ARGUMENT_ENV
ENV APP_HOME /var/www/html
ARG UID=1000
ARG GID=1000
ENV USERNAME=www-data
ENV COMPOSER_DIR=/usr/bin/composer
ENV SYMFONY_CLI_DIR=/usr/local/bin/symfony
ENV LC_ALL en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8


### Fix permissions
RUN mkdir -p /home/$USERNAME \
    && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -u $UID $USERNAME -d /home/$USERNAME \
    && groupmod -g $GID $USERNAME

# check environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
    elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then echo "Building development environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then echo "Building test environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then echo "Building staging environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then echo "Building production environment."; \
    else echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev,test,staging,prod." && exit 2; \
    fi

# install all the dependencies and enable PHP modules
RUN set -e; \
  apt-get update && apt-get upgrade -y \
    && apt-get install -y \
      procps \
      nano \
      vim \
      curl \
      wget \
      fish \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      cron \
      libzip-dev \
      locales \
      locales-all \
      enca \
      librabbitmq-dev \
    && pecl install amqp-1.11.0 \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && yes '' | pecl install -o -f redis && docker-php-ext-enable redis \
    && docker-php-ext-install \
      pdo_mysql \
      intl \
      opcache \
      zip \
      sockets\
    && docker-php-ext-enable amqp \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# gd only for tests, pcntl for phpcs with some cores
RUN if [ "$BUILD_ARGUMENT_ENV" = "dev" ] || [ "$BUILD_ARGUMENT_ENV" = "staging" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; \
    then \
        apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
        && docker-php-ext-install gd pcntl; \
    fi

# create document root
RUN mkdir -p $APP_HOME/public

# change owner
RUN chown -R $USERNAME:$USERNAME $APP_HOME

# put php config for Symfony
COPY ./docker/$BUILD_ARGUMENT_ENV/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/$BUILD_ARGUMENT_ENV/php.ini /usr/local/etc/php/php.ini

# install Xdebug in case dev/test environment
# @todo temporary disabled Xdebug, fix it
COPY ./docker/general/do_we_need_xdebug.sh /tmp/
COPY ./docker/dev/xdebug.ini /tmp/
RUN chmod u+x /tmp/do_we_need_xdebug.sh && /tmp/do_we_need_xdebug.sh

# install composer
COPY --from=composer:2 $COMPOSER_DIR $COMPOSER_DIR
RUN chmod +x $COMPOSER_DIR
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN chown -R $USERNAME:$USERNAME $COMPOSER_DIR



# set working directory
WORKDIR $APP_HOME

USER ${USERNAME}

# copy source files
COPY --chown=${USERNAME}:${USERNAME} . $APP_HOME/

# install all PHP dependencies
RUN if [ "$BUILD_ARGUMENT_ENV" = "dev" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; then COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress; \
    else export APP_ENV=$BUILD_ARGUMENT_ENV && COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress --no-dev; \
    fi

# create cached config file .env.local.php in case staging/prod/test-ci environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "staging" ] || [ "$BUILD_ARGUMENT_ENV" = "prod" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; \
     then composer dump-env $BUILD_ARGUMENT_ENV; \
    fi

USER root
