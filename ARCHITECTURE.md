# Architecture Standard

Этот документ фиксирует обязательные архитектурные правила проекта.

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
  - `*Command` для application use case
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
- `*Command` для application use case
- `*Validator` для application validation
- `*Input` для typed input object
- `*Searcher` и `*SearchResponse` только как query contract, а не как замена domain model

Не использовать новые имена:
- `*DTO` для доменных сущностей
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
