FROM nginx:alpine

# Build argument for environment (default: production)
ARG ENV=local

# Create www user/group to match PHP containers
RUN addgroup -g 1000 www && \
    adduser -D -u 1000 -G www www

# Install OpenSSL for SSL support
RUN apk add --no-cache openssl

# Copy nginx base configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy environment-specific config based on build arg
COPY docker/nginx/default.${ENV}.conf /etc/nginx/conf.d/default.conf

# Create cache directories and set permissions
RUN mkdir -p /var/cache/nginx/client_temp \
             /var/cache/nginx/proxy_temp \
             /var/cache/nginx/fastcgi_temp \
             /var/cache/nginx/uwsgi_temp \
             /var/cache/nginx/scgi_temp && \
    chown -R www:www /var/cache/nginx && \
    chown -R www:www /var/run && \
    chmod -R 755 /var/cache/nginx

# Create directory for Let's Encrypt challenges
RUN mkdir -p /var/www/certbot && \
    chown -R www:www /var/www/certbot

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
