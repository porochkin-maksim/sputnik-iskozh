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
- Для работы с деньгами используй пакет `cknow/laravel-money` и фасад для него `core/Services/Money/MoneyService.php`
- Генерация PDF через `spatie/laravel-pdf` или `dompdf/dompdf`
- Генерация QR-кодов через `simplesoftwareio/simple-qrcode`

## Маршруты
- API маршруты в `routes/api.php` с префиксом `/api`
- Web маршруты в `routes/web.php`

## База данных
- Используй миграции с красноречивыми именами
- Для сидеров используй `database/seeders/`

## Тестирование
- PHPUnit в `tests/`
- Перед отправкой кода убедись, что `php artisan test` проходит