# Mandatory Task Preflight

Это правило выполняется перед каждой новой задачей Codex в этом проекте. Его нельзя пропускать молча.

## Перед любыми действиями

- Определи тип задачи: backend, frontend, architecture/refactoring, tests/build, docs-only или mixed.
- Проверь `git status --short` и не трогай чужие изменения.
- Сверься с обязательными правилами из `docs/agents/rules/*.md`.
- Для backend/architecture задач учитывай `ARCHITECTURE.md`.
- Для frontend задач учитывай `FRONTEND_ARCHITECTURE.md` и `FRONTEND_STANDARDIZATION_PLAN.md`.
- Для refactoring/jobs/use-case/event задач учитывай `docs/agents/rules/domain-blueprint.md` (если файл существует).
- Для команд runtime всегда учитывай `docs/agents/rules/docker-environment.md`.
- Если в sandbox возникает ошибка окружения (например `bwrap`, `Operation not permitted`, loopback/net namespace), сразу повторяй команду с `require_escalated` и дальше работай без sandbox для этой задачи.

## Перед правками

- Сформулируй коротко, какие правила применяются к задаче.
- Если задача затрагивает `core`, проверь границы `core/App`, `core/Domains`, `app/*`.
- Если задача затрагивает Vue, проверь component decomposition, generated API contracts и запрет legacy direct axios/routes/jQuery.
- Если задача требует запуска PHP/Yarn/Node/Composer/Artisan, используй только Make/Sail.
- Если есть конфликт между быстрым решением и правилами, выбирай правила или явно сообщай пользователю о невозможности соблюсти их без отдельного решения.
- Не трать время на повторные запуски в sandbox, если уже подтверждён системный сбой песочницы в текущей задаче.

## Перед завершением

- Для architecture/backend/frontend изменений запускай `make architecture`, если задача не является чисто текстовой.
- Для PHP-файлов запускай lint через `./vendor/bin/sail php -l <file>` или релевантные тесты через `make artisan ...`.
- Для frontend runtime изменений запускай `make yarn-build`.
- Если проверку нельзя выполнить, явно напиши причину.

## Запрещено

- Начинать реализацию, не определив релевантные правила.
- Заменять Docker/Sail локальным runtime.
- Расширять allowlist без объяснения и плана сокращения legacy.
- Игнорировать frontend decomposition thresholds при изменении крупных Vue components.
