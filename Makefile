.PHONY: help build up down logs restart clean test dev health-check scale

# Colors
BLUE=\033[0;34m
GREEN=\033[0;32m
RED=\033[0;31m
YELLOW=\033[0;33m
NC=\033[0m

help: ## Show this help message
	@echo "$(BLUE)Transport Management System - Docker Commands$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

# Development targets
dev: ## Start development environment
	@echo "$(BLUE)Starting development environment...$(NC)"
	docker-compose up -d
	@echo "$(GREEN)✅ Services started$(NC)"
	@echo "App: http://localhost:8000"
	@echo "Mailpit: http://localhost:8025"
	@echo "Vite: http://localhost:5173"

build: ## Build development images
	@echo "$(BLUE)Building development images...$(NC)"
	docker-compose build

up: ## Start services (docker-compose up)
	docker-compose up -d

down: ## Stop and remove containers
	@echo "$(YELLOW)Stopping containers...$(NC)"
	docker-compose down

logs: ## View service logs
	docker-compose logs -f

logs-app: ## View app logs
	docker-compose logs -f app

logs-web: ## View nginx logs
	docker-compose logs -f web

logs-db: ## View MySQL logs
	docker-compose logs -f mysql

# Database targets
migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

seed: ## Seed the database
	docker-compose exec app php artisan db:seed

db-backup: ## Backup MySQL database
	@echo "$(BLUE)Backing up database...$(NC)"
	docker-compose exec mysql mysqladmin dump -uroot -p$$(cat .env | grep MYSQL_ROOT_PASSWORD | cut -d '=' -f2) > backup-$$(date +%Y%m%d-%H%M%S).sql
	@echo "$(GREEN)✅ Database backed up$(NC)"

db-restore: ## Restore MySQL database
	@echo "$(BLUE)Enter backup filename:$(NC)"
	@read FILE; \
	docker-compose exec -T mysql mysql -uroot -p$$(cat .env | grep MYSQL_ROOT_PASSWORD | cut -d '=' -f2) < $$FILE
	@echo "$(GREEN)✅ Database restored$(NC)"

# Laravel commands
artisan: ## Run artisan command (make artisan cmd="migrate")
	docker-compose exec app php artisan $(cmd)

tinker: ## Open Laravel Tinker
	docker-compose exec app php artisan tinker

optimize: ## Optimize Laravel (cache config, routes, views)
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

optimize-clear: ## Clear all caches
	docker-compose exec app php artisan optimize:clear

# Queue targets
queue: ## Start queue worker
	docker-compose exec queue php artisan queue:work

queue-fail: ## Show failed jobs
	docker-compose exec queue php artisan queue:failed

queue-retry: ## Retry all failed jobs
	docker-compose exec queue php artisan queue:retry all

queue-flush: ## Flush all jobs
	docker-compose exec queue php artisan queue:flush

# Testing targets
test: ## Run test suite
	@echo "$(BLUE)Running tests...$(NC)"
	docker-compose exec app php artisan test --compact

test-unit: ## Run unit tests
	docker-compose exec app php artisan test tests/Unit --compact

test-feature: ## Run feature tests
	docker-compose exec app php artisan test tests/Feature --compact

# Cache & Optimization
cache-clear: ## Clear all caches
	@echo "$(BLUE)Clearing caches...$(NC)"
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	@echo "$(GREEN)✅ Caches cleared$(NC)"

storage-link: ## Create storage symlink
	docker-compose exec app php artisan storage:link

permissions: ## Fix storage permissions
	@echo "$(BLUE)Fixing permissions...$(NC)"
	docker-compose exec app chmod -R 755 storage bootstrap/cache
	docker-compose exec app chown -R 1000:1000 storage bootstrap/cache
	@echo "$(GREEN)✅ Permissions fixed$(NC)"

# Monitoring & Health
health-check: ## Run health check script
	@echo "$(BLUE)Running health checks...$(NC)"
	bash health-check.sh

stats: ## Show container resource usage
	docker stats

ps: ## Show running containers
	docker-compose ps

# Cleanup
clean: ## Remove containers and volumes
	@echo "$(RED)Removing containers and volumes...$(NC)"
	docker-compose down -v

prune: ## Clean up unused Docker resources
	@echo "$(YELLOW)Pruning Docker resources...$(NC)"
	docker image prune -f
	docker volume prune -f
	docker network prune -f
	@echo "$(GREEN)✅ Cleanup complete$(NC)"

prune-all: ## Clean up all Docker resources (WARNING: removes unused everything)
	@echo "$(RED)⚠️  WARNING: This will remove ALL unused Docker resources$(NC)"
	docker system prune -a --volumes -f

# Utility targets
bash: ## Open bash in app container
	docker-compose exec app bash

bash-web: ## Open bash in web container
	docker-compose exec web sh

mysql-cli: ## Open MySQL CLI
	docker-compose exec mysql mysql -uroot -p$$(cat .env | grep MYSQL_ROOT_PASSWORD | cut -d '=' -f2)

redis-cli: ## Open Redis CLI
	docker-compose exec redis redis-cli

# Scaling
scale: ## Scale service (make scale srv=app count=3)
	@echo "$(BLUE)Scaling $(srv) to $(count) instances...$(NC)"
	docker-compose up -d --scale $(srv)=$(count)

# Complete workflows
reset: clean build up migrate seed ## Complete reset (clean, build, up, migrate, seed)
	@echo "$(GREEN)✅ Reset complete$(NC)"

restart: down up ## Restart all services
	@echo "$(GREEN)✅ Services restarted$(NC)"

full-setup: build up migrate seed optimize storage-link permissions ## Complete setup
	@echo "$(GREEN)✅ Full setup complete$(NC)"

.DEFAULT_GOAL := help
