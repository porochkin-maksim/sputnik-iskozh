# Domain Blueprint (актуализированная версия)

## 1. Референсный домен

В качестве **образца для подражания** при создании новых доменов в пространстве `Core\Domains\*` используй существующий домен:

**`core/Domains/News/`** (полный путь от корня проекта)

Этот домен реализует все современные подходы: слоистую архитектуру, интерфейсы репозиториев, мапперы, собственные коллекции, сервисы без зависимостей от фреймворка.

## 1.1. Базовая терминология

- `Entity` в этом проекте является основной доменной моделью данных.
- Отдельный слой `DTO` для доменов `core/Domains/*` не нужен.
- Если в legacy-коде есть `DTO`, при рефакторинге его нужно убирать и переводить всё на `*Entity`.
- Переименование `DTO` в `Entity` без сохранения параллельного слоя считается корректным и целевым состоянием.

## 2. Структура Domain-Driven Design, DDD (на примере `News`)
- App/Models/News.php
- App/Repositories/News/
- ├── NewsEloquentMapper.php # Маппер для репозитория
- ├── NewsEloquentRepository.php # Репозиторий для Eloquent модели
- App/Http/Resources/Public/
- ├── NewsResource.php # Модель json ответа для http
- Core/Domains/News/
- ├── NewsEntity.php # доменная сущность
- ├── NewsCollection.php # коллекция сущностей
- ├── NewsRepositoryInterface.php # интерфейс репозитория
- ├── NewsSearcher.php # класс для поиска
- ├── NewsSearchResponse.php # результат поиска
- ├── NewsService.php # доменный сервис
- ├── NewsFactory.php # доменная фабрика
- ├── NewsCategoryEnum.php # перечисление
- Core/App/News/ # слой application
- ├── GetListCommand.php # получение списка элементов
- ├── GetListValidator.php # валидация входных параметров
- ├── SaveCommand.php # сохранение сущности
- ├── SaveValidator.php # валидация команды сохранения
- ├── * # дополнительные манипуляции

## 3. Доменный слой (`Core/Domains/{Domain}`)

### 3.0. Стиль кода внутри домена
- Все зависимости должны импортироваться через `use`.
- Не допускаются fully-qualified class names внутри тел методов и выражений, если можно сделать обычный импорт.
- Не нужно оборачивать `new SomeClass()` в дополнительные скобки перед fluent-chain, если синтаксис PHP это позволяет.
- Предпочтительно:
  ```php
  $entity = new SomeEntity()
      ->setId($id)
      ->setName($name);
  ```
- Нежелательно:
  ```php
  $entity = (new SomeEntity())
      ->setId($id)
      ->setName($name);
  ```

### 3.1. Сущность (`{Domain}Entity`)
- Использует проектный трейт временных меток, принятый в текущем коде.
- Приватные поля, геттеры/сеттеры возвращают `static`.
- Является основной доменной моделью данных.
- Бизнес-методы без зависимостей от фреймворка.
- Не должна реализовывать `JsonSerializable` в домене.
- Преобразование в JSON и HTTP-ответы должны происходить через ресурсы инфраструктуры.

### 3.1.1. Граница ответственности Factory
- `Factory` в домене нужна для создания новых доменных объектов и значений по бизнес-смыслу.
- `Factory` может содержать `makeDefault()`, `makeEmpty()`, named constructors и другую логику создания новых `Entity`.
- `Factory` не должна дублировать persistence-mapping логику из `EloquentMapper`.
- Если в проекте уже есть `EloquentMapper`, то преобразования `Entity -> Eloquent model`, `Eloquent model -> Entity`, `Collection of models -> Collection of entities` должны жить только в маппере.
- Запрещено одновременно держать в `Factory` и `EloquentMapper` пары методов вроде `makeFromModel`, `makeDtoFromObject`, `makeModelFromDto`, `makeEntityFromRepositoryData`, если они решают одну и ту же задачу преобразования.
- Запрещено в `Factory` использовать переделегирование вызова в `EloquentMapper`. Эти две сущности должны быть изолированны друг от друга.
- При конфликте ответственности выбирай один источник истины:
  - создание новых доменных объектов по бизнес-правилам → `Factory`
  - преобразование домен <-> persistence → `EloquentMapper`

### 3.2. Коллекция (`{Domain}Collection`)
- Расширяет проектную базовую коллекцию, используемую в текущем коде.
- Не требует проверки типа (можно оставить пустой).

### 3.3. Интерфейс репозитория (`{Domain}RepositoryInterface`)
- `search(SearcherInterface): SearchResponse`
- `save(Entity): Entity`
- `getById(?int): ?Entity`
- `getByIds(array): SearchResponse`
- `deleteById(?int): bool`
- (опционально) `getIdsByFullTextSearch(string): array`

### 3.4. Сёрчер (`{Domain}Searcher`)
- Расширяет `Core\Repositories\BaseSearcher`.
- Добавляет методы для установки специфичных условий (например, `setType`, `setRelatedId`).

### 3.5. Поисковый ответ (`{Domain}SearchResponse`)
- Расширяет `Core\Repositories\BaseSearchResponse`.
- Аннотация `@method {Domain}Collection getItems()`.

### 3.6. Сервис (`{Domain}Service`)
- Содержит бизнес-операции.
- Принимает интерфейсы в конструкторе (репозиторий, вспомогательные сервисы).
- Не использует фасады и статические вызовы.

## 4. Общие интерфейсы (`Core/Domains/Shared/Contracts`)
- `FileStorageInterface` – работа с файловым хранилищем.
- `StringGeneratorInterface` – генерация строк, замена, нормализация путей.
- `RepositoryDataMapperInterface` – преобразование объект ↔ модель.
- и другие

## 5. Инфраструктура (`app/*`)

### 5.1. Маппер (`{Domain}EloquentMapper`)
- Реализует `RepositoryDataMapperInterface`.
- `makeRepositoryDataFromEntity()` – заполняет Eloquent модель из сущности.
- `makeEntityFromRepositoryData()` – создаёт сущность из модели.
- `makeEntityFromRepositoryDatas()` – создаёт коллекцию сущностей из набора моделей.
- `EloquentMapper` является единственным местом для явного mapping persistence <-> domain.
- Если mapping уже реализован в `EloquentMapper`, повторять ту же логику в `Factory`, `Service`, `Repository` или `Entity` запрещено.
- `Repository` должен делегировать преобразования в mapper, а не собирать сущности вручную и не знать детали этого преобразования.

### 5.2. Репозиторий (`Eloquent{Domain}Repository`)
- Реализует доменный интерфейс.
- Использует трейт `App\Repositories\Shared\DB\RepositoryTrait`.
- Переопределяет абстрактные методы:
  - `modelClass()` → класс Eloquent модели.
  - `getTable()` → имя таблицы.
  - `makeEmptyCollection()` → новая пустая доменная коллекция.
  - `makeEmptySearchResponse()` → новый экземпляр SearchResponse.
  - `getMapper()` → экземпляр маппера.

## 6. Элоквент модель (`app/Models/{Domain}/{Domain}Model`)
- Константы для всех полей (`TABLE`, `ID`, `NAME` и т.д.).
- Отношения (если есть).
- Не содержит бизнес-логики.

## 7. Очереди и блокировки (для асинхронных задач)
- Джоба должна реализовать `Core\Queue\LockableJobInterface` и использовать трейт `DispatchIfNeededTrait`.
- Методы: `getIdentificator()`, `static getLockName()`, `process()`.
- Статические методы `dispatchIfNeeded` и `dispatchSyncIfNeeded`.

## 8. Регистрация зависимостей
В `App\Providers\BindingProvider` добавь биндинги:
```php
$this->app->bind(
    \Core\Domains\{Domain}\{Domain}RepositoryInterface::class,
    \App\Repositories\{Domain}\{Domain}EloquentRepository::class,
);
```

## 9. Работа с контроллерами
Получение данных запросов реализовывать  через `app/Http/Requests/DefaultRequest.php`

- Контроллеры должны оставаться thin.
- Бизнес-логика не должна жить в контроллерах.
- Persistence-логика не должна жить в контроллерах.
- Контроллер должен orchestration-слоем вызывать сервисы, команды, ресурсы и request-объекты.
- Контроллер не должен валидировать бизнес-правила вручную через Laravel Validator, если логика уже может быть вынесена в `core/App/*`.
- Контроллер должен:
  - извлечь примитивы и value objects из `DefaultRequest` / `Request`;
  - передать их в `core/App/{Domain}/*Command` или `UseCase`;
  - вернуть `JsonResponse`, `View` или ресурс;
  - отдать обработку доменных ошибок глобальному exception handler.

## 10. Слой приложения  (`core/App/{Domain}`)
- В этом разделе ты должен создавать команды {Action}{Domain}Command.php
- Команда должна уметь сама себя валидировать и выбрасывать исключение `core/Exceptions/ValidationException.php` с массивом ошибок и их описанием
- Игнорировать этот слой при рефакторинге контроллеров, HTTP-сценариев и доменных операций запрещено.
- Если в контроллере появляется нетривиальная orchestration-логика, создание или использование команд в `core/App/{Domain}` обязательно.

### 10.1. Application Command / UseCase
- `core/App/{Domain}` — это слой application orchestration между HTTP и доменом.
- Если операция приходит из HTTP и содержит нетривиальные проверки, orchestration или подготовку доменных объектов, она должна жить в `core/App/{Domain}`.
- Команда или use case должна принимать примитивные значения, `Entity`, `Enum` или project value objects, но не Laravel `Request`.
- Команда не должна зависеть от Laravel facade и HTTP-контекста.
- Команда должна собирать доменные сущности через `Factory`, вызывать доменные сервисы и возвращать доменный результат.

### 10.2. Валидация
- Валидация входных данных должна жить в application-слое (`core/App/{Domain}`), а не в Laravel controller.
- Допустимы два проектных варианта:
  - команда валидирует себя сама;
  - команда делегирует проверку отдельному `*Validator` в том же `core/App/{Domain}`.
- Отдельный `Validator` предпочтителен, если:
  - проверок больше нескольких;
  - они используются повторно;
  - команду нужно упростить.
- Ошибки валидации должны собираться в массив вида:
```php
[
    'field' => ['Сообщение об ошибке'],
]
```
- При ошибках application-слой должен выбрасывать `Core\Exceptions\ValidationException`.
- Для project-стандарта это основной способ вернуть ошибки валидации из домена / application-слоя без зависимости от Laravel Validation.

### 10.3. Граница с Laravel
- `app/Http/Requests/*` нужны для безопасного извлечения и приведения HTTP-данных, а не как основное место бизнес-валидации.
- `app/Exceptions/Handler.php` должен централизованно преобразовывать `Core\Exceptions\ValidationException` в HTTP-ответ.
- Контроллер не должен вручную собирать JSON ошибок, если это уже умеет `Handler`.
- При рефакторинге нужно стремиться к схеме:
  - HTTP Request → `DefaultRequest` / Request helper methods
  - Controller → `core/App/{Domain}/*Command`
  - Command / Validator → `Core\Exceptions\ValidationException`
  - `app/Exceptions/Handler.php` → HTTP 422 / redirect с ошибками

## 11. Режим полного рефакторинга домена

Если пользователь пишет:
- "сделай так же для другого домена или в домене `A`"
- "повтори для `core/Domains/X`"
- "переведи домен на DDD"
- "приведи к виду `News` / `User`"
- "сделай как предыдущий домен"

это означает не точечную правку, а **полный domain refactor** до конечного состояния без legacy-слоя.

### 11.1. Цель
- Привести домен к единому стандарту DDD и clean code по образцу `core/Domains/User`, `core/Domains/News`, `core/App/News`.
- Сохранить текущее поведение, но убрать архитектурный мусор, промежуточные адаптеры и устаревшие слои.

### 11.2. Целевое состояние
- В `core/Domains/{Domain}/` остаются только актуальные доменные классы.
- Доменные классы должны лежать в одной папке домена, если для подпапок нет реальной необходимости.
- Не должно оставаться `DTO`, legacy-обёрток, alias-классов, совместимых прокладок и временного наследования.
- Если legacy-домен использовал `DTO` как основную модель, после рефакторинга должна остаться только `Entity`.
- Не должно оставаться вложенных папок `Models`, `Factories`, `Collections`, `Responses`, `Services`, `Enums`, если это можно выразить плоской структурой по образцу `User` и `News`.
- `Entity`, `Collection`, `RepositoryInterface`, `Searcher`, `SearchResponse`, `Service`, `Factory`, `Enum` должны быть разделены по отдельным файлам.
- Инфраструктурные `Mapper` и `EloquentRepository` должны жить только в `app/Repositories/...`.
- `Eloquent`, `DB`, `Laravel Facade`, framework-specific relations и прочий инфраструктурный код не должны оставаться в `core/Domains/...`.
- `Locator`-ы, static service access и legacy helper-классы должны удаляться, если можно перейти на DI или `app(...)` без потери поведения.
- Дублирование логики между `Factory`, `Mapper`, `Service`, `Entity` должно устраняться.
- Особенно запрещено дублирование mapping-логики между `Factory` и `EloquentMapper`.
- Наследование допускается только если оно реально уменьшает сложность. Alias-наследование ради совместимости запрещено.
- Все namespace-зависимости должны быть оформлены через `use`, а не через fully-qualified имена в коде.
- Не нужно механически переписывать валидные fluent-chain выражения в форму с `(new Class())`, если проектный стиль допускает `new Class()->...`.

### 11.3. Обязательные шаги
1. Найти все использования домена по проекту.
2. Сравнить текущую структуру домена с эталоном `User` / `News`.
3. Построить целевую плоскую структуру домена.
4. Перенести инфраструктуру в `app/Repositories/...`.
5. Удалить `DTO` и legacy-слой, а не оставлять его рядом с новой реализацией.
6. Переподключить все использования на новые классы и namespace.
7. Удалить неиспользуемые legacy-файлы и пустые каталоги.
8. Проверить синтаксис всех изменённых файлов.
9. В финале перечислить:
   - что осталось в домене,
   - какие legacy-файлы удалены,
   - какие внешние использования были переподключены,
   - что проверено.

### 11.4. Что не нужно уточнять дополнительно
Если пользователь говорит "сделай как предыдущий домен", нужно автоматически:
- повторить тот же архитектурный паттерн;
- довести домен до конечного состояния;
- убрать `DTO`;
- убрать `Locator`;
- не оставлять legacy alias-классы;
- не останавливаться на промежуточной совместимости, если пользователь явно не просил backward compatibility.

### 11.5. Что запрещено оставлять
- новая `Entity` + старый `DTO`;
- новый `RepositoryInterface` + старый repository в `core`;
- новые namespace + старые alias-классы;
- сервис через DI, но параллельно живой `Locator` без необходимости;
- часть домена в плоской структуре, а часть в старых подпапках без причины;
- одинаковая логика, размазанная между `Factory`, `Mapper`, `Service`.
- `DTO` как отдельную доменную модель рядом с `Entity`, если это по сути один и тот же объект.
