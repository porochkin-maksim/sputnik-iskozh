# СНТ "Спутник-Искож"

Веб-приложение для работы с участками, периодами, начислениями, оплатами, обращениями, документами и личным кабинетом жителей СНТ.

## Что это за система

- `public` - публичная часть сайта: главная, новости, контакты, обращения, файлы, документы, юридические страницы.
- `profile` - личный кабинет: счётчики, счета, платежи, пароль, профиль.
- `admin` - админка: участки, пользователи, услуги, периоды, счета, платежи, обращения, история изменений, логи.
- `core` - домен и application layer в DDD-структуре.

## Технологии

- Laravel
- PHP 8.4
- Vue 3
- Vite
- Sass
- Bootstrap 5
- Docker / Sail / Make

## Архитектура

- `core/Domains/*` - доменная модель, enum-ы, коллекции, domain services, repository interfaces.
- `core/App/*` - application commands, validators, orchestration use cases.
- `app/*` - HTTP controllers, resources, repositories, mappers, bindings, jobs, framework glue.
- `resources/views/*` - Blade shell, страницы и shared partials.
- `resources/js/components/*` - Vue-компоненты.
- `resources/sass/*` - визуальный слой.

Подробные правила:
- [ARCHITECTURE.md](ARCHITECTURE.md)
- [FRONTEND_ARCHITECTURE.md](FRONTEND_ARCHITECTURE.md)
- [FRONTEND_STANDARDIZATION_PLAN.md](FRONTEND_STANDARDIZATION_PLAN.md)
- [docs/agents/README.md](docs/agents/README.md)

## Billing model

Счета в системе строятся вокруг `account + period`.

- `REGULAR` invoice - основной периодный счёт.
- `INCOME` / `OUTCOME` - дополнительные финансовые документы.
- `InvoiceEntity` может иметь необязательную связь `serviceId`, если счёт относится к конкретной услуге.
- `ClaimEntity` остаётся атомарной строкой начисления.

Подробно:
- [docs/billing-invoice-model.md](docs/billing-invoice-model.md)

## Frontend shell

- Public и profile визуально сближены.
- Public/admin шапка унифицирована по shell-контракту.
- Profile использует тот же верхний каркас, но со своим набором элементов.

Подробно:
- [docs/public-profile-visual-refactor-plan.md](docs/public-profile-visual-refactor-plan.md)
- [docs/visual-style-refactor-plan.md](docs/visual-style-refactor-plan.md)

## Agent docs

Для нового агента:
- [docs/agents/README.md](docs/agents/README.md)
- [docs/agents/agent-handbook.md](docs/agents/agent-handbook.md)
- [docs/agents/agent-rules.md](docs/agents/agent-rules.md)

## Запуск и проверки

Проектные команды запускаются через `Make` / Sail:

- `make up` - поднять окружение
- `make architecture` - архитектурная проверка
- `make tests` - тесты
- `make prod` - полный pre-deploy gate: architecture + tests + frontend build

Нельзя:
- использовать локальные `php`, `composer`, `yarn`, `npm`, `node`, `vite` для проектных команд;
- игнорировать dirty worktree;
- менять DDD/transport boundaries без сверки с архитектурными документами.

## Документация по проекту

- [docs/legal-pages-checklist.md](docs/legal-pages-checklist.md)
- [docs/billing-invoice-model.md](docs/billing-invoice-model.md)
- [docs/public-profile-visual-refactor-plan.md](docs/public-profile-visual-refactor-plan.md)
- [docs/visual-style-refactor-plan.md](docs/visual-style-refactor-plan.md)
- [docs/agents/README.md](docs/agents/README.md)

