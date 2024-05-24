# Dockerfile pour le back-end PHP
FROM php:7.4-apache

# Copier les fichiers du projet
COPY public/ /var/www/html/

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80
