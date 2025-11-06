FROM nginx:alpine

RUN addgroup -g 1000 www && \
    adduser -D -u 1000 -G www www

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

USER www

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

