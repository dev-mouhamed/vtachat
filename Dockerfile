FROM php:8.2-apache

# Installer les extensions MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copier tous les fichiers
COPY . /var/www/html/

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80