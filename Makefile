SHELL := /usr/bin/env bash
PWD = $(shell pwd)
ROOT_DIR := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))

DOCKER_COMPOSE = cd Tests/Sandbox/Compose && COMPOSE_DOCKER_CLI_BUILD=0 COMPOSE_PROJECT_NAME=smartybundle docker-compose
DOCKER_COMPOSE_PROD = $(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.prod.yml
DC_BUILD_OPTS = --pull

default: help

help: ## The help text you're reading
	@grep --no-filename -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

### Sandbox/Compose Docker-Compose targets ###
dc-build-all: ## Builds all the Docker images
	$(MAKE) dc-build-php8-sf5
	$(MAKE) dc-build-php8-sf4
	$(MAKE) dc-build-php7-sf5
	$(MAKE) dc-build-php7-sf4
	$(MAKE) dc-build-php7-sf3
	docker images | grep smartybundle-sandbox
.PHONY: dc-build-all

dc-build-php8-sf5: ## Builds the php8-sf5 Docker images
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php8-sf5

dc-build-php8-sf4: ## Builds the php8-sf4 Docker images
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php8-sf4

dc-build-php7-sf5: ## Builds the php7-sf5 Docker images
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php7-sf5

dc-build-php7-sf4: ## Builds the php7-sf4 Docker images
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php7-sf4

dc-build-php7-sf3: ## Builds the php7-sf3 Docker images
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php7-sf3

dc-config: ## Dumps the default docker-compose config
	@$(DOCKER_COMPOSE) config
.PHONY: dc-config

dc-up-php8-sf5: ## Start the php8-sf5 docker hub
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) up
.PHONY: dc-up-php7-sf4

dc-up-php8-sf4: ## Start the php8-sf4 docker hub
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) up
.PHONY: dc-up-php7-sf4

dc-up-php7-sf5: ## Start the php7-sf5 docker hub
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) up
.PHONY: dc-up-php7-sf4

dc-up-php7-sf4: ## Start the php7-sf3 docker hub
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) up
.PHONY: dc-up-php7-sf4

dc-up-php7-sf3: ## Start the php7-sf3 docker hub in detached mode (no logs)
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE) up --detach
.PHONY: dc-up-php7-sf3

dc-run-php8-sf5: ## Get a shell in a php8-sf5 container
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php8-sf5

dc-run-php8-sf4: ## Get a shell in a php8-sf4 container
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php8-sf4

dc-run-php7-sf4: ## Get a shell in a php7-sf4 container
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php7-sf4

dc-run-php7-sf3: ## Get a shell in a php7-sf3 container
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php7-sf3

### Sandbox/Simple Docker targets ###
build-php7-sf3: ## Build the php7-sf3 test image
	docker build . -f Tests/Sandbox/Simple/Dockerfile --no-cache --build-arg PHP_VERSION=7.4 --build-arg SYMFONY_VERSION=3.4 -t noiselabs/smarty-bundle-testing:php7-sf3
.PHONY: build-php7-sf3

build-php7-sf4: ## Build the php7-sf4 test image
	docker build . -f Tests/Sandbox/Simple/Dockerfile --no-cache --build-arg PHP_VERSION=7.4 --build-arg SYMFONY_VERSION=4.4 -t noiselabs/smarty-bundle-testing:php7-sf4
.PHONY: build-php7-sf4

build-php8-sf4: ## Build the php8-sf4 test image
	docker build . -f Tests/Sandbox/Simple/Dockerfile --no-cache --build-arg PHP_VERSION=8.0 --build-arg SYMFONY_VERSION=4.4 -t noiselabs/smarty-bundle-testing:php8-sf4
.PHONY: build-php8-sf4

build: ## Build Docker images
	$(MAKE) build-php7-sf3 build-php7-sf4 build-php8-sf4
.PHONY: build

build-parallel: ## Build Docker images in parallel
	$(MAKE) -j4 build-php7-sf3 build-php7-sf4 build-php8-sf4
.PHONY: build-parallel

test-php7-sf3: ## Run unit and functional tests in the php7-sf3 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php7-sf3 composer test
.PHONY: test-php7-sf3

test-php7-sf4: ## Run unit and functional tests in the php7-sf4 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php7-sf4 composer test
.PHONY: test-php7-sf4

test-php8-sf4: ## Run unit and functional tests in the php8-sf4 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php8-sf4 composer test
.PHONY: test-php8-sf4

test: ## Run unit and functional tests
	$(MAKE) test-php70 test-php71 test-php72 test-php73

test-parallel: ## Run unit and functional tests in parallel
	$(MAKE) -j4 test-php71 test-php72 test-php73 test-php80

sh-php7-sf3: ## Get a shell in the php7-sf3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php7-sf3 sh
.PHONY: sh-php7-sf3

sh-php7-sf4: ## Get a shell in the php7-sf4 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php7-sf4 sh
.PHONY: sh-php7-sf4

sh-php8-sf4: ## Get a shell in the php8-sf4 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php8-sf4 sh
.PHONY: sh-php8-sf4

web-php7-sf3: ## Launches the built-in PHP web server in the php7-sf3  container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8073:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle-testing:php7-sf3 php -S 0.0.0.0:8080 -t web
.PHONY: web-php7-sf3

web-php7-sf4: ## Launches the built-in PHP web server in the php7-sf4  container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8074:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle-testing:php7-sf4 php -S 0.0.0.0:8080 -t web
.PHONY: web-php7-sf4

web-php8-sf4: ## Launches the built-in PHP web server in the php8-sf4  container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8084:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle-testing:php8-sf4 php -S 0.0.0.0:8080 -t web
.PHONY: web-php8-sf4

### Coding Standards ###
php-cs-fixer-dry-run: ## Check for PHP Coding Standards fixes but don't apply changes
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --dry-run --diff -v
.PHONY: php-cs-fixer-dry-run

php-cs-fixer-apply: ##  Run the PHP Coding Standards Fixer (PHP CS Fixer)
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff -v
.PHONY: php-cs-fixer-apply
