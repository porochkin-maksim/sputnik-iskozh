# Architecture Standards

При любой задаче, затрагивающей домены, application layer, controller orchestration, jobs или mapper-ы, нужно
сверяться с [ARCHITECTURE.md](/web/sputnik-iskozh/ARCHITECTURE.md) и соблюдать его как обязательный стандарт.

## Обязательные правила

- Любой use case входит через `core/App/*`.
- Контроллеры, jobs, listeners и console commands должны оставаться thin.
- Валидация сценария должна жить рядом с command в `core/App/*`.
- `core/Domains/*` не должен зависеть от Laravel container, locator-ов, Eloquent, HTTP и Facades.
- `app/*` является transport/infrastructure слоем.
- Преобразование `repository data <-> domain entity` выполняют только mapper-ы.

## Запрещено

- `app(...)` внутри `core/Domains/*`
- новые `*Locator`
- `DB::transaction`, `beginTransaction`, `commit`, `rollBack` в контроллерах
- `Validator` и `validate()` в контроллерах как место бизнес-валидации
- `makeDtoFromObject`, `makeDtoFromObjects`, `makeModelFromDto`
- lazy loading в entity через container или locator
- новые `*DTO` как доменные сущности в `core/Domains/*`

## Практическое правило

Если есть сомнение, куда поместить логику:
- бизнес-сценарий и orchestration -> `core/App/*`
- бизнес-сущность и инварианты -> `core/Domains/*`
- Eloquent, mapper, repository implementation, bindings -> `app/*`

## Рефакторинг legacy-кода

Приоритет рефакторинга:
1. убрать `app(...)` и locator-style зависимости из `core`
2. вынести orchestration в `core/App/*`
3. перенести validation в commands
4. перевести mapping на отдельные mapper-ы
5. дочистить naming и legacy-файлы
