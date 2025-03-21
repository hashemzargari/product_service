.PHONY: build up down test migrate seed clean help migration-create

# Default target
.DEFAULT_GOAL := help

# Colors for help messages
BLUE := \033[34m
GREEN := \033[32m
NC := \033[0m # No Color

help: ## Show this help message
	@echo '${BLUE}Usage:${NC}'
	@echo '  make <target>'
	@echo ''
	@echo '${BLUE}Targets:${NC}'
	@awk '/^[a-zA-Z\-_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")-1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${GREEN}%-15s${NC} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

build: ## Build the Docker containers
	docker-compose build

up: ## Start the application containers
	docker-compose up -d

down: ## Stop and remove the containers
	docker-compose down

test: ## Run all tests
	docker-compose exec app vendor/bin/phpunit

test-unit: ## Run unit tests only
	docker-compose exec app vendor/bin/phpunit tests/Unit

test-integration: ## Run integration tests only
	docker-compose exec app vendor/bin/phpunit tests/Integration

migrate: ## Run database migrations
	docker-compose exec app vendor/bin/phinx migrate

migrate-rollback: ## Rollback the last migration
	docker-compose exec app vendor/bin/phinx rollback

migrate-reset: ## Reset all migrations
	docker-compose exec app vendor/bin/phinx rollback -t 0

migrate-status: ## Check migration status
	docker-compose exec app vendor/bin/phinx status

migration-create: ## Create a new migration from entity class (usage: make migration-create entity=EntityName)
	@if [ "$(entity)" = "" ]; then \
		echo "Error: entity parameter is required. Usage: make migration-create entity=EntityName"; \
		exit 1; \
	fi
	docker-compose exec app php src/Core/DB/generate-migration.php $(entity)

seed: ## Seed the database
	docker-compose exec app vendor/bin/phinx seed:run

seed-rollback: ## Rollback the last seed
	docker-compose exec app vendor/bin/phinx seed:rollback

clean: ## Clean up Docker resources
	docker-compose down -v
	docker system prune -f

logs: ## View application logs
	docker-compose logs -f

shell: ## Open shell in the application container
	docker-compose exec app bash

composer-install: ## Install PHP dependencies
	docker-compose exec app composer install

composer-update: ## Update PHP dependencies
	docker-compose exec app composer update 