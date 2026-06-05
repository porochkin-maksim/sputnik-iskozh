# Контекст проекта "Спутник-Искож"

## Основные возможности
- CRM для предприятия садоводческого товарищества
- Формы обращений и обратной связи
- Ведение финансовой отчётности
- Генерация документов (PDF, QR-коды)
- Экспорт в Excel (maatwebsite/excel)
- Диаграммы (chart.js)
- WYSIWYG редакторы (TinyMCE, TipTap, Quill)

## Важные пакеты
- spatie/laravel-pdf — генерация PDF
- dompdf — альтернативный PDF
- simplesoftwareio/simple-qrcode — QR-коды
- cknow/laravel-money — работа с деньгами

## Аутентификация
- Laravel Sanctum (API токены)
- Возможно, используется laravel/ui (Bootstrap стили)

## Среда разработки
- Проект работает через Docker Compose и Laravel Sail.
- Основной сервис приложения: `laravel.test` из `docker-compose.yml`.
- Все проектные PHP/Composer/Artisan/PHPUnit/Yarn/Node/Vite команды запускать через `Makefile` или `./vendor/bin/sail`.
- Локальные `php`, `composer`, `yarn`, `npm`, `node`, `vite` не использовать для проектных команд.
- Основные команды: `make up`, `make artisan ...`, `make composer ...`, `make tests`, `make yarn-build`, `make yarn-watch`.

## Правила агента
- Перед каждой задачей агент обязан выполнить preflight из `docs/agents/rules/01-task-preflight.md`.
- Backend/DDD задачи сверять с `ARCHITECTURE.md`.
- Frontend/Vue задачи сверять с `FRONTEND_ARCHITECTURE.md` и `FRONTEND_STANDARDIZATION_PLAN.md`.
