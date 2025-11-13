# Use PHP with Apache
FROM php:8.2-apache

# Copy all backend files to Apache's document root
COPY . /var/www/html/

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
