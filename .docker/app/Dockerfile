####################### Build image ##################################################################
FROM dockette/debian:buster as build

# PHP
ENV PHP_MODS_DIR=/etc/php/8.0/mods-available
ENV PHP_CLI_DIR=/etc/php/8.0/cli/
ENV PHP_CLI_CONF_DIR=${PHP_CLI_DIR}/conf.d
ENV PHP_CGI_DIR=/etc/php/8.0/cgi/
ENV PHP_CGI_CONF_DIR=${PHP_CGI_DIR}/conf.d
ENV PHP_FPM_DIR=/etc/php/8.0/fpm/
ENV PHP_FPM_CONF_DIR=${PHP_FPM_DIR}/conf.d
ENV PHP_FPM_POOL_DIR=${PHP_FPM_DIR}/pool.d
ENV TZ=Europe/Prague

# INSTALLATION
RUN apt update && apt dist-upgrade -y && \
    # DEPENDENCIES #############################################################
    apt install -y wget curl apt-transport-https ca-certificates gnupg2 git && \
    # PHP DEB.SURY.CZ ##########################################################
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ buster main" > /etc/apt/sources.list.d/php.list && \
    wget -O- http://nginx.org/keys/nginx_signing.key | apt-key add - && \
    echo "deb http://nginx.org/packages/debian/ buster nginx" > /etc/apt/sources.list.d/nginx.list && \
    echo "deb-src http://nginx.org/packages/debian/ buster nginx" >> /etc/apt/sources.list.d/nginx.list && \
    apt update && \
    apt install -y --no-install-recommends \
        nano \
        nginx \
        sudo \
        supervisor \
        php8.0-apc \
        php8.0-apcu \
        php8.0-bz2 \
        php8.0-bcmath \
        php8.0-calendar \
        php8.0-cgi \
        php8.0-cli \
        php8.0-ctype \
        php8.0-curl \
        php8.0-fpm \
        php8.0-gettext \
        php8.0-intl \
        php8.0-imap \
        php8.0-mbstring \
        php8.0-memcached \
        php8.0-pdo \
        php8.0-pgsql \
        php8.0-ssh2 \
        php8.0-sqlite \
        php8.0-tidy \
        php8.0-zip \
        php8.0-xml && \
    # COMPOSER #################################################################
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --2 && \
    # PHP MOD(s) ###############################################################
    rm ${PHP_FPM_POOL_DIR}/www.conf && \
    # NGINX ####################################################################
    ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log && \
    # CLEAN UP #################################################################
    rm /etc/nginx/conf.d/default.conf && \
    apt-get clean -y && \
    apt-get autoclean -y && \
    apt-get remove -y wget && \
    apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/* /var/lib/log/* /tmp/* /var/tmp/*

# PHP
COPY ./.docker/app/php/php-fpm.conf /etc/php/8.0/

# NGINX
COPY ./.docker/app/nginx/nginx.conf /etc/nginx/
COPY ./.docker/app/nginx/mime.types /etc/nginx/

# SUPERVISOR
COPY ./.docker/app/supervisor/supervisord.conf /etc/supervisor/
COPY ./.docker/app/supervisor/services/nginx.conf /etc/supervisor/conf.d/nginx.conf
COPY ./.docker/app/supervisor/services/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf

WORKDIR /pocketpilot

# APPLICATION
COPY ./app /pocketpilot/app
COPY ./www /pocketpilot/www

# COMPOSER
COPY ./composer.json /pocketpilot/composer.json
COPY ./composer.lock /pocketpilot/composer.lock



####################### Development image ##########################################################
FROM build as develop
ARG USER_ID
ARG GROUP_ID
WORKDIR /pocketpilot

COPY ./.docker/app/init.sh /

COPY ./phpstan.neon /pocketpilot/phpstan.neon
COPY ./coding-standard.xml /pocketpilot/coding-standard.xml

COPY ./.docker/app/nginx/sites.d/sites.dev.conf /etc/nginx/sites.d/
RUN mv /etc/nginx/sites.d/sites.dev.conf /etc/nginx/sites.d/sites.conf

# PORTS
EXPOSE 80

RUN if [ -n "$USER_ID" ] && [ "${USER_ID}" != 0 ]; then \
    sed -i "s/^www-data:x:33:33:/www-data:x:$USER_ID:$GROUP_ID:/" /etc/passwd; \
    fi

CMD ["/init.sh"]



####################### Webpack build image ##########################################################
FROM node:8.12.0-alpine as webpack
WORKDIR /pocketpilot

COPY ./.docker/app/nginx/sites.d/sites.dev.conf /etc/nginx/sites.d/


COPY ./assets /pocketpilot/assets
COPY ./www /pocketpilot/www
COPY ./.eslintrc.js /pocketpilot/.eslintrc.js
COPY ./webpack.config.js /pocketpilot/webpack.config.js
COPY ./package.json /pocketpilot/package.json
COPY ./package-lock.json /pocketpilot/package-lock.json

RUN npm ci
RUN npm run build


####################### Production image #############################################################
FROM build as production
WORKDIR /pocketpilot

RUN useradd -m -s /bin/bash letsencrypt
COPY ./.docker/app/letsencrypt/init.sh /usr/local/bin/letsencrypt-init.sh
RUN chmod 755 /usr/local/bin/letsencrypt-init.sh
RUN echo 'letsencrypt ALL = NOPASSWD: /usr/sbin/service nginx force-reload' > /etc/sudoers.d/letsencrypt && chmod 600 /etc/sudoers.d/letsencrypt

COPY ./.docker/app/nginx/sites.d/sites.conf /etc/nginx/sites.d/
COPY ./.docker/app/supervisor/services/letsencrypt.conf /etc/supervisor/conf.d/letsencrypt.conf

RUN mkdir -p temp log && chown www-data:www-data temp log

COPY --from=webpack /pocketpilot/www/dist /pocketpilot/www/dist

RUN composer install --no-dev

EXPOSE 80 443
CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/supervisord.conf"]
