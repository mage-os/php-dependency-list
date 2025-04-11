FROM php:8.3.19-cli

ARG UUID=1000
ARG GUID=1000

# composer install
RUN EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"; \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"; \
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then \
    >&2 echo 'ERROR: Invalid installer checksum'; \
    rm composer-setup.php; \
    exit 1; \
    fi; \
    php composer-setup.php --quiet; \
    RESULT=$?; \
    rm composer-setup.php; \
    mv composer.phar /usr/local/bin/composer; \
    exit $RESULT

# php extensions
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
        libicu-dev \
        libxml2-dev \
        libxslt-dev \
        libzip-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure bcmath \
	&& docker-php-ext-install -j$(nproc) bcmath \
    && docker-php-ext-configure intl \
	&& docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-configure pdo_mysql \
	&& docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-configure soap \
	&& docker-php-ext-install -j$(nproc) soap \
    && docker-php-ext-configure xsl \
	&& docker-php-ext-install -j$(nproc) xsl \
    && docker-php-ext-configure zip \
	&& docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-configure sockets \
	&& docker-php-ext-install -j$(nproc) sockets

# create user to run as
RUN groupadd --gid ${GUID} --non-unique mage \
    && useradd --gid mage --shell /bin/bash --uid ${UUID} --no-user-group --non-unique mage

WORKDIR /usr/src
USER mage
