FROM ubuntu:xenial

RUN apt update && \
    apt-get install -y python-software-properties software-properties-common curl && \
    curl -sL https://deb.nodesource.com/setup_7.x | bash - && \
    curl -sL https://sentry.io/get-cli/ | bash
RUN
    add-apt-repository ppa:ondrej/php && \
    apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8 && \
    echo "deb http://mirror.aarnet.edu.au/pub/MariaDB/repo/10.1/ubuntu xenial main" >> /etc/apt/sources.list.d/mariadb101.list && \
    echo "deb-src http://mirror.aarnet.edu.au/pub/MariaDB/repo/10.1/ubuntu xenial main" >> /etc/apt/sources.list.d/mariadb101.list && \
    apt-get update
RUN
    apt install -y \
    php7.2-fpm \
    php7.2-apcu \
    php7.2-ctype \
    php7.2-curl \
    php7.2-dom \
    php7.2-gd \
    php7.2-iconv \
    php7.2-imagick \
    php7.2-json \
    php7.2-intl \
    php7.2-mcrypt \
    php7.2-mbstring \
    php7.2-opcache \
    php7.2-openssl \
    php7.2-pdo \
    php7.2-pdo_mysql \
    php7.2-mysqli \
    php7.2-xml \
    php7.2-zlib \
    php7.2-phar \
    php7.2-tokenizer \
    php7.2-session \
    php7.2-simplexml \
    php7.2-xdebug

# Install LaTex packages
#RUN apk add \
#    texlive-full \
#    texmf-dist-latexextra \
#    texlive-dvi \
#    texmf-dist-most \
#    texmf-dist-langcyrillic

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/* && \
    curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

ADD symfony.ini /etc/php7/php-fpm.d/
ADD symfony.ini /etc/php7/cli/conf.d/
ADD xdebug.ini  /etc/php7/conf.d/

ADD symfony.pool.conf /etc/php7/php-fpm.d/

CMD ["php-fpm7", "-F"]

WORKDIR /var/www/symfony
EXPOSE 9000