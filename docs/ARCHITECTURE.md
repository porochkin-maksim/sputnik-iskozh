# Architecture Standard

Этот документ фиксирует обязательные архитектурные правила проекта.

Frontend правила для Laravel + Vue 3 зафиксированы отдельно в `FRONTEND_ARCHITECTURE.md`.

Обязательная автоматическая проверка backend/frontend architecture: `bash scripts/check-architecture.sh`.

## 0. Среда выполнения команд

Проект работает через Docker Compose и Laravel Sail. Для разработки и проверок использовать runtime из контейнера
`laravel.test`, а не локальное окружение хоста.

Обязательно:
- PHP/Composer/Artisan/PHPUnit запускать через `make` или `./vendor/bin/sail`;
- frontend tooling запускать через `make yarn ...`, `make yarn-build`, `make yarn-watch` или `./vendor/bin/sail yarn ...`;
- при необходимости поднять окружение через `make up`;
- для frontend build использовать `make yarn-build`, потому что он сначала обновляет generated frontend contracts;
- для тестов использовать `make tests` или `make artisan test --filter ...`;
- для PHP lint использовать `./vendor/bin/sail php -l <file>`.

Запрещено:
- запускать проектные команды через локальные `php`, `composer`, `yarn`, `npm`, `node`, `vite`;
- заменять Sail-команды локальным PHP 8.4 wrapper-ом;
- делать вывод о прохождении backend/frontend проверок по локальному окружению хоста.

Цель:
- держать бизнес-логику вне HTTP/framework слоя
- обеспечить единый application entrypoint для web, api, queue и cli
- не допускать деградации обратно в locator/container-driven код

## 1. Слои

### `core/Domains/*`

Содержит доменную модель:
- entities
- collections
- enums
- value objects
- repository interfaces
- domain services
- domain exceptions

Не содержит:
- Laravel container
- locator-ы
- Eloquent models
- HTTP request/response
- Blade/resources
- Facades

### `core/App/*`

Содержит application layer:
- commands
- input objects
- validators
- orchestration use cases
- transaction boundaries
- сценарные access checks

Это единственная точка входа в бизнес-сценарии.

### `app/*`

Содержит transport и infrastructure:
- controllers
- resources
- framework bindings
- repository implementations
- eloquent mappers
- queue integration
- framework-specific glue code

## 2. Основные правила

### Обязательно

- Каждый use case входит через `core/App/*`.
- Контроллеры, jobs, listeners, console commands не собирают бизнес-сценарии вручную.
- Валидация сценария живёт рядом с command в `core/App/*`.
- Любая операция записи оформляется отдельным command.
- Если сценарий нужен из web, api, queue или cron, используется один и тот же command.
- Repository interfaces живут в `core/Domains/*`.
- Repository implementations и mapper-ы живут в `app/*`.
- Mapper является единственным местом преобразования `repository data <-> domain entity`.
- Контроллер должен быть thin: получить primitive input, вызвать command, вернуть response.
- Job должен быть thin: вызвать command или application service, а не содержать длинную бизнес-логику.
- Transaction boundary живёт в application-layer, а не в controller.

### Желательно

- Если у command больше 3-4 аргументов, использовать typed input object.
- Validator должен вызываться внутри command.
- Для list/filter/search сценариев использовать отдельный query command, если там есть заметная orchestration.
- Domain entity должна постепенно забирать в себя инварианты, а не быть только контейнером данных.
- Для денег, периодов, статусов и похожих значимых понятий использовать value objects, если логика начинает разрастаться.
- Naming должен быть однозначным:
  - `*Command` для исполняемого application use case с зависимостями и методом `execute()`
  - `*Input` для typed input object, если аргументы сценария надо сгруппировать
  - `*CatalogService` для справочников
  - `*DomainService` только если это действительно доменный сервис

### Запрещено

- `app(...)` внутри `core/Domains/*`
- любые `*Locator` в новом коде
- `DB::transaction`, `beginTransaction`, `commit`, `rollBack` в контроллерах
- `Validator` и `validate()` в контроллерах как место бизнес-валидации
- ручная сборка domain entities в контроллерах
- прямой вызов repository из контроллера
- прямой вызов Eloquent model из `core/Domains/*`
- mapper-логика во factory
- методы вида `makeDtoFromObject`, `makeDtoFromObjects`, `makeModelFromDto`
- lazy loading в entity через container или locator
- HTTP/framework-specific типы в `core/Domains/*` и `core/App/*`

## 3. Правила по слоям

### Контроллеры

Контроллер:
- читает primitives из request
- вызывает один или несколько application commands
- возвращает response/resource/view

Контроллер не должен:
- валидировать бизнес-сценарий вручную
- создавать domain entities
- работать с repository напрямую
- открывать транзакции
- содержать значимую orchestration-логику

### Jobs

Job:
- вызывает один application command или один координирующий application service
- не должен быть основным местом бизнес-логики

Если job разрастается, логика переносится в `core/App/*`.

### Entities

Entity может:
- хранить состояние
- выполнять локальные бизнес-операции над собой
- проверять свои инварианты

Entity не может:
- ходить в container
- искать другие сущности через repository/service locator
- зависеть от Laravel или HTTP

### Mapper-ы

Mapper:
- переводит repository data в domain entity
- переводит domain entity в repository data

Mapper не должен:
- быть factory под другим именем
- содержать business rules
- зависеть от controller/request слоя

## 4. Entry Point Rule

Любой transport слой использует только application layer:
- HTTP -> `core/App/*`
- API -> `core/App/*`
- Queue -> `core/App/*`
- CLI -> `core/App/*`

Запрещено вызывать доменные сервисы из transport слоя как замену command-ам, если это уже полноценный use case.

## 5. Naming Rule

Использовать следующие соглашения:
- `*Entity` для доменных сущностей
- `*Collection` для доменных коллекций
- `*RepositoryInterface` для доменных контрактов репозитория
- `*EloquentRepository` для инфраструктурной реализации
- `*EloquentMapper` для инфраструктурного маппинга
- `*Command` для исполняемого application use case
- `*Validator` для application validation
- `*Input` для typed input object
- `*Searcher` и `*SearchResponse` только как query contract, а не как замена domain model

Не использовать новые имена:
- `*DTO` для доменных сущностей
- `*Dto`/`*DTO` для input objects в `core/App/*`; использовать `*Input`
- `*Request` для input objects в `core/App/*`; `Request` остаётся HTTP-слоем `app/Http/Requests/*`
- `*Bag` для typed input objects; `Bag` допустим только для реально generic key-value контейнера
- `*Handler` для application use cases в `core/App/*`; use case должен называться `*Command` и иметь `execute()`
- `*Locator`
- `*UseCase` внутри bounded context, если use case уже перенесён в `core/App/*`

## 6. Migration Rule

При рефакторинге legacy-кода приоритет такой:
1. убрать `app(...)` и locator-style зависимости из `core`
2. вынести orchestration в `core/App/*`
3. перенести validation в commands
4. перевести infrastructure mapping на отдельные mapper-ы
5. дочистить naming

## 7. Архитектурные проверки

Минимальный набор проверок, который должен поддерживаться:
- в `core/Domains/*` нет `app(`
- в `core/App/*` нет locator-ов
- в `app/Http/Controllers/*` нет `validate(` и `Validator`
- в `app/Http/Controllers/*` нет `DB::transaction`
- в проекте нет новых `makeDtoFromObject`, `makeDtoFromObjects`, `makeModelFromDto`

Если правило нарушается, это считается архитектурным регрессом, а не допустимой локальной оптимизацией.

## 8. Pragmatic Note

Правила нужны не ради формальной "чистоты", а чтобы:
- один и тот же сценарий одинаково работал из web/api/queue
- код было проще тестировать
- bounded contexts не протекали в framework-детали
- новые рефакторинги были дешевле, чем поддержка legacy-паттернов

## 9. Billing Model Invariants

Биллинг проекта опирается на несколько устойчивых инвариантов:
- `REGULAR` invoice — это контейнер основных периодных начислений для `account + period`;
- `INCOME` и `OUTCOME` используются для дополнительных или сервисных документов, но не заменяют `REGULAR`;
- счёт может иметь `serviceId` как необязательную связь с `ServiceEntity`, если документ относится к конкретной услуге;
- `serviceId` не обязателен для каждого счета и не должен использоваться как замена `ClaimEntity::serviceId`;
- `ClaimEntity` остаётся атомарной строкой начисления, а не местом агрегации разных событий;
- поиск/создание счета должен учитывать `accountId + periodId + type + serviceId`, если `serviceId` задан;
- `CheckClaimForCounterChangeCommand` не должен складывать электрические начисления в `REGULAR`; для них нужен отдельный сервисный invoice с ясной привязкой к услуге;
- направление денежных потоков по-прежнему определяется бизнес-ролью аккаунта и типом документа, а не только названием услуги.

Подробный рабочий конспект по модели счета и service-link хранится в [docs/billing-invoice-model.md](docs/billing-invoice-model.md).
