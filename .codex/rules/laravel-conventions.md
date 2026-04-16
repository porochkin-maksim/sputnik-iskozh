# Laravel Conventions for this project

## Структура

- Используй Laravel 12 с PHP 8.4
- Модели хранятся в `app/Models/`
- Контроллеры в `app/Http/Controllers/`
- Кастомные классы в `core/` (пространство имён `Core\`)
- Фасады кастомные в `core/Facades/` (пространство имён глобальное, указано в composer.json)

## Стандарты кода

- Следуй PSR-12
- Используй строгую типизацию (declare(strict_types=1))
- В `app/*` держи Laravel-specific и persistence-specific код.
- Не тащи Eloquent, DB facade и framework-specific логику в `core/Domains/*`.
- В этой среде не полагайся на системный `php`, если нужна версия `8.4`.
- Для локальных CLI-проверок и команд PHP используй явный wrapper `./scripts/php84.sh ...`, а не просто `php ...`.
- Все классы и исключения подключай через `use`; не оставляй `\Exception` и аналогичные FQCN в телах методов.
- Если код использует fluent-chain после `new`, не добавляй лишние скобки вокруг `new ...()`, если выражение и так
  валидно.
- Для работы с деньгами используй пакет `cknow/laravel-money` и фасад для него `core/Services/Money/MoneyService.php`
- Генерация PDF через `spatie/laravel-pdf` или `dompdf/dompdf`
- Генерация QR-кодов через `simplesoftwareio/simple-qrcode`

## Контроллеры и слой приложения

- Контроллеры должны оставаться thin.
- Валидация должна жить в Request или в application command, если домен уже переведён на `core/App/*`.
- Бизнес-логика не должна жить в контроллере.
- Контроллер должен координировать request, service, command и resource.
- Если домен рефакторится по DDD, все `Mapper` и `EloquentRepository` должны жить в `app/Repositories/*`.
- Если операция не является тривиальным CRUD-pass-through, контроллер должен вызывать `core/App/{Domain}/*Command` или
  `UseCase`, а не собирать логику сам.
- `Request` в Laravel-слое должен извлекать и приводить входные данные, но не должен становиться основным местом
  бизнес-валидации.
- Ошибки application/domain-валидации должны возвращаться через `Core\Exceptions\ValidationException`, а их HTTP-маппинг
  должен жить в `app/Exceptions/Handler.php`.
- Не дублируй одну и ту же валидацию одновременно в Laravel `Request` и в `core/App/*`, если это не разные уровни
  проверки.

## Маршруты

- API маршруты в `routes/api.php` с префиксом `/api`
- Web маршруты в `routes/web.php`

## База данных

- Используй миграции с красноречивыми именами
- Для сидеров используй `database/seeders/`

## Тестирование

- PHPUnit в `tests/`
- Перед отправкой кода убедись, что `php artisan test` проходит
