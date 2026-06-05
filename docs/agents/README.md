# Agent Docs

Этот каталог содержит документы, которые нужны любому агенту перед началом работы в репозитории.

## Структура

- [agent-handbook.md](./agent-handbook.md) — контекст проекта: DDD-слои, billing-модель, визуальный рефактор frontend, быстрый список ключевых файлов
- [agent-rules.md](./agent-rules.md) — краткие правила агента (обязательные источники, команды, backend/frontend/billing-ограничения, проверки)
- [rules/00-core.md](./rules/00-core.md) — **обязателен к прочтению перед каждой задачей**: PHP 8.4 стандарты, PSR-12, именование, типизация, property hooks
- [rules/01-task-preflight.md](./rules/01-task-preflight.md) — preflight checklist: что проверить до и во время работы, что запрещено
- [rules/architecture-standards.md](./rules/architecture-standards.md) — архитектурные правила DDD: границы слоёв, именование, запреты, рефакторинг legacy
- [rules/docker-environment.md](./rules/docker-environment.md) — Docker/Sail: канонические команды, что нельзя делать локально
- [rules/laravel-conventions.md](./rules/laravel-conventions.md) — Laravel 12 конвенции: структура, контроллеры, маршруты, тесты
- [rules/vue-conventions.md](./rules/vue-conventions.md) — Vue 3 + Vite + Bootstrap: стек, компоненты, стили
- [rules/domain-blueprint.md](./rules/domain-blueprint.md) — полный blueprint DDD-домена: News как эталон, структура слоёв, мапперы, команды, рефакторинг

## Что читать первым

1. `../AGENTS.md`
2. `rules/00-core.md`
3. `agent-rules.md`
4. `agent-handbook.md`
5. `rules/01-task-preflight.md`
6. `rules/architecture-standards.md`
7. `../ARCHITECTURE.md`
8. `../FRONTEND_ARCHITECTURE.md` (если задача по frontend)
9. `../FRONTEND_STANDARDIZATION_PLAN.md` (если задача по frontend)
