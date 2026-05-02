.DEFAULT_GOAL := help
PHP ?= php
CONSOLE = $(PHP) bin/console

.PHONY: help start css css-watch importmap clear lint test phpstan cs-fix cs-check qa prod-build prod-cache-warm prod-cache-clear

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'

# ───── Dev ─────────────────────────────────────────────────────────────────────

start: ## Start Symfony local server
	symfony server:start --no-tls

css: ## Build Tailwind once (minified)
	$(CONSOLE) tailwind:build --minify

css-watch: ## Watch + rebuild Tailwind on change
	$(CONSOLE) tailwind:build --watch

importmap: ## Install/refresh JS deps into assets/vendor/
	$(CONSOLE) importmap:install

clear: ## Clear cache (dev)
	$(CONSOLE) cache:clear

# ───── QA ──────────────────────────────────────────────────────────────────────

lint: ## Lint container, twig and yaml
	$(CONSOLE) lint:container
	$(CONSOLE) lint:twig templates/
	$(CONSOLE) lint:yaml config/

test: ## Run PHPUnit suite
	vendor/bin/phpunit

phpstan: ## Run PHPStan static analysis
	vendor/bin/phpstan analyse --memory-limit=1G

cs-fix: ## Auto-fix code style
	vendor/bin/php-cs-fixer fix

cs-check: ## Check code style (dry-run)
	vendor/bin/php-cs-fixer fix --dry-run --diff

qa: lint cs-check phpstan test ## Run full QA suite

# ───── Prod ────────────────────────────────────────────────────────────────────

prod-build: ## Optimised composer install + warmup for prod deploy
	composer install --no-dev --optimize-autoloader --classmap-authoritative --apcu-autoloader --no-interaction
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) cache:clear
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) cache:warmup
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) tailwind:build --minify
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) importmap:install
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) asset-map:compile

prod-cache-clear: ## Clear all prod caches (HTTP cache + app cache)
	rm -rf var/cache/prod
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) cache:clear
	APP_ENV=prod APP_DEBUG=0 $(CONSOLE) cache:pool:clear cache.app cache.system 2>/dev/null || true

# ───── Production PHP-FPM tuning (php.ini snippet to copy on the server) ──────
#
# [opcache]
# opcache.enable=1
# opcache.enable_cli=0
# opcache.memory_consumption=256
# opcache.max_accelerated_files=20000
# opcache.validate_timestamps=0          ; disable in prod, force re-deploy to invalidate
# opcache.preload=/var/www/esp/config/preload.php
# opcache.preload_user=www-data
# opcache.jit=tracing
# opcache.jit_buffer_size=128M
#
# [apcu]
# apcu.enable=1
# apcu.enable_cli=1
# apcu.shm_size=64M
# apcu.ttl=3600
#
# [realpath_cache]
# realpath_cache_size=4096K
# realpath_cache_ttl=600
