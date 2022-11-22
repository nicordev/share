# use variables from Symfony's .env file
include .env
export

DOCKER_COMPOSE = docker compose
EXEC = $(DOCKER_COMPOSE) exec
EXEC_APPLICATION = $(EXEC) application
EXEC_APPLICATION_BIN_CONSOLE = $(EXEC_APPLICATION) bin/console
CA_CERTIFICATES_DIRECTORY = devops/caddy/ca-certificates

ifdef filter
	TEST_FILTER = --testdox --filter=$(filter)
endif

ifdef level
	PHPSTAN_LEVEL = --level=${level}
endif

##
## Installation
##

.env.local: .env
	@if [ -f .env.local ]; then \
		echo "\033[33m.env has been modified:\033[0m please update .env.local accordingly (this message will only appear once)."; \
		touch .env.local; \
		exit 1; \
  	fi; \
  	echo "creating .env.local from .env, please retry your last command."; \
  	cp .env .env.local; \
  	exit 1

# .env.local must be before this include
include .env.local
export

.PHONY: build
build: ## build images
build:
	$(DOCKER_COMPOSE) pull --ignore-pull-failures
	$(DOCKER_COMPOSE) build

.PHONY: install
install: ## install the project
install: .env.local ca-certificates build start vendor database

##
## Run
##

.PHONY: start
start: ## start the containers
	$(DOCKER_COMPOSE) up --remove-orphans --force-recreate --detach

.PHONY: stop
stop: ## stop the containers gracefully by sending a SIGTERM signal first, then a few seconds later a SIGKILL signal
	$(DOCKER_COMPOSE) stop

.PHONY: kill
kill: ## stop containers immediately by sending a SIGKILL signal
	$(DOCKER_COMPOSE) kill

.PHONY: down
down: ## stop containers and remove containers, networks, volumes, images created by start
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

.PHONY: restart
restart: ## restart the containers
restart: stop start

.PHONY: reset
reset: ## recreate the project
reset: kill down install

##
## Caddy
##

.PHONY: ca-certificates
ca-certificates: ## generate certificates to use https
	@echo 'Generating new certificates. Existing certificates will be removed.'; \
	rm $(CA_CERTIFICATES_DIRECTORY)/*.pem; \
	cd "$(CA_CERTIFICATES_DIRECTORY)" && \
	mkcert ${SERVER_NAME}

.PHONY: logs_caddy
logs_caddy: ## show caddy's logs
	$(DOCKER_COMPOSE) logs caddy --follow

.PHONY: logs_caddy_snapshot
logs_caddy_snapshot: ## show last caddy's logs only, useful to copy
	$(DOCKER_COMPOSE) logs caddy

##
## Application
##

.PHONY: shell_application
shell_application: ## open a terminal in php container
	$(EXEC_APPLICATION) bash || $(EXEC_APPLICATION) sh

vendor: composer.lock
	$(EXEC_APPLICATION) composer install

composer.lock: composer.json
	@echo "\033[33mcomposer.json has been modified:\033[0m please run \033[35mmake composer-udpate\033[0m (this message will only appear once)."; \
	touch composer.lock; \
	exit 1

.PHONY: composer-update
composer-update: ## execute composer update in php container, add arg=packageNameHere to target a specific package
	$(EXEC_APPLICATION) composer update ${arg}

##
## Database
##

.PHONY: database
database: ## create or recreate the database
database: database-create migrate fixtures

.PHONY: database-create
database-create:
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:database:drop --force --if-exists
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:database:create

.PHONY: migration
migration: ## create a migration file
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:migration:diff --formatted

.PHONY: migrate
migrate: ## run migrations
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:migration:migrate --no-interaction

.PHONY: fixtures
fixtures: ## load fixtures
	$(EXEC_APPLICATION_BIN_CONSOLE) hautelook:fixtures:load --no-interaction --purge-with-truncate

##
## Test
##

.PHONY: test-database
test-database:
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:database:drop --force --if-exists --env=test
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:database:create --env=test
	$(EXEC_APPLICATION_BIN_CONSOLE) doctrine:migration:migrate --no-interaction --env=test
	$(EXEC_APPLICATION_BIN_CONSOLE) hautelook:fixtures:load --no-interaction --purge-with-truncate --env=test

.PHONY: test
test: ## run tests
test: #test-database
	$(EXEC_APPLICATION) bin/phpunit $(TEST_FILTER)

##
## Tools
##

.PHONY: qa
qa: ## check code quality
qa: php-cs-fixer-check phpstan

.PHONY: php-cs-fixer-check
php-cs-fixer-check: ## show php cs fixer report
php-cs-fixer-check:
	$(EXEC_APPLICATION) vendor/bin/php-cs-fixer fix --verbose --dry-run

.PHONY: php-cs-fixer
php-cs-fixer: ## fix files using php-cs-fixer
php-cs-fixer:
	$(EXEC_APPLICATION) vendor/bin/php-cs-fixer fix --verbose

.PHONY: phpstan
phpstan: ## run PHPStan
	$(EXEC_APPLICATION) vendor/bin/phpstan analyse ${PHPSTAN_LEVEL}

##
## Convenience
##

.PHONY: browse
browse: ## browse the project's homepage
	@echo https://${SERVER_NAME}:447

.DEFAULT_GOAL := help
.PHONY: help
help: ## describe targets
	@grep -E '(^[a-z0-9A-Z_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
