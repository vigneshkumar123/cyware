FROM ubuntu:latest

ENV DEBIAN_FRONTEND noninteractive

# Install updates and dependencies
RUN apt-get update && \
    apt install software-properties-common  -y && add-apt-repository ppa:ondrej/php && \
    apt-get install -y --no-install-recommends nginx php8.1 libapache2-mod-php8.1 php8.1-common php8.1-gd php8.1-mysql php8.1-curl php8.1-intl php8.1-xsl php8.1-mbstring php8.1-zip php8.1-bcmath php8.1-iconv php8.1-soap php8.1-fpm php8.1-mcrypt composer && \
    rm -rf /var/lib/apt/lists/*

# Configure Nginx
COPY nginx.conf /etc/nginx/sites-enabled/
RUN unlink /etc/nginx/sites-enabled/default

# Set working directory
WORKDIR /var/www/html/

# Copy the web application files to the container
COPY src/ .

# Install dependencies and optimize for production
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start Nginx and PHP-FPM services
CMD service php8.1-fpm start && nginx -g "daemon off;"