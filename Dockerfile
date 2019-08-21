FROM php:7.3-apache

COPY ./ /var/www/html
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
 
EXPOSE 80 443 3306 
