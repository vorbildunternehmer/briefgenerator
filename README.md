Briefgenerator
===

Mittels des Briefgenerators können ganz einfach Markdown-Texte als PDF-Briefe generiert werden.

Eine prima Möglichkeit zur Effizienzsteigerung von Obsidian.

Dazu einfach den Ordner `organisations/demo` anpassen / kopieren und gewünschte Änderungen vornehmen in folgenden Dateien:

- bg.png
- index.php
- style.css

Benötigte Schriftarten können über den Ordner `fonts` beigefügt werden.

Anschließend kann man mit `docker-compose up -d` den Server starten.

**docker-compose.yml**

```yml
version: '2'

services:
  brief:
    build: .
    volumes:
      - ./organisations:/organisations
      - ./fonts:/fonts
    networks:
      - web
    command: php -S 0.0.0.0:80
    expose:
      - 80
    ports:
      - 8080:80
networks:
  web:
```

**Dockerfile**
```Dockerfile
# Use the official PHP image as the base image
FROM php:7.4.33

RUN apt update \
  && apt install \
  software-properties-common \
  libfreetype6-dev \
  zlib1g-dev \
  libjpeg-dev \
  libjpeg-progs \
  djvulibre-bin \
  imagemagick \
  libmagickwand-dev \
  exiftool \
  libzip-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  ghostscript -y\
  && pecl install imagick \
  && docker-php-ext-enable imagick \
  && docker-php-ext-install exif \
  && docker-php-ext-enable exif \
  && docker-php-ext-install zip \
  && docker-php-ext-enable zip \
  && docker-php-ext-configure gd \
  --with-freetype=/usr/include/ \
  --with-jpeg=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install gd \
  && docker-php-ext-enable gd \
  && rm -rf /tmp/*

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && rm composer-setup.php \
  && mv composer.phar /usr/local/bin/composer


# Optionally, you can install other PHP extensions or dependencies here if needed

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your PHP files into the container
COPY ./src/ /var/www/html
COPY ./settings/policy.xml /etc/ImageMagick-6/policy.xml

RUN composer install
```