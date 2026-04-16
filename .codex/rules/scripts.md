# NPM & Composer Scripts

## Доступные скрипты

### Composer
- `composer dev` — запускает dev-сервер, queue listener, pail и Vite concurrently
- `composer test` — запускает PHPUnit

### NPM
- `npm run dev` — Vite dev server
- `npm run build` — Vite build

## Запуск окружения
- Для разработки используй `composer dev` (он запускает всё сразу)
- Если нужен отдельно Laravel: `php artisan serve`
- Если нужен Vite: `npm run dev`

## Очереди
- Queue driver: возможно, используется database/sync. В `composer dev` есть `php artisan queue:listen --tries=1`

## Pail
- Логи смотрятся через `php artisan pail --timeout=0`