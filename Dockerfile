FROM php:7.4-apache

# Instalar las dependencias necesarias para compilar las extensiones
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
         gd \
         mysqli \
         pdo \
         pdo_mysql \
         mbstring \
         curl \
         xml \
         zip \
         bcmath \
         intl \
         soap \
         opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar Apache para usar el directorio público deseado
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Instalar Xdebug
RUN pecl install -o -f xdebug-2.9.8 && docker-php-ext-enable xdebug

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar configuración personalizada de PHP
COPY ./php.ini /usr/local/etc/php/

# Actualizar la configuración de Apache para usar la nueva raíz del documento
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código de la aplicación al contenedor
COPY . .

# Instalar FPDF con Composer
# RUN composer require setasign/fpdf

RUN composer require setasign/fpdf:^1.8 setasign/fpdi:^2.0

# Asignar permisos adecuados
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
