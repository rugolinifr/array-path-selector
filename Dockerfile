FROM php:8.5-alpine3.23

ADD --chmod=755 https://getcomposer.org/download/2.9.8/composer.phar /usr/local/bin/composer
ADD --chmod=755 https://github.com/phpstan/phpstan/releases/download/2.2.5/phpstan.phar /usr/local/bin/phpstan

RUN apk update &&\
    apk add --virtual php_dependencies $PHPIZE_DEPS &&\
    docker-php-ext-install pdo_mysql &&\
    apk add linux-headers &&\
    pecl install xdebug-3.5.1 &&\
    docker-php-ext-enable xdebug

RUN ln -s $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
RUN printf "xdebug.mode=debug\n" > $PHP_INI_DIR/conf.d/zzzzz-xdebug.ini
RUN printf "xdebug.client_host=172.17.0.1\n" >> $PHP_INI_DIR/conf.d/zzzzz-xdebug.ini
RUN printf "xdebug.client_port=9003\n" >> $PHP_INI_DIR/conf.d/zzzzz-xdebug.ini
RUN printf "xdebug.start_with_request=trigger\n" >> $PHP_INI_DIR/conf.d/zzzzz-xdebug.ini
RUN printf "xdebug.trigger_value=1\n" >> $PHP_INI_DIR/conf.d/zzzzz-xdebug.ini

RUN printf "#!/bin/sh\n" > /usr/local/bin/phpunit
RUN printf "vendor/bin/phpunit \"\$@\"\n" >> /usr/local/bin/phpunit
RUN chmod 755 /usr/local/bin/phpunit