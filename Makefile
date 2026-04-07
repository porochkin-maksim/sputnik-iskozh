.DEFAULT_GOAL := help

include .env
-include local.mk

DIR := ${CURDIR}

.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: install-docker
install-docker: ## установка docker
ifneq ($(OS_NAME), darwin)
	@./scripts/install-docker-linux.sh
else ifeq ($(filter arm%,$(ARCH)),)
	@./scripts/install-docker-mac.sh
else
	@./scripts/install-docker-linux.sh
endif

.PHONY: build
build: ## собрать контейнеры
	@./scripts/install.sh
	@docker compose build
	@#docker compose build --no-cache
	@./vendor/bin/sail up -d
	@./vendor/bin/sail composer install
	@./vendor/bin/sail yarn -i
	@./vendor/bin/sail yarn build

.PHONY: up
up: ## запуск приложения
	@./vendor/bin/sail up -d
	@./vendor/bin/sail artisan cache:clear-all

.PHONY: down
down: ## остановка приложения
	@./vendor/bin/sail stop
	@docker compose down -v

.PHONY: restart
restart: ## перезапуск приложения
	@./vendor/bin/sail stop
	@./vendor/bin/sail up -d
	@./vendor/bin/sail artisan cache:clear-all

.PHONY: sail
sail: ## запуск sail
	@./vendor/bin/sail $(filter-out $@,$(MAKECMDGOALS))

.PHONY: artisan
artisan: ## запуск artisan
	@./vendor/bin/sail artisan $(filter-out $@,$(MAKECMDGOALS))

.PHONY: tests
tests: ## запуск artisan
	@./vendor/bin/sail artisan test

.PHONY: php
php: ## запуск php
	@./vendor/bin/sail bash

.PHONY: mysql
mysql: ## запуск php
	@./vendor/bin/sail mysql -e "SHOW VARIABLES LIKE 'datadir';"

.PHONY: import-sql
import-sql: ## импорт SQL дампа
	@if [ -z "$(FILE)" ]; then \
		echo "Ошибка: не указан файл дампа. Используйте: make import-sql FILE=имя_файла.sql"; \
		exit 1; \
	fi
	@if [ ! -f "$(FILE)" ]; then \
		echo "Ошибка: файл $(FILE) не найден"; \
		exit 1; \
	fi
	@echo "Импорт SQL дампа из файла $(FILE)"
	./vendor/bin/sail mysql -u root -p < $(FILE)

.PHONY: node
node: ## запуск node
	@./vendor/bin/sail node $(filter-out $@,$(MAKECMDGOALS))

.PHONY: yarn
yarn: ## запуск yarn
	@./vendor/bin/sail yarn $(filter-out $@,$(MAKECMDGOALS))

.PHONY: yarn-watch
yarn-watch: ## прослушивать фронт
	@./vendor/bin/sail artisan front:export-route-list-command
	@./vendor/bin/sail artisan front:export-route-functions-list-command
	@./vendor/bin/sail yarn run dev

.PHONY: yarn-build
yarn-build: ## собрать фронт
	@./vendor/bin/sail artisan front:export-route-list-command
	@./vendor/bin/sail artisan front:export-route-functions-list-command
	@./vendor/bin/sail yarn run build

.PHONY: js-routes
js-routes: ## Выгрузить маршруты с бэка
	@./vendor/bin/sail artisan front:export-route-functions-list-command

.PHONY: composer
composer: ## запуск composer
	@./vendor/bin/sail composer $(filter-out $@,$(MAKECMDGOALS))

.PHONY: migrate
migrate: ## выполнить миграции
	@./vendor/bin/sail artisan migrate

.PHONY: migrate-rollback
migrate-rollback: ## откатить миграции
	@./vendor/bin/sail artisan migrate:rollback

.PHONY: migrate-refresh
migrate-refresh: ## откатить миграции
	@./vendor/bin/sail artisan migrate:refresh

.PHONY: volume-rm
volume-rm: ## удалить "docker volumes" проекта
	@docker volume rm laravel_sail-meilisearch laravel_sail-mysql laravel_sail-redis

.PHONY: remove
remove-all: ## Удалить все контейнеры и образы
	@docker compose down -v --rmi all

.PHONY: create-domain
create-domain: ## создать структуру домена в Core/Domains (используй: make create-domain NAME=SomeObject)
	@if [ -z "$(NAME)" ]; then \
		echo "Ошибка: не указано имя домена. Используйте: make create-domain NAME=SomeObject"; \
		exit 1; \
	fi
	@./scripts/create-domain.sh -n $(NAME)

# empty action
%:
	@:
