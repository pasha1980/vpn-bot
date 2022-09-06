ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-fpm
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# install common packages
RUN apt-get update \
    && apt-get install -y libpq-dev \
    libpcre3-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev  \
    libjpeg-dev \
    libpng-dev \
    zip \
    git \
    nginx \
    python3-pip \
    python-setuptools \
    && pip install supervisor \
    && pip install git+https://github.com/coderanger/supervisor-stdout

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd pdo-mysql bcmath
RUN docker-php-ext-install pdo pdo_mysql sockets zip

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=. --filename=composer
RUN mv composer /usr/local/bin/

# install redis
RUN pecl install redis  \
    && docker-php-ext-enable redis

# copy sources
RUN mkdir -p /var/www/app
WORKDIR /var/www/app
COPY . /var/www/app

# wait-for-it script
COPY ./build/scripts/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

# run `composer install`
RUN /usr/local/bin/composer install -d /var/www/app

# supervisor config
COPY ./build/dev/supervisord.conf /etc/supervisord.conf

# override nginx's default config
COPY ./build/dev/nginx.conf /etc/nginx/sites-available/default

# boot set up
COPY build/dev/boot.sh /usr/local/bin/boot
RUN chmod +x /usr/local/bin/boot

EXPOSE 80

ENTRYPOINT ["sh", "/usr/local/bin/boot"]