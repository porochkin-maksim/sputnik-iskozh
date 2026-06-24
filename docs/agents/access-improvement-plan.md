# План улучшения системы доступа

## Выводы из анализа

Система permission-based (не строго RBAC). Роль — контейнер для набора `PermissionEnum`. Нет иерархии ролей, нет `is_admin` поля. User-to-role назначается через редактирование пользователя (выбор роли) или через редактирование роли (выбор пользователей). Хардкод `UserToRole` для id=1 — fallback до первого похода в БД.

**Ключевое ограничение:** система прав не знает о бизнес-стейтах (например, `isClosed` периода). Проверки стейта приходится добавлять вручную в каждый контроллер. Это не баг, а архитектурное решение — права и стейт ортогональны. Но это требует дисциплины: любой метод, меняющий данные в контексте периода, должен проверять `isClosed`.

---

## Приоритет 1 — Критичные (баги / безопасность)

### 1.1 Gate для повторяющихся проверок "право + стейт"

**Статус: ✅ ГОТОВО**

`PeriodGate` создан (`core/Domains/Billing/Period/PeriodGate.php`):
- `assertCanEditInvoices()` — permission + isClosed
- `assertCanEditServices()` — permission + isClosed
- `assertNotClosed()` — только проверка isClosed

Внедрён в `InvoiceController` (заменил старый `assertPeriodNotClosed` + дублирующиеся `can()` проверки).
Внедрён в `InvoiceImportController` (заменил проверку isClosed в `fetchPeriod()`).
Внедрён в `ServiceController.save()`, `ClaimController` (через хелпер), `PaymentManageController.payAll()`.

---

## Приоритет 2 — Средние (качество / консистентность)

### 2.1 Кеширование прав между запросами

**Статус: ✅ ГОТОВО**

`lc::role()` теперь использует `Cache::remember("user_role_{$userId}", 300, ...)`. TTL=5 мин. В пределах одного запроса — статическое кеширование как и было.

Инвалидация не реализована: при смене прав роли старые данные живут до 5 мин. Для админки биллинга приемлемо. Если потребуется — добавить `Cache::forget()` в `SaveRoleCommand` через событие или прямой вызов.

### 2.2 Стабилизировать `sectionKey()`

**Статус: ✅ УЖЕ БЫЛО ГОТОВО**

`sectionKey()` в `PermissionEnum` уже использует явный `match`, не `Str::slug()`. Все ключи совпадают с фронтовыми вызовами `has('section', 'action')`.

### 2.3 Прокидывать `isClosed` во все ресурсы с периодом

**Статус: ✅ ГОТОВО**

- `PeriodsSelectResource` — уже есть `isClosed` (сделан ранее)
- `InvoiceResource` — `actions.periodClosed` вместо корневого поля
- `ServiceResource` — `actions.periodClosed` вместо корневого поля
- `ClaimResource`, `PaymentResource` — не сериализуют период, не требуют поля

---

## Приоритет 3 — Низкие (refactoring)

### 3.1 Внедрить `PeriodGate` в `ClaimController`, `ServiceController`, `PaymentManageController`

**Статус: ✅ ГОТОВО**

- `ServiceController.save()` — `$this->periodGate->assertCanEditServices()`
- `ClaimController.create()/save()/delete()` — permission check + `assertNotClosedByInvoiceId()` (хелпер грузит invoice → periodId и делегирует в `PeriodGate::assertNotClosed()`)
- `PaymentManageController.payAll()` — `$this->periodGate->assertNotClosed()`
- `PaymentManageController.payClaim()` — не трогали (требует загрузки claim → invoice → period, больший рефакторинг)

### 3.2 Пересмотреть `canAccessAdmin()`

**Статус: ✅ ГОТОВО**

- Добавлено отдельное право `PermissionEnum::ADMIN_ACCESS = 0`
- `RoleDecorator::canAccessAdmin()` теперь проверяет только `ADMIN_ACCESS` (вместо `canAny` 9 VIEW-прав)
- Создана миграция `2026_06_20_000002`, которая проставляет `ADMIN_ACCESS` всем существующим ролям в БД (обратная совместимость)
- `ADMIN_ACCESS` имеет `sectionKey() = 'admin'`, `actionKey() = 'access'`, отображается в UI как отдельная секция «Доступ в админку»

### 3.3 Объединить `UserToRole` хардкод с БД

**Статус: ❌ РЕШЕНО НЕ ДЕЛАТЬ**

Завести в БД роль ADMIN с полными правами, в сиде привязать id=1. Убрать `UserToRole` и `RoleFactory::makeForUserId()`.
Осознанное решение: хардкод для id=1 — защита от ситуации, когда таблица ролей пуста (например, до первого сида). Менять не будем.

### 3.4 Убрать неиспользуемые методы из `PeriodGate`

**Статус: ✅ ГОТОВО**

`assertCanEditClaims()` и `assertCanView()` удалены (были объявлены, но нигде не использовались).
Остальные методы (`assertCanEditInvoices`, `assertCanEditServices`, `assertNotClosed`) используются.
