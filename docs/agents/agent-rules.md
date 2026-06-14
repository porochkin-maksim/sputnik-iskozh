# Agent Rules

Это отдельный набор правил для любого агента, работающего в этом репозитории.

## 1. Обязательные источники правил

- Перед любой задачей читать `docs/agents/rules/00-core.md`.
- Для backend/DDD соблюдать `ARCHITECTURE.md`.
- Для frontend соблюдать `FRONTEND_ARCHITECTURE.md` и `FRONTEND_STANDARDIZATION_PLAN.md`.
- Для billing-логики учитывать `docs/billing-invoice-model.md`.

## 2. Команды и среда

- Использовать только Docker/Sail/Make для runtime-команд.
- Не использовать локальные `php`, `composer`, `yarn`, `npm`, `node`, `vite`.
- Перед завершением задачи запускать релевантные проверки, если менялся runtime-код.

## 3. Работа с деревом

- Не игнорировать dirty worktree.
- Не откатывать чужие изменения без явного запроса.
- Не использовать destructive-команды без необходимости и без согласования.

## 4. Backend rules

- **Бизнес-сценарий входит через `core/App/*`.**  
  Каждый use case — отдельный класс Command методом `execute()`.  
  Контроллер только принимает request, маппит данные и вызывает команду.  
  *Пример: `core/App/Counter/CreateProfileCounterCommand` — контроллер `CounterController::create()` только передаёт запрос в команду и возвращает ответ.*

- **Контроллеры — thin.**  
  Никакой бизнес-логики в контроллерах. Максимум — маппинг request → DTO, вызов команды, возврат response.

- **Валидация живёт рядом с командой.**  
  `core/App/<Domain>/Validator/<CommandName>Validator.php` — используется только своей командой.  
  *Пример: `core/App/Counter/Validator/CreateProfileCounterValidator` — вызывается только из `CreateProfileCounterCommand`.*

- **Domain-слой не зависит от Laravel/HTTP.**  
  В `core/Domains/*` запрещены: `use Illuminate\*`, Facades, Eloquent, Request/Response, Blade.

- **`Mapper` — единственное место трансформации repository data ↔ domain entity.**  
  *Пример: `App\Http\Resources\Profile\Counters\CounterMapper::toEntity(Counter $model): CounterEntity`.*  
  Нигде больше не преобразовывать Eloquent model в Entity напрямую.

## 5. Frontend rules

- Не плодить nested card wrappers.
- Не возвращаться к случайным bootstrap spacing-классам, если есть stack/container rhythm.
- Не ломать семантику таблиц ради стилизации.
- Не смешивать разные визуальные паттерны ссылок и action-кнопок в одной таблице без причины.

## 6. Billing-specific rules

- `REGULAR` invoice не смешивать с сервисными начислениями.
- `serviceId` на invoice — необязательная связь, а не замена claim/service.
- `ClaimEntity` остаётся атомарной строкой начисления.
- Для counter/electric сценариев не использовать `REGULAR` как контейнер.

## 7. Checks

Минимум:
- `make architecture`
- `make prod` при изменении runtime/backend/frontend кода
- `php -l` для изменённых PHP-файлов, если есть риск синтаксической ошибки

## 8. Code style

PHP-код форматируется единообразно во всём проекте — тесты и production одинаково.

Инструменты автоформатирования (Laravel Pint, PHP CS Fixer) **не подходят** — их правила конфликтуют со стилем проекта (разбивают `<?php declare`, сбрасывают выравнивание, ломают `;` на отдельной строке). Форматирование только ручное, по конвенциям ниже.

| Правило | Пример |
|---|---|
| `<?php declare(strict_types=1);` | одной строкой, без перевода |
| Типы свойств/параметров | выравниваются по самому длинному через пробелы |
| Присваивания | выравниваются по `=` |
| `new ClassName` без `()` | `new CounterEntity` (нет аргументов — нет скобок) |
| `catch` / `finally` | на новой строке (`}\ncatch`, `}\nfinally`) |
| `fn(...)` | без пробела после `fn` |
| `;` после fluent chain | на отдельной строке |
| Пустые строки между свойствами | отсутствуют |
| Фигурные скобки для классов/методов | на новой строке |
| Фигурные скобки для control structures | на той же строке |

## 9. Testing

- Unit-тесты писать для всех `core/App/*` команд и их валидаторов.
- Команды тестировать через моки зависимостей (PHPUnit `createMock`).
- Валидаторы тестировать: валидные данные, каждый null/empty кейс, дубликаты, `validation_returns_all_errors`.
- Имена тестов: `test_execute_<scenario>` для команд, `test_<field>_<condition>` для валидаторов.
- Все моки объявлять как private свойства класса (не инлайнить).
- Все зависимости, включая `HistoryChangesService`, выносить в `setUp()` единообразно.
- **readonly class** — команды и валидаторы в HelpDesk используют `readonly class`. При тестировании такие классы тестируются как обычные.
- **PHP strict types + моки**: если production-код передаёт `null` в метод с non-nullable type-hint (`TicketTypeEnum $type`, а не `?TicketTypeEnum $type`), PHPUnit mock выбрасывает `TypeError`, а не ожидаемое исключение. Либо чинить production-код (добавлять guard), либо учитывать в тесте.
- **Config в тестах**: `config()` возвращает реальные значения из `.env` даже в тестовом окружении. Учитывай это при подсчёте вызовов (например, `config('mail.emails.admin')`).
- **AccountService::getById()** принимает 1 аргумент (`int|string|null $id`), но `UpdateValidator` вызывает с 2 (`getById($id, true)`). Это баг production-кода — при тестировании мок принимает любые аргументы, но production упадёт, если код дойдёт до этой ветки.

## 11. Глобальная обработка ValidationException

`Core\Exceptions\ValidationException` глобально обрабатывается в `app/Exceptions/Handler.php`:

```php
$this->renderable(function (ValidationException $e, $request) {
    if ($request->expectsJson()) {
        return response()->json(['errors' => $e->errors], 422);
    }
    return redirect()->back()->withErrors($e->errors);
});
```

**Следствия для кода:**
- Не оборачивать `ValidationException` в try/catch в контроллере — Handler сам вернёт корректный ответ.
- В HTTP-тестах для JSON-эндпоинтов использовать `$this->postJson()` (или `->withHeaders(['Accept' => 'application/json'])`), чтобы `expectsJson()` возвращал `true`.
- Если эндпоинт всегда возвращает JSON (тип `JsonResponse`), достаточно `$this->postJson()` в тестах — контроллер не проверяет `wantsJson()`, ответ всегда JSON.

## 12. Страницы ошибок (HTTP exceptions)

- **Всегда использовать `__()` для сообщений** в `resources/views/errors/*.blade.php`. Не выводить `$exception->getMessage()` — он может содержать английский текст от Symfony.
- **Лейаут выбирается динамически** через `Handler::render()`:
  - `/admin/*` → `layouts.admin-layout`
  - `/profile/*` → `layouts.profile-layout`
  - остальные → `layouts.app-layout`
- **Namespace `errors::` не зарегистрирован** — использовать только dot-нотацию: `@extends('errors.minimal')`, а не `@extends('errors::minimal')`.
- **Русские переводы** HTTP-сообщений лежат в `resources/lang/ru.json`. Вендорные переводы (Nova/Spark) — в `resources/lang/_ru.json`, их не редактировать.

## 10. Мета-правила агента

1. **Фиксировать всё в документацию.** Любое новое знание о проекте, стиле, архитектуре — сразу в `docs/agents/`. Если пользователь поправил — обновить доку.
2. **Проверять изменения.** После `write`/`edit` — перечитать файл через `read` и убедиться, что изменение применилось. Write tool иногда не применяет запись.
3. **Правила из docs — закон.** При выполнении любой задачи сверяться с `docs/agents/agent-rules.md` и `AGENTS.md`. Если правило противоречит интуиции — доверять доку.
4. **Стиль един для всего кода.** Правила из раздела 8 распространяются на тесты и production без исключений.
5. **Актуализировать `docs/testing-coverage-plan.md`** — при любой модификации приложения (новые/изменённые команды, валидаторы, роуты). После каждого завершённого этапа покрытия отмечать выполненное в плане и прогонять `make architecture && make tests`.

