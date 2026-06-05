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
- `*Handler` для application use cases в `core/App/*`
- `*Request`, `*Dto` или `*Bag` для typed input objects в `core/App/*`; используй `*Input`

## Практическое правило

Если есть сомнение, куда поместить логику:

- бизнес-сценарий и orchestration -> `core/App/*`
- бизнес-сущность и инварианты -> `core/Domains/*`
- Eloquent, mapper, repository implementation, bindings -> `app/*`

## Application naming

- `*Command` в `core/App/*` - исполняемый use case с зависимостями и методом `execute()`.
- `*Input` в `core/App/*` - readonly typed input object для группировки аргументов command.
- `*Input` не создаётся, если command принимает только идентификаторы (`id`, `periodId`, `invoiceId`, `counterId` и
  т.п.) или другой одиночный scalar идентификатор; в таком случае передавай идентификатор напрямую в `execute(...)`.
- Job/controller/listener/console command инжектит application `*Command`, создаёт `*Input` при необходимости и вызывает
  `execute()`.
- Не создавать пары `*Command` как data object + `*Handler` как исполнитель.
- При распиле use case на вспомогательные application-helpers, держи их в каталоге bounded context рядом с командой и
  называй по роли:
    - `*Walker` для обхода/сбора;
    - `*Executor` для исполнения side effects;
    - `*Renderer` для форматирования/рендера;
    - `*Service` только если это действительно координирующий сервис, а не общий контейнер логики.
- Для use case helpers формулируй имя по обязанности, а не по сущности:
    - имя должно описывать одну конкретную ответственность helper-а;
    - допускаются повторяющиеся паттерны вроде ordering/cleanup/deletion/rendering, если они реально отражают роль;
    - не называй helper по сущности, если он решает более узкую задачу;
    - если обязанность уже выделена в application helper, не возвращай её обратно в domain service;
    - если helper перестаёт быть узким, разделяй его снова по роли, а не расширяй domain service.
- Для frontend feature decomposition держи распиленные child components и composables в отдельном подкаталоге рядом с
  родительским блоком, чтобы один распил не расползался по соседним папкам.
- Для файлового контура держи структуру слоя так:
    - `Core\Domains\Files\FileService` является корневым сервисом общих файлов и папок;
    - domain-specific наследники `FileService` допустимы для задания base path, default file type, public/private режима
      и привязки к bounded context;
    - настройку наследника делай через один immutable config object или один override-конструктор конфигурации, а не
      через россыпь protected getter-методов;
    - если файловый контекст привязан к конкретной сущности, держи его как отдельный наследник/специализацию, а не как
      набор ad-hoc helper-ов в контроллере;
    - не смешивай в одном сервисе общие файловые операции и domain-specific path/type defaults без необходимости.
    - если специализация управляет файлами, привязанными к сущности, допускай узкие helper-методы вида
      `storeRelatedFile`, `getRelatedFile`, `deleteRelatedFile`, но держи их внутри этой специализации;
    - такие helper-методы должны оперировать через type/relatedId и не протекать в общие file commands;
    - application commands должны использовать специализированный file service, когда работают с attached-entity
      файлами, а не повторять search/delete/store логику руками.
    - если specialization-only service содержит только base path/type/public defaults, не раздувай его искусственными
      helper-методами; такая тонкая специализация уже является нормой.
    - если в bounded context есть несколько file-flows одной сущности, специализация может дать узкие named helpers для
      каждого flow, чтобы commands не знали про file type constants и повторный поиск.
    - если поток файлов в bounded context один, достаточно одного named helper вроде `storePaymentFiles` или
      `storeTicketFiles`; не добавляй лишние flow-методы без второго сценария.
    - Для репозиториев с повторяющимися structural settings используй один immutable config object для
      model/table/searcher/collection/response baseline, а не россыпь локальных констант и дублирующих protected
      getter-методов.
    - В `repositoryConfig()` для `collectionClass` используй доменную коллекцию bounded context, а не корневую
      `Core\Shared\Collections\Collection`, если доменная коллекция существует.
    - Если repository layer начинает терять контрактные методы при наследовании или trait-based реализации, сначала
      введи явный config object и public repository methods для ожидаемого API, а не оставляй half-repository с одной
      `save()`.
    - После любого рефакторинга обязательно убирай неиспользуемые `use`-подключения; мёртвые импорты недопустимы во всём
      коде.
    - Тот же запрет на мёртвые `use` действует и для mapper-ов.
    - Если relation mapping между двумя mapper-ами создаёт цикл, выноси assembly в отдельный слой assembler/helper;
      не восстанавливай mutual dependency через `app(...)` или constructor injection между mapper-ами.
- В HTTP resources не вкладывай взаимно-связанные full resources друг в друга; для nested relations используй
  lightweight reference resources с плоским payload, а full resource оставляй для верхнего уровня ответа.
- В frontend shared/cross-cutting слоях импортируй через alias (`@common`, `@api`, `@composables`, `@utils`, `@form`,
  `@components`); relative paths оставляй только для детей внутри того же feature tree. Не смешивай эти два режима для
  одного и того же слоя.

## Рефакторинг legacy-кода

Приоритет рефакторинга:

1. убрать `app(...)` и locator-style зависимости из `core`
2. вынести orchestration в `core/App/*`
3. перенести validation в commands
4. перевести mapping на отдельные mapper-ы
5. дочистить naming и legacy-файлы
