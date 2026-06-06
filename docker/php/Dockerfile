FROM php:8.3-fpm-alpine

# Composer (per gestire le dipendenze dentro il container)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Estensioni base sufficienti per Laravel 11 con session/cache su file (nessun DB).
# php:8.3-fpm-alpine include gia' la maggior parte di cio' che serve; aggiungiamo
# pcntl per i comandi artisan long-running e l'header zlib per opcache.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install pcntl opcache \
    && apk del .build-deps

# traceroute con cap_net_raw invece di setuid: il processo resta www-data
# ma può aprire raw socket ICMP. Il binario è una copia reale di busybox
# perché setcap non funziona su symlink.
RUN apk add --no-cache libcap \
    && cp /bin/busybox /usr/local/bin/traceroute \
    && setcap cap_net_raw+ep /usr/local/bin/traceroute

EXPOSE 9000

CMD ["php-fpm"]
