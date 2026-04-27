# Domain Blueprint

## Референсный домен

В качестве **образца для подражания** при создании новых доменов в пространстве `Core\Domains\*` используй существующий домен:

**`core/Domains/Billing/Claim/`** (полный путь от корня проекта)

## Структура образца


## Связи с Eloquent моделью и обсервером

- **Eloquent модель** для каждого домена должна лежать в `app/Models/Billing/{Domain}.php` (пространство имён `App\Models\Billing`).
- **Обсервер** для модели в `app/Observers/Billing/{Domain}Observer.php` (пространство имён `App\Observers\Billing`).

Пример для домена `Claim`:
- Модель: `app/Models/Billing/Claim.php`
- Обсервер: `app/Observers/Billing/ClaimObserver.php`

## Правила генерации нового домена

Когда пользователь просит создать новый домен (например, `SomeObject`), выполни следующие шаги:

1. Скопируй структуру папок и файлов из `core/Domains/Billing/Claim/` в `core/Domains/SomeObject/`.
2. Переименуй все классы с `Claim` на `SomeObject` (включая файлы, неймспейсы, use-операторы).
3. Создай Eloquent модель `app/Models/SomeObject.php`, используя аналогичную логику, что в `Claim.php`.
4. Создай обсервер `app/Observers/SomeObjectObserver.php`, используя `ClaimObserver.php` как шаблон.
5. Зарегистрируй обсервер в `ObserverServiceProvider`.
6. Адаптируй DTO, репозиторий, сервис под поля новой сущности (если пользователь укажет поля).

## Именование

- Для домена `Xxx` все классы должны называться `XxxCollection`, `XxxDTO`, `XxxRepository`, `XxxService`, `XxxObserver` и т.д.
- Файлы и папки должны соответствовать PSR-4: `Core\Domains\Billing\Xxx` → `core/Domains/Xxx/`.

## Пример запроса от пользователя

Если пользователь говорит: *«Создай домен SomeObject для Billing»* — ты должен применить этот blueprint.