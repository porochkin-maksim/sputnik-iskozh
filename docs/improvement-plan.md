# План улучшений приложения

На основе архитектурных правил (`docs/agents/rules/*.md`, `docs/ARCHITECTURE.md`) и аудита (`docs/architecture-audit.md`).

## 🔴 Критические

- [ ] **Написать тесты** — `tests/Unit/` и `tests/Feature/` пусты. Покрыть все `core/App/*` команды и валидаторы unit-тестами (PHPUnit, моки). Обновить `docs/testing-coverage-plan.md`.
- [ ] **Подключить PHPStan/Psalm** — заменить grep-based `make architecture` на полноценный статический анализ.
- [ ] **Разобраться с dirty worktree** — `AGENTS.md` и `docs/testing-coverage-plan.md` изменены, не закоммичены.

## 🟠 Высокие

- [ ] **Архитектурный рефакторинг legacy** (приоритет из `docs/ARCHITECTURE.md` §6):
  - убрать `app(...)` и locator-style зависимости из `core`
  - вынести orchestration в `core/App/*`
  - перенести валидацию из контроллеров в команды
  - перевести mapping на отдельные mapper-ы
  - дочистить naming и legacy-файлы
- [ ] **Billing: изолировать `REGULAR` от сервисных начислений** — электричество через service-specific invoice, а не `REGULAR` (`docs/billing-invoice-model.md`).
- [ ] **Добавить недостающие индексы БД**:
  - `payments.invoice_id`
  - `payments.account_id`
  - `claim_to_objects(type, reference_id)`
  - `acquiring.invoice_id`
  - `acquiring.status`

## 🟡 Средние

- [ ] **Автосинхронизация invoice ↔ claims** — при изменении claim данные на invoice рассинхронизируются (денормализация).
- [ ] **Frontend: ESLint/Prettier** — автоматическое форматирование JS/Vue.
- [ ] **Frontend: декомпозиция крупных Vue-компонентов** — соблюдение decomposition thresholds.
- [ ] **Frontend: визуальный рефактор** — довести public/profile/admin до единого визуального ритма.
- [ ] **Исправить баг `AccountService::getById()`** — принимает 1 аргумент, но `UpdateValidator` вызывает с 2 (`docs/agents/agent-rules.md` §9).
- [ ] **Обновить `docs/testing-coverage-plan.md`** — актуализировать после каждого изменения.
