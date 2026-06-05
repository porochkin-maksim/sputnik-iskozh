# Agent Handbook

Этот файл нужен как быстрый старт для любого агента, который работает в репозитории `sputnik-iskozh`.

## 1. Что это за проект

- Laravel 12 приложение с DDD-слоями в `core/`.
- Публичная часть, личный кабинет и админка собраны на Laravel Blade + Vue 3.
- Система работает вокруг участков, периодов, услуг, счетов, начислений, платежей, счётчиков, обращений и файлов.
- Основной домен — СНТ, где:
  - `AccountEntity` — участок/аккаунт;
  - `PeriodEntity` — период;
  - `ServiceEntity` — услуга;
  - `InvoiceEntity` — счёт;
  - `ClaimEntity` — строка начисления;
  - `PaymentEntity` — платёж;
  - `CounterEntity` / `CounterHistoryEntity` — счётчики и история показаний.

## 2. Архитектурная модель

### Backend
- `core/Domains/*` — доменная модель, коллекции, enum-ы, сервисы и интерфейсы репозиториев.
- `core/App/*` — application layer, use case commands, validators, orchestration.
- `app/*` — transport/infrastructure:
  - controllers,
  - HTTP resources,
  - Eloquent mappers/repositories,
  - bindings,
  - jobs,
  - framework glue.

### Frontend
- `resources/views/*` — Blade shell, страницы и shared partials.
- `resources/js/components/public/*` — публичный фронт.
- `resources/js/components/profile/*` — ЛК.
- `resources/js/components/admin/*` — админка.
- `resources/js/components/common/*` — переиспользуемые UI-компоненты.
- `resources/sass/*` — общий визуальный слой.

## 3. Текущий важный billing-контекст

### Invoice model
- `REGULAR` invoice — основной периодный счёт для `account + period`.
- `INCOME` / `OUTCOME` — отдельные финансовые документы для дополнительных сценариев.
- `InvoiceEntity` теперь поддерживает необязательную связь `serviceId`.
- `serviceId` на invoice нужен, когда счёт относится к конкретной услуге.
- `ClaimEntity` остаётся атомарной строкой начисления и не должен превращаться в агрегатор разных услуг.

### Invoice/service invariant
- Ищем/создаём invoice по:
  - `accountId`
  - `periodId`
  - `type`
  - `serviceId`, если он задан
- `REGULAR` не используется как контейнер для электрических начислений.
- Для электричества и похожих service-specific сценариев используется отдельный invoice, привязанный к услуге.

### Current electric claim flow
- `core/App/Billing/Claim/CheckClaimForCounterChangeCommand.php` создаёт/обновляет claim для counter history.
- Для электрических начислений логика должна работать через service-specific invoice, а не через `REGULAR`.

## 4. Frontend state

### Визуальная цель
- Public и profile должны быть визуально близкими.
- Public/admin шапки идентичны по shell-контракту.
- Profile имеет тот же header shell, но другой набор элементов.

### Что уже выровнено
- `layout` shell:
  - `app-layout.blade.php`
  - `admin-layout.blade.php`
  - `profile-layout.blade.php`
- public/profile forms:
  - общий `page-card` / `auth-form-card` / `public-form-card` ритм;
  - убраны лишние bootstrap spacing-классы вроде `my-3`, `mb-3` там, где они ломали rhythm.
- files/folders/public news/search/help-desk:
  - получили более карточный и плотный вид;
  - dropdown-меню в таблицах/списках в основном убраны;
  - ссылки и кнопки приведены к единому стилю.

### Текущее правило по ссылкам и действиям
- `link-firm` — единый визуальный стиль ссылок.
- `admin-table-firm` — стиль таблиц админки.
- `table-thin-column` — для узких технических колонок.
- `dropdown` в таблицах по возможности не используется.

## 5. Ключевые документы проекта

- `docs/agents/rules/00-core.md` — PHP-стандарты (обязательно к прочтению перед каждой задачей)
- `docs/agents/rules/01-task-preflight.md` — preflight checklist
- `docs/agents/rules/architecture-standards.md` — архитектурные правила
- `docs/agents/rules/docker-environment.md` — Docker/Sail execution rule
- `docs/agents/rules/laravel-conventions.md` — Laravel-конвенции
- `docs/agents/rules/vue-conventions.md` — Vue-конвенции
- `docs/agents/rules/domain-blueprint.md` — полный blueprint DDD-домена
- `docs/agents/agent-rules.md` — обязательные правила агента (краткая сводка)
- `ARCHITECTURE.md` — backend/DDD правила.
- `FRONTEND_ARCHITECTURE.md` — frontend архитектура.
- `FRONTEND_STANDARDIZATION_PLAN.md` — frontend-стандартизация.
- `docs/public-profile-visual-refactor-plan.md` — визуальный рефактор public/profile.
- `docs/visual-style-refactor-plan.md` — общий визуальный рефактор.
- `docs/billing-invoice-model.md` — модель invoice/service-link.
- `docs/legal-pages-checklist.md` — legal pages checklist.

## 6. Практические наблюдения

- Репозиторий живой и уже содержит много legacy/частично отрефакторенного кода.
- Нельзя бездумно ломать семантику колонок, сортировку и action-areas ради внешнего вида.
- Если таблица сортируется по `id`, `id` нельзя убирать из первой колонки без отдельного решения.
- Если компонент уже живёт внутри `page-card`, не надо добавлять ему ещё одну карточку.
- Если нужен один spinner/loading shell на page-level, он должен быть единым по зоне, а не дублироваться на каждом уровне.
- Для форм spacing должен идти от container stack, а не от случайных `mt-3/mb-3/my-3`.

## 7. Быстрый список файлов для ориентира

- `core/App/Billing/Claim/CheckClaimForCounterChangeCommand.php`
- `core/App/Billing/Claim/GetFormDataCommand.php`
- `core/Domains/Billing/Invoice/InvoiceEntity.php`
- `core/Domains/Billing/Invoice/InvoiceTypeEnum.php`
- `core/Domains/Billing/Invoice/InvoiceSearcher.php`
- `core/Domains/Billing/Service/ServiceEntity.php`
- `resources/views/layouts/app-layout.blade.php`
- `resources/views/layouts/profile-layout.blade.php`
- `resources/views/layouts/admin-layout.blade.php`
- `resources/sass/components/public.scss`
- `resources/sass/components/auth.scss`
- `resources/sass/layout.scss`

## 8. Как работать дальше

Перед изменениями:
- читать `docs/agents/rules/00-core.md`;
- выполнять preflight из `docs/agents/rules/01-task-preflight.md`;
- смотреть `ARCHITECTURE.md`;
- смотреть `FRONTEND_ARCHITECTURE.md` и `FRONTEND_STANDARDIZATION_PLAN.md` для Vue;
- не забывать, что бизнес-логика должна жить в `core/App/*`;
- проверять, не ломается ли current worktree.

## 9. Common frontend pitfalls

### CustomSelect + Enum json format

`EnumCommonTrait::json()` возвращает `[{key, value}]`, а `CustomSelect.vue:63` ожидает `{value, label}` и нормализует через `opt.value ?? opt.key ?? opt`.

При передаче enum-options в `custom-select` всегда нормализуй через computed:

```js
const normalizedOptions = computed(() =>
    props.categories.map(opt => ({value: opt.key, label: opt.value}))
);
```

Иначе `modelValue` (число, например `0`) не совпадёт с value опции (строка `'Разное'`).

### NewsItemEdit prop contract

- `modelValue = null` — создание новой статьи
- `modelValue = { id, title, ... }` — редактирование
- `categories` — всегда массив `[{key, value}]` из `@json(NewsCategoryEnum::json())` в Blade
- После сохранения: создание → `/news/form/{id}`, редактирование → `/news/{id}` (show page)

