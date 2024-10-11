# Use the official WordPress image
FROM wordpress:latest

# Copy your WordPress files into the container
COPY . /var/www/html

# Set permissions (adjust if necessary)
RUN chown -R www-data:www-data /var/www/html