# Инструкции для агента

## Non-negotiable
- Docker/Sail/Make only. Никогда не использовать локальные `php`, `composer`, `yarn`, `npm`, `node`, `vite`.
- Не игнорировать dirty worktree.
- Общаться и рассуждать **только на русском языке**.

## Обязательное чтение перед задачей
| Файл | Когда |
|---|---|
| `docs/agents/README.md` | Обзор docs/agents и порядок чтения |
| `docs/agents/rules/00-core.md` | Каждая задача |
| `docs/agents/ARCHITECTURE.md` | Backend/DDD |
| `docs/agents/FRONTEND_ARCHITECTURE.md`, `FRONTEND_STANDARDIZATION_PLAN.md` | Vue/frontend |
| `docs/billing-invoice-model.md` | Биллинг |
| `docs/billing-database-analysis.md` | Схема БД и архитектура биллинга |
| `docs/architecture-audit.md` | Аудит архитектуры: проблемы, критичность, статус рефакторинга |
| `docs/agents/agent-rules.md` | Правила агента |
| `docs/agents/agent-handbook.md` | Контекст проекта, визуальный рефактор, модель биллинга |

## Key make commands
- `make up` / `make down` / `make restart` — containers
- `make tests` — PHPUnit
- `make architecture` — grep-based architecture guard (run before finishing)
- `make prod` — pre-deploy: `architecture + tests + yarn-build`
- `make yarn-watch` — frontend dev (auto-regenerates JS contracts from PHP)
- `make yarn-build` — production frontend build
- `make artisan <cmd>` — artisan via Sail
- `make create-domain NAME=<X>` — scaffold new DDD domain

## Domain structure
- `core/Domains/*` — pure domain, zero Laravel/HTTP deps
- `core/App/*` — Commands with `execute()`, never `Handler`/`handle()`
- `app/*` — transport: thin controllers, Eloquent repos, mappers, DI bindings
- `Mapper` is the only place for repo ↔ domain transformation
- DI bindings: `app/Providers/BindingProvider.php`
- Reference domain: `core/Domains/News/` (copy structure from here)

## Frontend
- 3 Vue apps: `app.js` (public), `admin.js`, `profile.js`
- Composition API + `<script setup>` — never `export default { }`
- `resources/js/api/index.js` is auto-generated — do not edit manually. **Никогда** не писать URL вручную, использовать только сгенерированные беком функции из `@api`.
- Vite uses polling for HMR (Docker requirement)
- `CustomSelect` ждёт `{value, label}`, `EnumCommonTrait::json()` отдаёт `{key, value}` — всегда нормализовать через computed

## Редактирование файлов (Surgeon Rule)
- `oldString` должен содержать **только целевую строку** (максимум ±1 токен для уникальности).
- `newString` — то же самое, с минимальным изменением. Не добавлять/удалять пустые строки, не менять отступы, не сбивать выравнивание `=`, `->method()`, `catch` и т.д.
- После каждого `edit` — делать `read` изменённого участка и `git diff` чтобы убедиться, что не задето лишнее.
- Если нужно откатить — `git checkout -- filename`, не делать edit поверх edit.

## Before finishing
- `make architecture` — always if runtime code changed
- `make prod` — if backend/frontend runtime code changed
- `php -l` for changed PHP files if syntax error risk