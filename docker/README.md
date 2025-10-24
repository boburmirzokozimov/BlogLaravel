# Docker Configuration for Laravel Blog

This directory contains Docker configurations for running the Laravel Blog application with PHP 8.4.

## Services

### 1. PHP-FPM (php-fpm)
- **Base Image**: php:8.4-fpm
- **Purpose**: Handles PHP execution for web requests
- **Port**: 9000 (internal)
- **Features**:
  - PHP 8.4
  - Common PHP extensions (PDO, MySQL, GD, Zip, etc.)
  - Composer installed
  - Custom PHP configuration (php.ini)

### 2. Nginx (nginx)
- **Base Image**: nginx:alpine
- **Purpose**: Web server and reverse proxy
- **Port**: 8080 (host) → 80 (container)
- **Features**:
  - Optimized for Laravel
  - Gzip compression enabled
  - Custom nginx configuration
  - PHP-FPM integration

### 3. PHP-CLI (php-cli)
- **Base Image**: php:8.4-cli
- **Purpose**: Run Artisan commands, queue workers, and other CLI tasks
- **Features**:
  - PHP 8.4
  - Same extensions as PHP-FPM
  - Composer installed
  - Interactive terminal access

### 4. MySQL Database (database)
- **Base Image**: mysql:8.0
- **Purpose**: Database server
- **Port**: 3306
- **Credentials**:
  - Database: blog
  - Root Password: root
  - User: blog_user
  - Password: password

## Directory Structure

```
docker/
├── php-fpm/
│   ├── Dockerfile       # PHP-FPM container configuration
│   └── php.ini          # Custom PHP settings
├── php-cli/
│   └── Dockerfile       # PHP-CLI container configuration
├── nginx/
│   ├── Dockerfile       # Nginx container configuration
│   ├── nginx.conf       # Main nginx configuration
│   └── default.conf     # Laravel site configuration
└── README.md            # This file
```

## Getting Started

### Quick Start with Makefile (Recommended)

```bash
# Show all available commands
make help

# Complete fresh installation
make fresh

# Or step by step:
make install              # Build and start services
cp .env.example .env      # Copy environment file
make setup                # Generate key, run migrations, set permissions
```

### Manual Setup with Docker Compose

### 1. Build and Start Services

```bash
docker-compose up -d --build
# or with Makefile: make build up
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
docker-compose exec php-cli composer install
# or: make composer-install

# Install Node dependencies (if needed)
npm install
# or: make npm-install
```

### 3. Setup Laravel

```bash
# Copy environment file
cp .env.example .env

# Generate application key
docker-compose exec php-cli php artisan key:generate
# or: make key-generate

# Run migrations
docker-compose exec php-cli php artisan migrate
# or: make migrate

# Clear and cache config
docker-compose exec php-cli php artisan config:cache
# or: make cache
```

### 4. Set Permissions

```bash
docker-compose exec php-cli chmod -R 775 storage bootstrap/cache
# or: make permissions
```

## Useful Commands

### Makefile Commands (Recommended)

```bash
# Show all available commands
make help

# Service Management
make up                   # Start all services
make down                 # Stop all services
make restart              # Restart all services
make restart-php          # Restart only PHP-FPM
make restart-nginx        # Restart only Nginx
make logs                 # View all logs
make logs-php             # View PHP-FPM logs
make ps                   # Show running containers

# Shell Access
make shell                # Access PHP-FPM shell
make shell-cli            # Access PHP-CLI shell
make shell-nginx          # Access Nginx shell
make shell-db             # Access MySQL shell

# Artisan Commands
make artisan CMD="migrate"           # Run any artisan command
make migrate                         # Run migrations
make migrate-fresh                   # Fresh migrations
make migrate-seed                    # Migrate and seed
make seed                            # Run seeders
make test                            # Run tests

# Composer Commands
make composer-install                # Install dependencies
make composer-update                 # Update dependencies
make composer CMD="require package"  # Run any composer command

# Cache Management
make cache-clear                     # Clear all caches
make cache                           # Cache config, routes, views
make optimize                        # Optimize application
make optimize-clear                  # Clear optimization

# Database Operations
make db-backup                       # Backup database
make db-restore FILE=backup.sql      # Restore from backup

# Maintenance
make permissions                     # Fix file permissions
make clean                           # Clean up Docker
make rebuild                         # Rebuild everything
make fresh                           # Fresh installation
make info                            # Show app info
```

### Direct Docker Compose Commands

### PHP-CLI Commands

```bash
# Run Artisan commands
docker-compose exec php-cli php artisan [command]

# Run Composer commands
docker-compose exec php-cli composer [command]

# Access PHP-CLI container shell
docker-compose exec php-cli bash

# Run PHPUnit tests
docker-compose exec php-cli php artisan test
```

### Service Management

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart a specific service
docker-compose restart php-fpm

# View logs
docker-compose logs -f [service-name]

# Rebuild services
docker-compose up -d --build
```

### Database Commands

```bash
# Access MySQL shell
docker-compose exec database mysql -u blog_user -ppassword blog

# Backup database
docker-compose exec database mysqldump -u blog_user -ppassword blog > backup.sql

# Restore database
docker-compose exec -T database mysql -u blog_user -ppassword blog < backup.sql
```

## Access URLs

- **Application**: http://localhost:8080
- **MySQL**: localhost:3306

## Environment Variables

Update your `.env` file with these database settings:

```env
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=blog_user
DB_PASSWORD=password
```

## Troubleshooting

### Permission Issues
If you encounter permission issues:
```bash
docker-compose exec php-cli chown -R www:www /var/www/html/storage
docker-compose exec php-cli chmod -R 775 /var/www/html/storage
```

### Clear All Caches
```bash
docker-compose exec php-cli php artisan cache:clear
docker-compose exec php-cli php artisan config:clear
docker-compose exec php-cli php artisan route:clear
docker-compose exec php-cli php artisan view:clear
```

### Rebuild Everything
```bash
docker-compose down -v
docker-compose up -d --build
```

## Notes

- The application code is mounted as a volume, so changes are reflected immediately
- Database data is persisted in a named volume (`dbdata`)
- All services run on the `blog-network` bridge network
- The www user (UID 1000) is created for proper file permissions

