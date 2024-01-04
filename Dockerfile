FROM php:5.6-apache
USER root
ARG UID="33"
ARG GID="33"
RUN usermod --uid ${UID} --gid ${GID} www-data && chown -R www-data:www-data /var/www \
    && echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list \
    && apt-get update \
    # locales
    && apt-get install -y locales locales-all \
    && locale-gen en_US.UTF-8 ru_RU.UTF-8 \
    && rm -rf /var/cache/apt/*
USER www-data 
RUN mkdir -p /var/www/plex/media /var/www/plex/library && chown -R www-data:www-data /var/www/plex/media /var/www/plex/library
COPY . /var/www/html/
