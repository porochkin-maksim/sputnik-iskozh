# Docker/Sail Execution Rule

Проект работает через Docker Compose и Laravel Sail. Это обязательное правило для Codex без исключений.

## Неумолимая обязанность

- Не запускать проектные PHP, Composer, Artisan, PHPUnit, Yarn, NPM, Node, Vite команды через локальное окружение хоста.
- Всё, что есть внутри Docker/Sail контейнера, запускать через `Makefile` или `./vendor/bin/sail`.
- Не использовать системные `php`, `composer`, `yarn`, `npm`, `node`, `vite`, если команда относится к проекту.
- Локально допустимы только shell/file операции, которые не зависят от runtime проекта: `rg`, `sed`, `find`, `git diff`, `git status`, `bash -n`, чтение файлов.

## Канонические команды

- Запуск приложения: `make up`
- Остановка приложения: `make down`
- Artisan: `make artisan <command>` или `./vendor/bin/sail artisan <command>`
- Composer: `make composer <command>` или `./vendor/bin/sail composer <command>`
- Тесты: `make tests` или `./vendor/bin/sail artisan test`
- Миграции: `make migrate`, `make migrate-rollback`, `make migrate-refresh`
- PHP one-off команды: `./vendor/bin/sail php <args>`
- Node one-off команды: `make node <args>` или `./vendor/bin/sail node <args>`
- Yarn: `make yarn <args>` или `./vendor/bin/sail yarn <args>`
- Frontend build с backend-generated contracts: `make yarn-build`
- Frontend dev/watch с backend-generated contracts: `make yarn-watch`
- Экспорт route functions: `make js-routes`
- Architecture guard: `make architecture` или `bash scripts/check-architecture.sh`

## Проверки перед завершением задач

- Backend-задачи проверять через Sail/Make: `make tests`, `make artisan test --filter ...`, `./vendor/bin/sail php -l <file>`.
- Frontend-задачи проверять через `make yarn-build`, а не через локальный `npm run build`.
- Если Docker/Sail не поднят, сначала использовать `make up`; не заменять это локальным PHP/Node окружением.
