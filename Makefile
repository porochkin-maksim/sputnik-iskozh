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

#.PHONY: build
#build: ## сборка приложения
#	@chmod -R u+x ./scripts/
#	@./scripts/install.sh
#	@./vendor/bin/sail composer install
#	@make up

.PHONY: up
up: ## запуск приложения
	@./vendor/bin/sail up -d

.PHONY: down
down: ## остановка приложения
	@./vendor/bin/sail stop
	@docker-compose down -v

.PHONY: restart
restart: ## перезапуск приложения
	@./vendor/bin/sail stop
	@./vendor/bin/sail up -d

.PHONY: sail
sail: ## запуск sail
	@./vendor/bin/sail $(filter-out $@,$(MAKECMDGOALS))

.PHONY: artisan
artisan: ## запуск artisan
	@./vendor/bin/sail artisan $(filter-out $@,$(MAKECMDGOALS))

.PHONY: php
php: ## запуск php
	@./vendor/bin/sail bash

.PHONY: node
node: ## запуск node
	@./vendor/bin/sail node $(filter-out $@,$(MAKECMDGOALS))

.PHONY: yarn
yarn: ## запуск yarn
	@./vendor/bin/sail yarn $(filter-out $@,$(MAKECMDGOALS))

.PHONY: yarn-watch
yarn-watch: ## прослушивать фронт
	@./vendor/bin/sail artisan front:export-route-list-command
	@./vendor/bin/sail yarn run dev

.PHONY: yarn-build
yarn-build: ## собрать фронт
	@./vendor/bin/sail artisan front:export-route-list-command
	@./vendor/bin/sail yarn run build

.PHONY: routes
routes: ## Выгрузить маршруты с бэка
	@./vendor/bin/sail artisan front:export-route-list-command

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

# empty action
%:
	@:

#CASHBOX_SSL_DIR := $(shell dirname ${CASHBOX_SSL_CERTIFICATE_PATH})
#AEROFLOT_SSL_DIR := $(shell dirname ${AEROFLOT_SSL_CERTIFICATE_PATH})
#
## define os
#OS_NAME := $(shell uname -s | tr A-Z a-z)
#
#.PHONY: configure
#configure: ## генерация .env файла
#	@./scripts/configure.sh
#
#.PHONY: reconfigure
#reconfigure: ## ре-генерация .env файла
#	@./scripts/configure.sh reconfigure
#
#.PHONY: build
#build: ## запуск установщика
#ifeq ($(OS_NAME), linux)
#	@exec sg docker "make build-main" || echo "Ошибка"
#endif
#
#ifeq ($(OS_NAME), darwin)
#	@make build-main
#endif
#
#
#.PHONY: build-main
#build-main: ## сборка образов и приложения
#	@printf '\033[36mСоздание директорий\033[0m\n'
#	@mkdir -p -m 755 ./var
#	@if ! [ -w ./var ] ; then printf '\033[31mfailed to create %s\033[0m\n' "./var" ; exit 1 ; fi
#	@printf '\033[36mСоздание basic auth для monolith-api-v2\033[0m\n'
#	@make generate-monolith-api-htpasswd
#	@printf '\033[36mГенерация документации monolith-api-v2\033[0m\n'
#	@make generate-monolith-api-documentation
#	@printf '\033[36mСоздание директории ssl ключей для кассы\033[0m\n'
#	@mkdir -p -m 700 "${CASHBOX_SSL_DIR}"
#	@if ! [ -w "${CASHBOX_SSL_DIR}" ] ; then printf '\033[31mfailed to create %s\033[0m\n' "${CASHBOX_SSL_DIR}" ; exit 1 ; fi
#	@printf '\033[36mСоздание директории ssl ключей для аерофлота\033[0m\n'
#	@mkdir -p -m 700 "${AEROFLOT_SSL_DIR}"
#	@if ! [ -w "${AEROFLOT_SSL_DIR}" ] ; then printf '\033[31mfailed to create %s\033[0m\n' "${AEROFLOT_SSL_DIR}" ; exit 1 ; fi
#	@printf '\033[36mСоздание ключа и сертификата для кассы\033[0m\n'
#	@echo "${CASHBOX_SSL_PRIVATE_KEY_BASE64}" | base64 --decode > "${CASHBOX_SSL_PRIVATE_KEY_PATH}"
#	@echo "${CASHBOX_SSL_CERTIFICATE_BASE64}" | base64 --decode > "${CASHBOX_SSL_CERTIFICATE_PATH}"
#	@printf '\033[36mСоздание сертификата для аерофлота\033[0m\n'
#	@echo "${AEROFLOT_SSL_CERTIFICATE_BASE64}" | base64 --decode > "${AEROFLOT_SSL_CERTIFICATE_PATH}"
#	@printf '\033[36mСборка образов\033[0m\n'
#	@DOCKER_BUILDKIT=1 docker build \
#		--pull \
#		--no-cache \
#		--build-arg=USER_ID=${DOCKER_UID} \
#		--build-arg=GROUP_ID=${DOCKER_GID} \
#		-t "${SERVICE_NAME}-${LOCAL_BASE_DOMAIN_SLUG}" \
#		-f build/monolith/Dockerfile.80 \
#		--target=dev \
#		.
#	@printf '\033[36mПроверка и создание сетей Docker \033[0m\n'
#	@make create-docker-networks
#	@printf '\033[36mЗапуск контейнеров\033[0m\n'
#	@docker-compose up -d
#	@printf '\033[36mИнициализация локального s3\033[0m\n'
#	@docker run --rm -it "registry.bronevik.space/library/minio-bronevik:latest" sh -c "mc config host add bronevik http://s3.me.bronevik.space ${FILE_STORAGE_S3_KEY} ${FILE_STORAGE_S3_SECRET} && mc mb --ignore-existing bronevik/${FILE_STORAGE_S3_BUCKET_DOCUMENTS} bronevik/${FILE_STORAGE_S3_BUCKET_UPLOADS} bronevik/${FILE_STORAGE_S3_BUCKET_CACHE} bronevik/${FILE_STORAGE_S3_BUCKET_COMMON_CACHE} bronevik/${FILE_STORAGE_S3_BUCKET_FIN_INVOICE} bronevik/${FILE_STORAGE_S3_BUCKET_HBA_HOTEL_DUMPS}"
#	@printf '\033[36mСборка приложения\033[0m\n'
#	@./scripts/build-php8.sh "composer --working-dir=/srv install && composer --working-dir=/srv/api/supplier install && composer --working-dir=/srv/api/monolith install && composer --working-dir=/srv/api/monolith_v2 install && composer --working-dir=/srv/api/service/offers install && composer --working-dir=/srv/api/hotels/ install && composer --working-dir=/srv/app/bronevik/ install && composer --working-dir=/srv/app/avrora/ install && composer --working-dir=/web/bronevik/app/secure/ install && cd /srv && yarn install && yarn compile-text && yarn run development"
#	@printf '\033[36mИнициализация тестовой базы\033[0m\n'
#	@docker-compose exec php ./scripts/init-test-db.sh
#	@printf '\033[36mВыполнение миграций\033[0m\n'
#	@docker-compose exec php ./scripts/migrate.sh
#	@printf '\033[36mКомпиляция файлов переводов\033[0m\n'
#	@./scripts/build-php8.sh "/srv/config/CI/translate/lang_update_mo.sh"
#	@printf '\033[36mВыдаю необходимые права\033[0m\n'
#	@./scripts/set-var-pemissions-to-current-user.sh
#	@printf '\033[36mГенерация protobuf классов для monolith-api-v2\033[0m\n'
#	@make generate-monolith-api-protobuf-classes
#
#.PHONY: build-prod
#build-prod:
#	@DOCKER_BUILDKIT=1 docker build \
#		--pull \
#		--ssh default=${SSH_AUTH_SOCK} \
#		-t "${SERVICE_NAME}-production" \
#		-f build/monolith/Dockerfile.80 \
#		--target=prod \
#		.
#
#.PHONY: up
#up: ## (пере)сборка образов и запуск приложения
#	@printf '\033[36mСборка образов\033[0m\n'
#	@DOCKER_BUILDKIT=1 docker build \
#		--pull \
#		--build-arg=USER_ID=${DOCKER_UID} \
#        --build-arg=GROUP_ID=${DOCKER_GID} \
#		-t "${SERVICE_NAME}-${LOCAL_BASE_DOMAIN_SLUG}" \
#		-f build/monolith/Dockerfile.80 \
#		--target=dev \
#		.
#	@printf '\033[36mЗапуск контейнеров\033[0m\n'
#	@docker-compose up -d
#
#.PHONY: start
#start: ## запуск приложения
#	@docker-compose start
#
#.PHONY: stop
#stop: ## остановка приложения
#	@docker-compose stop
#
#.PHONY: restart
#restart: ## перезапуск приложения
#	@docker-compose stop
#	@docker-compose start
#
#.PHONY: migrate
#migrate: ## выполнение миграций
#	@printf '\033[36mВыполнение миграций\033[0m\n'
#	@docker-compose exec php ./scripts/migrate.sh
#
#.PHONY: migration-create
#migration-create: ## Создание новой миграции (пример: migration-create db=finfin AlterSomeTable)
#	@printf '\033[36mСоздание новой миграции в БД \033[0m\n'
#	@./scripts/build-php8.sh "cd /srv && ./vendor/bin/phinx --configuration=database/$(db)/phinx.php create $(filter-out $@,$(MAKECMDGOALS))"
#
#.PHONY: migration-rollback
#migration-rollback: ## Откатить миграцию (пример: migration-rollback db=finfin)
#	@printf '\033[36mСоздание новой миграции в БД \033[0m\n'
#	@./scripts/build-php8.sh "cd /srv && ./vendor/bin/phinx --configuration=database/$(db)/phinx.php rollback $(filter-out $@,$(MAKECMDGOALS))"
#
#.PHONY: composer
#composer: ## запуск composer'а
#	@./scripts/build-php8.sh "cd /srv && composer $(filter-out $@,$(MAKECMDGOALS))"
#
#.PHONY: yarn
#yarn: ## запуск yarn'а
#	@./scripts/build-php8.sh "cd /srv && yarn $(filter-out $@,$(MAKECMDGOALS))"
#
#.PHONY: mc
#mc:
#	@docker-compose exec minio mc $(filter-out $@,$(MAKECMDGOALS))
#
#.PHONY: mc-init
#mc-init:
#	@docker-compose exec minio mc config host add bronevik http://s3.${LOCAL_BASE_DOMAIN} ${FILE_STORAGE_S3_KEY} ${FILE_STORAGE_S3_SECRET}
#	@for bucket in "${FILE_STORAGE_S3_BUCKET_DOCUMENTS}" "${FILE_STORAGE_S3_BUCKET_UPLOADS}" "${FILE_STORAGE_S3_BUCKET_CACHE}" "${FILE_STORAGE_S3_BUCKET_COMMON_CACHE}" "${FILE_STORAGE_S3_BUCKET_FIN_INVOICE}" "${FILE_STORAGE_S3_BUCKET_HBA_HOTEL_DUMPS}" ; do \
#		if ! [ -d ./var/s3/$${bucket} ] ; then docker-compose exec minio mc mb bronevik/$${bucket} ; fi \
#	done
#
#.PHONY: tests
#tests: ## запуск тестов
#	@docker-compose exec php bash -c "echo 'Tests in progress, please wait...'"
#	@docker-compose exec php bash -c 'result=$$(cd /srv && ./vendor/bin/phpunit); \
#		 echo "$$result"; \
#		 echo $$result | grep -q "Notice:\|error\|errors\|failure\|failures" && echo "Errors found in test code (Notices, Warnings). Break." && exit 1 || exit 0'
#
#	@docker-compose exec php bash -c "cd /srv && ./vendor/bin/phpunit ./api/monolith/tests -c ./api/monolith/phpunit.xml"
#	@docker-compose exec php bash -c "cd /srv && ./vendor/bin/phpunit ./api/monolith_v2/tests -c ./api/monolith_v2/phpunit.xml"
#	@docker-compose exec php bash -c "cd /srv && ./vendor/bin/phpunit ./api/service/offers/tests -c ./api/service/offers/phpunit.xml"
#	@docker-compose exec php bash -c "cd /srv && ./vendor/bin/phpunit ./app/bronevik/core/tests -c ./app/bronevik/phpunit.xml"
#	@docker-compose exec php bash -c "cd /srv && ./vendor/bin/phpunit ./app/avrora/core/tests -c ./app/avrora/phpunit.xml"
#	@docker-compose exec php bash -c "cd /web/bronevik/ && ./vendor/bin/phpunit ./app/secure/core/tests -c ./app/secure/phpunit.xml"
#	@docker-compose exec php bash -c "cd /srv && ./vendor/bin/phpunit ./api/hotels/core/tests -c ./api/hotels/phpunit.xml"
#
#.PHONY: bash-php
#bash-php: ## запуск шелла в php контейнере
#	@docker-compose exec php bash
#
#.PHONY: bash-nginx
#bash-nginx: ## запуск шелла в nginx контейнере
#	@docker-compose exec nginx bash
#
#.PHONY: logs-php
#logs-php: ## просмотр логов php контейнера
#	@docker-compose logs -f php
#
#.PHONY: logs-nginx
#logs-nginx: ## просмотр логов nginx контейнера
#	@docker-compose logs -f nginx
#
#.PHONY: translate
#translate: ## генерация переводов
#	@./scripts/build-php8.sh "/srv/config/CI/translate/lang_update_mo.sh"
#
#.PHONY: debug-enable
#debug-enable: ## включение дебага (xdebug и pcov)
#ifeq ($(OS_NAME), linux)
#	@docker-compose exec -u root php ./scripts/xdebug/xdebug-switch.sh "debug"
#endif
#
#ifeq ($(OS_NAME), darwin)
#	@docker-compose exec -u root php ./scripts/xdebug/xdebug-switch-mac.sh "debug"
#endif
#	@make restart
#
#.PHONY: debug-disable
#debug-disable: ## выключение дебага (xdebug и pcov)
#	@docker-compose exec -u root php ./scripts/xdebug/xdebug-switch.sh "disable"
#	@make restart
#
#.PHONY: profile-enable
#profile-enable: ## включение дебага (xdebug и pcov)
#	@docker-compose exec -u root php ./scripts/xdebug/xdebug-switch.sh "profile"
#	@make restart
#
#.PHONY: profile-disable
#profile-disable: ## выключение дебага (xdebug и pcov)
#	@docker-compose exec -u root php ./scripts/xdebug/xdebug-switch.sh "disable"
#	@make restart
#
#.PHONY: print-url
#print-url: ## просмотр URL'ов текущего проекта
#	@for domain in monolith avrora bronevik hotels-api offers-api secure xml s3 metrics ; do sed -n -e "s|\(^LOCAL_BASE_DOMAIN=\)\(.\{1,\}\)|$$domain.\2|p" .env ; done
#
#.PHONY: print-db-all
#print-db-all: ## просмотр адресов всех активных баз
#	@docker inspect --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}:3306	{{ index .Config.Labels "com.docker.compose.project.working_dir" }}	{{ index .Config.Labels "com.docker.compose.project" }}-{{index .Config.Labels "com.docker.compose.service" }}' $$(docker ps -q --filter "expose=3306")
#
#.PHONY: help
#help:
#	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
#
#.PHONY: prepare-before-run-hba-tests
#prepare-before-run-hba-tests: ## подготовка к запуску HBA тестов локально
#	@docker-compose exec php bash -c "/srv/scripts/init-test-db.sh && /srv/database/phinx_all migrate -e test"
#	@docker cp ./config/CI/tests/hba-vat/dump.sql $$(docker-compose ps -q dbtest):/tmp
#	@docker-compose exec dbtest bash -c "mysql -u ${TEST_DB_USER} -p${TEST_DB_PASSWORD} < /tmp/dump.sql"
#	@echo "change DB_USER=${TEST_DB_USER} and DB_PASSWORD=${TEST_DB_PASSWORD} in your .env file"
#
#.PHONY: generate-api-diagrams
#generate-api-diagrams: ## генерация диаграмм для API документации
#	@sh ./api/hotels/cli/build-doc.sh $(filter-out $@,$(MAKECMDGOALS))
#
#.PHONY: generate-api-classes
#generate-api-classes: ## генерация классов и карты классов из wsdl
#	@sh ./api/hotels/cli/build-classes.sh $(filter-out $@,$(MAKECMDGOALS))
#
#.PHONY: generate-offers-api-protobuf-classes
#generate-offers-api-protobuf-classes: ## генерация классов из proto схемы для offers-api
#	@[ "${dir}" ] || ( echo ">> usage: generate-offers-api-protobuf-classes dir=/path/to/offers/api/lib"; exit 1 )
#	@./scripts/build-php8.sh "cd /srv && protoc --php_out=./var/protobuf ./api/service/offers/schema.proto"
#	@cp ./api/service/offers/schema.proto "${dir}"
#	@cp -r ./var/protobuf/BronevikLibs/OffersAPI/Protobuf "${dir}/src"
#
#.PHONY: generate-monolith-api-htpasswd
#generate-monolith-api-htpasswd: ## создание htpasswd для v2 monolith-api
#	@sh ./scripts/api-monolith-htpasswd-generate.sh
#
#.PHONY: generate-monolith-api-protobuf-classes
#generate-monolith-api-protobuf-classes: ## генерация классов из proto схемы для monolith-api
#	@./scripts/build-php8.sh "sh /srv/api/monolith_v2/scripts/generate-protobuf-classes.sh"
#
#.PHONY: generate-monolith-api-documentation
#generate-monolith-api-documentation: ## генерация документации из proto файла
#	@sh api/monolith_v2/scripts/generate-gnostic-documentation.sh
#
#.PHONY: create-docker-networks
#create-docker-networks: ## создание сетей docker
#	@sh scripts/create-docker-networks.sh
#
#.PHONY: generate-js-enums
#generate-js-enums: ## генерация enum для js
#	@./scripts/build-php8.sh "php /srv/config/frontend/generate-enums-for-js"
#
## empty action
#%:
#	@:
