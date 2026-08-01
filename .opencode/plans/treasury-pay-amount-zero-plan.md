# План: `amount=0` в TreasuryController::pay()

## Задача

При приёме платежа учитывать баланс счёта. Если у участника есть переплата на балансе, она суммируется с наличными для распределения по услугам. Если наличных 0 — платёж покрывается только с баланса, без создания нового приходного платежа (Payment).

---

## Изменения

### 1. `app/Http/Controllers/Treasury/TreasuryController.php`

**Добавить в DI:** `private PayCommand $payCommand`

**Новый `pay()`:**
- Принимает `amount >= 0`
- Если `amount > 0` — создаёт Payment(cost=amount) + remainder-транзакцию (весь amount в нераспределённый пул)
- Если `amount = 0` — Payment не создаётся
- Allocation'ы обрабатываются через `PayCommand::payClaim()` (берёт из существующего нераспределённого пула)
- `ValidationException` ловится → возвращается 422 JSON

**Удалить:**
- Валидацию `$amount <= 0` → 422
- Валидацию `$totalAllocated > $amount`
- Ручное создание Transaction для allocation'ов
- Ручное `$claim->setPaid()` / `$claimService->save()`
- Цикл recalcInvoice (делает PayCommand)

### 2. `resources/js/components/treasury/TreasuryPaymentModal.vue`

**Строка 405:**
```js
// Было:
const payAmount = Math.round(Math.max(amount.value, totalAllocated.value) * 100) / 100;
// Стало:
const payAmount = Math.round(amount.value * 100) / 100;
```

Фронтенд отправляет `amount` как есть (может быть 0). Больше не форсирует `amount >= totalAllocated`.

### 3. Нет проблемы с `payment_id = NULL`

При `amount = 0` allocation'ы идут через `PayCommand::payClaim`, который переиспользует существующие unallocated-транзакции (с уже проставленным `payment_id`). Новые транзакции с `payment_id = NULL` не создаются.

---

## Проверки после реализации

- `make architecture`
- `make tests`
- `php -l app/Http/Controllers/Treasury/TreasuryController.php`
