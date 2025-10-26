.PHONY: help build up down restart logs ps shell shell-cli shell-nginx composer artisan test migrate migrate-fresh seed cache-clear permission install setup clean rebuild

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Show this help message
	@echo "$(BLUE)Available commands:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

# Docker commands
build: ## Build all Docker containers
	@echo "$(BLUE)Building Docker containers...$(NC)"
	docker-compose build

up: ## Start all Docker containers
	@echo "$(GREEN)Starting Docker containers...$(NC)"
	docker-compose up -d

down: ## Stop all Docker containers
	@echo "$(YELLOW)Stopping Docker containers...$(NC)"
	docker-compose down

restart: ## Restart all Docker containers
	@echo "$(YELLOW)Restarting Docker containers...$(NC)"
	docker-compose restart

restart-php: ## Restart PHP-FPM service
	@echo "$(YELLOW)Restarting PHP-FPM...$(NC)"
	docker-compose restart php-fpm

restart-nginx: ## Restart Nginx service
	@echo "$(YELLOW)Restarting Nginx...$(NC)"
	docker-compose restart nginx

restart-cli: ## Restart PHP-CLI service
	@echo "$(YELLOW)Restarting PHP-CLI...$(NC)"
	docker-compose restart php-cli

logs: ## Show logs for all services
	docker-compose logs -f

logs-php: ## Show PHP-FPM logs
	docker-compose logs -f php-fpm

logs-nginx: ## Show Nginx logs
	docker-compose logs -f nginx

logs-cli: ## Show PHP-CLI logs
	docker-compose logs -f php-cli

logs-db: ## Show database logs
	docker-compose logs -f database

ps: ## Show running containers
	docker-compose ps

# Shell access
shell: ## Access PHP-FPM container shell
	docker-compose exec php-fpm bash

shell-cli: ## Access PHP-CLI container shell
	docker-compose exec php-cli bash

shell-nginx: ## Access Nginx container shell
	docker-compose exec nginx sh

shell-db: ## Access MySQL shell
	docker-compose exec database mysql -u blog_user -ppassword blog

# Composer commands
composer-install: ## Install PHP dependencies
	@echo "$(BLUE)Installing Composer dependencies...$(NC)"
	docker-compose exec php-cli composer install

composer-update: ## Update PHP dependencies
	@echo "$(BLUE)Updating Composer dependencies...$(NC)"
	docker-compose exec php-cli composer update

composer-dump: ## Dump Composer autoload
	docker-compose exec php-cli composer dump-autoload

composer: ## Run Composer command (use: make composer CMD="require package")
	docker-compose exec php-cli composer $(CMD)

# Artisan commands
artisan: ## Run Artisan command (use: make artisan CMD="migrate")
	docker-compose exec php-cli php artisan $(CMD)

migrate: ## Run database migrations
	@echo "$(BLUE)Running migrations...$(NC)"
	docker-compose exec php-cli php artisan migrate

migrate-fresh: ## Fresh migration (WARNING: destroys data)
	@echo "$(RED)Running fresh migrations (this will destroy all data)...$(NC)"
	docker-compose exec php-cli php artisan migrate:fresh

migrate-rollback: ## Rollback last migration
	@echo "$(YELLOW)Rolling back last migration...$(NC)"
	docker-compose exec php-cli php artisan migrate:rollback

seed: ## Run database seeders
	@echo "$(BLUE)Running seeders...$(NC)"
	docker-compose exec php-cli php artisan db:seed

migrate-seed: ## Run migrations and seeders
	@echo "$(BLUE)Running migrations and seeders...$(NC)"
	docker-compose exec php-cli php artisan migrate --seed

migrate-fresh-seed: ## Fresh migration with seeding
	@echo "$(RED)Running fresh migrations with seeding...$(NC)"
	docker-compose exec php-cli php artisan migrate:fresh --seed

# Testing
test: ## Run PHPUnit tests
	@echo "$(BLUE)Running tests...$(NC)"
	docker-compose exec php-cli php artisan test

test-coverage: ## Run tests with coverage
	docker-compose exec php-cli php artisan test --coverage

# Cache commands
cache-clear: ## Clear all Laravel caches
	@echo "$(YELLOW)Clearing all caches...$(NC)"
	docker-compose exec php-cli php artisan cache:clear
	docker-compose exec php-cli php artisan config:clear
	docker-compose exec php-cli php artisan route:clear
	docker-compose exec php-cli php artisan view:clear

cache: ## Cache Laravel config, routes, and views
	@echo "$(BLUE)Caching configuration...$(NC)"
	docker-compose exec php-cli php artisan config:cache
	docker-compose exec php-cli php artisan route:cache
	docker-compose exec php-cli php artisan view:cache

optimize: ## Optimize Laravel application
	@echo "$(BLUE)Optimizing application...$(NC)"
	docker-compose exec php-cli php artisan optimize

optimize-clear: ## Clear optimization cache
	docker-compose exec php-cli php artisan optimize:clear

# Permission commands
permissions: ## Fix storage and cache permissions
	@echo "$(BLUE)Fixing permissions...$(NC)"
	docker-compose exec php-cli chmod -R 775 storage bootstrap/cache
	docker-compose exec php-cli chown -R www:www storage bootstrap/cache

# Queue commands
queue-work: ## Run queue worker
	docker-compose exec php-cli php artisan queue:work

queue-listen: ## Listen to queue
	docker-compose exec php-cli php artisan queue:listen

queue-restart: ## Restart queue workers
	docker-compose exec php-cli php artisan queue:restart

# Installation and setup
install: build up composer-install permissions ## Complete installation (build, start, install dependencies)
	@echo "$(GREEN)Installation complete!$(NC)"
	@echo "$(YELLOW)Don't forget to:$(NC)"
	@echo "  1. Copy .env.example to .env"
	@echo "  2. Run: make key-generate"
	@echo "  3. Run: make migrate"

setup: ## Setup Laravel application (key, migrate, seed)
	@echo "$(BLUE)Setting up Laravel application...$(NC)"
	docker-compose exec php-cli php artisan key:generate
	docker-compose exec php-cli php artisan migrate --seed
	docker-compose exec php-cli php artisan storage:link
	@make permissions
	@make cache
	@echo "$(GREEN)Setup complete! Visit http://localhost:8080$(NC)"

key-generate: ## Generate application key
	docker-compose exec php-cli php artisan key:generate

storage-link: ## Create storage symbolic link
	docker-compose exec php-cli php artisan storage:link

# Maintenance
clean: ## Clean up containers and volumes
	@echo "$(RED)Cleaning up Docker environment...$(NC)"
	docker-compose down -v
	docker system prune -f

rebuild: clean build up ## Rebuild everything from scratch
	@echo "$(GREEN)Rebuild complete!$(NC)"

fresh: ## Fresh start (down, build, up, setup)
	@echo "$(BLUE)Starting fresh installation...$(NC)"
	@make down
	@make build
	@make up
	@make composer-install
	@make setup
	@echo "$(GREEN)Fresh installation complete!$(NC)"

# Database commands
db-backup: ## Backup database to backups/backup-TIMESTAMP.sql
	@mkdir -p backups
	@echo "$(BLUE)Backing up database...$(NC)"
	docker-compose exec -T database mysqldump -u blog_user -ppassword blog > backups/backup-$$(date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)Backup complete!$(NC)"

db-restore: ## Restore database from backup (use: make db-restore FILE=backup.sql)
	@echo "$(YELLOW)Restoring database from $(FILE)...$(NC)"
	docker-compose exec -T database mysql -u blog_user -ppassword blog < $(FILE)
	@echo "$(GREEN)Restore complete!$(NC)"

# NPM commands (if you have Node.js locally or add a node container)
npm-install: ## Install NPM dependencies (requires local Node.js)
	npm install

npm-dev: ## Run NPM dev server
	npm run dev

npm-build: ## Build assets for production
	npm run build

# Info
info: ## Show application info
	@echo "$(BLUE)Application Information:$(NC)"
	@echo "$(GREEN)Web URL:$(NC) http://localhost:8080"
	@echo "$(GREEN)Database:$(NC) localhost:3306"
	@echo "$(GREEN)Database Name:$(NC) blog"
	@echo "$(GREEN)Database User:$(NC) blog_user"
	@echo "$(GREEN)Database Password:$(NC) password"
	@echo ""
	@echo "$(BLUE)Running Containers:$(NC)"
	@docker-compose ps

full:
	docker-compose exec php-cli composer fix
	docker-compose exec php-cli composer analyse
	docker-compose exec php-cli php artisan test

