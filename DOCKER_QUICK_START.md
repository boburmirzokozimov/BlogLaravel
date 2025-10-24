# 🐳 Docker Quick Start Guide

This guide shows you how to use the Makefile commands to work with your Dockerized Laravel application.

## 📋 Quick Reference

### First Time Setup

```bash
# 1. Show all available commands
make help

# 2. Fresh installation (builds, starts, installs, and sets up everything)
make fresh

# That's it! Your app is now running at http://localhost:8080
```

### Most Used Commands

```bash
make up              # Start all services
make down            # Stop all services
make restart         # Restart all services
make logs            # View all logs
make shell-cli       # Access PHP CLI shell
make migrate         # Run database migrations
make test            # Run tests
```

## 📚 Command Categories

### 🚀 Service Management

| Command | Description |
|---------|-------------|
| `make up` | Start all Docker containers |
| `make down` | Stop all Docker containers |
| `make restart` | Restart all containers |
| `make restart-php` | Restart only PHP-FPM |
| `make restart-nginx` | Restart only Nginx |
| `make ps` | Show running containers |
| `make info` | Show application information |

### 📝 Logs

| Command | Description |
|---------|-------------|
| `make logs` | Show logs for all services |
| `make logs-php` | Show PHP-FPM logs |
| `make logs-nginx` | Show Nginx logs |
| `make logs-cli` | Show PHP-CLI logs |
| `make logs-db` | Show database logs |

### 💻 Shell Access

| Command | Description |
|---------|-------------|
| `make shell` | Access PHP-FPM container |
| `make shell-cli` | Access PHP-CLI container |
| `make shell-nginx` | Access Nginx container |
| `make shell-db` | Access MySQL shell |

### 🎨 Laravel Artisan

| Command | Description |
|---------|-------------|
| `make artisan CMD="..."` | Run any artisan command |
| `make migrate` | Run database migrations |
| `make migrate-fresh` | Fresh migrations (⚠️ destroys data) |
| `make migrate-seed` | Run migrations with seeding |
| `make migrate-rollback` | Rollback last migration |
| `make seed` | Run database seeders |
| `make key-generate` | Generate application key |
| `make storage-link` | Create storage symbolic link |

### 📦 Composer

| Command | Description |
|---------|-------------|
| `make composer-install` | Install PHP dependencies |
| `make composer-update` | Update PHP dependencies |
| `make composer-dump` | Dump autoload files |
| `make composer CMD="..."` | Run any composer command |

**Examples:**
```bash
make composer CMD="require laravel/sanctum"
make composer CMD="require --dev laravel/pint"
```

### 🧪 Testing

| Command | Description |
|---------|-------------|
| `make test` | Run PHPUnit tests |
| `make test-coverage` | Run tests with coverage report |

### 🗄️ Database

| Command | Description |
|---------|-------------|
| `make db-backup` | Backup database to `backups/` folder |
| `make db-restore FILE=backup.sql` | Restore database from backup |

**Examples:**
```bash
# Backup
make db-backup

# Restore
make db-restore FILE=backups/backup-20250124_120000.sql
```

### 🧹 Cache Management

| Command | Description |
|---------|-------------|
| `make cache-clear` | Clear all Laravel caches |
| `make cache` | Cache config, routes, and views |
| `make optimize` | Optimize the application |
| `make optimize-clear` | Clear optimization cache |

### 🔧 Maintenance

| Command | Description |
|---------|-------------|
| `make permissions` | Fix storage and cache permissions |
| `make clean` | Clean up Docker (removes volumes) |
| `make rebuild` | Rebuild everything from scratch |
| `make fresh` | Fresh installation |

### ⚙️ Queue Management

| Command | Description |
|---------|-------------|
| `make queue-work` | Run queue worker |
| `make queue-listen` | Listen to queue |
| `make queue-restart` | Restart queue workers |

## 🔄 Common Workflows

### Starting Your Day

```bash
make up              # Start all services
make logs-php        # Check if everything is running
```

### Adding a New Package

```bash
make composer CMD="require vendor/package"
make restart-php     # Restart PHP-FPM to load new classes
```

### Database Changes

```bash
# Create a new migration
make artisan CMD="make:migration create_posts_table"

# Edit the migration file, then run:
make migrate

# Or with seeding:
make migrate-seed
```

### Debugging Issues

```bash
make logs           # Check all logs
make shell-cli      # Access container
make cache-clear    # Clear caches
make restart        # Restart services
```

### Resetting Everything

```bash
make migrate-fresh-seed   # Reset database
make cache-clear          # Clear caches
make permissions          # Fix permissions
```

### Complete Fresh Start

```bash
make clean          # Remove everything
make fresh          # Rebuild and setup
```

## 📍 Important URLs

- **Application**: http://localhost:8080
- **Database**: localhost:3306

## 🔐 Database Credentials

```
Host: database (or localhost from your machine)
Port: 3306
Database: blog
Username: blog_user
Password: password
Root Password: root
```

## 💡 Tips

1. **Use `make help`** - Shows all available commands with descriptions
2. **Run `make info`** - Shows application info and running containers
3. **Logs are your friend** - Use `make logs` or `make logs-php` to debug issues
4. **Permission issues?** - Run `make permissions`
5. **Something broken?** - Try `make restart` or `make rebuild`

## 🆘 Troubleshooting

### Containers won't start?
```bash
make down
make build
make up
make logs
```

### Permission errors?
```bash
make permissions
```

### Database connection issues?
```bash
# Check if database is running
make ps

# Check database logs
make logs-db

# Verify .env has correct DB_HOST=database
```

### Cache issues?
```bash
make cache-clear
make restart
```

### Need a complete reset?
```bash
make clean
make fresh
```

## 📖 More Information

For detailed documentation, see:
- `docker/README.md` - Complete Docker documentation
- `Makefile` - All available commands and their definitions

---

**Happy coding! 🚀**

