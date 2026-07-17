# План расширения системы периодов: закрытие, блокировка, каскадная оплата

## 1. Проблема

Сейчас:
- `isClosed` — просто булев флаг без бизнес-логики
- Перенос долга (`getMigratingClaimsToNewPeriod`) берёт предыдущий период по **ID**, а не последний **закрытый**
- Нет блокировки редактирования Invoice/Claim/Payment при закрытом периоде
- Оплата только в рамках одного периода (UI профиля)

Нужен сценарий:
- Период 01.2026-06.2026 (полугодие, открыт)
- Период 07.2026-06.2027 (год, открыт)
- Пользователь может платить в обе периода сразу
- При закрытии полугодия — недоплата финализируется как долг и переносится в год

---

## 2. Изменения в Domain / Entities

### 2.1. `PeriodEntity` — добавить `closedAt`, `closedBy`

```php
private ?Carbon $closedAt = null;
private ?int    $closedBy = null;
```

Миграция:
```sql
ALTER TABLE periods ADD COLUMN closed_at TIMESTAMP NULL AFTER is_closed;
ALTER TABLE periods ADD COLUMN closed_by BIGINT UNSIGNED NULL AFTER closed_at;
```

### 2.2. `InvoiceEntity` — добавить `isLocked` (вычисляемое: период закрыт)

Не хранить в БД. Вычислять через `$this->period?->isClosed()`.

Метод:
```php
public function isLocked(): bool
{
    return $this->period?->isClosed() ?? false;
}
```

### 2.3. `ClaimEntity` — добавить `isLocked`

Аналогично: если invoice-период закрыт — claim заблокирован.

```php
public function isLocked(): bool
{
    return $this->invoice?->getPeriod()?->isClosed() ?? false;
}
```

### 2.4. `PaymentEntity` — добавить `isLocked`

Платёж заблокирован, если:
- У него есть распределённые транзакции (`allocatedSum > 0`)
- ИЛИ invoice-период, к которому привязан платёж, закрыт

```php
public function isLocked(): bool
{
    if ($this->allocatedSum !== null && $this->allocatedSum > 0) {
        return true;
    }
    return $this->invoice?->getPeriod()?->isClosed() ?? false;
}
```

---

## 3. Изменения в Application / Commands

### 3.1. `RecalcClaimsPaidCommand` — каскадный расчёт по всем открытым периодам

Сейчас: `execute(int $invoiceId)` — распределяет платежи только в рамках одного invoice.

Нужно сделать новый метод `executeForAccount(int $accountId)`, который:

```
1. Получить все открытые периоды (isClosed = false), сортировка start_at ASC
2. Для каждого периода (от самого старого к новому):
   a. Найти REGULAR invoice для account
   b. Вызвать существующий execute(invoiceId)
3. Если остались нераспределённые транзакции — перейти к следующему периоду
```

Это гарантирует, что самый старый долг гасится первым.

### 3.2. Новый `ClosePeriodCommand` — закрытие периода как бизнес-событие

```php
readonly class ClosePeriodCommand
{
    public function execute(int $periodId, int $userId): void
    {
        // 1. Проверить, что период не закрыт
        // 2. Найти следующий открытый период (start_at > current.end_at)
        // 3. Для каждого account с долгом:
        //    - Запустить CreateClaimsAndPaymentsForRegularInvoiceCommand
        //      для invoice следующего периода (перенос долга)
        // 4. Установить isClosed = true, closedAt = now, closedBy = userId
        // 5. Сохранить период
    }
}
```

### 3.3. `PeriodService::getOpenPeriods()` — уже есть, проверить сортировку

Убедиться что `getOpenPeriods()` сортирует по `start_at ASC` для использования в каскадных расчётах.

### 3.4. `CreateClaimsAndPaymentsForRegularInvoiceCommand::getMigratingClaimsToNewPeriod()`

Изменить логику поиска источника долга:
- Вместо `id < текущий_id LIMIT 1` → искать последний **закрытый** период, у которого `end_at < текущий.start_at`
- Если закрытых периодов нет → не переносить долг

---

## 4. Контроллеры / API

### 4.1. Новый эндпоинт `POST /admin/billing/periods/{id}/close`

В `PeriodController`:
```php
public function close(int $id): JsonResponse
{
    $this->closePeriodCommand->execute($id, auth()->id());
    return response()->json(['success' => true]);
}
```

### 4.2. Изменить `PeriodEditDialog.vue` — заменить чекбокс на кнопку

- Вместо чекбокса "Закрыть период" сделать кнопку "Закрыть период" (с confirm)
- После закрытия — перезагрузить список
- В закрытом периоде все поля формы — readonly

### 4.3. Добавить `isLocked` в API-ответы

В ресурсы/мапперы Invoice, Claim, Payment добавить поле `is_locked`.

### 4.4. Profile UI — оплата за несколько периодов

В `InvoicesBlock.vue`:
- Заменить селект одного периода на multi-select (или чекбоксы)
- Показывать сводную сумму по выбранным периодам
- При оплате — передавать список invoiceId

---

## 5. Валидация / Guard-ы

### 5.1. `Invoice SaveCommand` — проверка блокировки

```
if ($invoice->isLocked()) {
    throw new ValidationException(['period' => 'Период закрыт, редактирование невозможно']);
}
```

### 5.2. `Claim SaveCommand` — проверка блокировки

```
if ($claim->isLocked()) {
    throw new ValidationException(['period' => 'Период закрыт, редактирование невозможно']);
}
```

### 5.3. `Payment SaveCommand / LinkPaymentCommand` — проверка

```
if ($payment->isLocked()) {
    throw new ValidationException(['payment' => 'Платёж заблокирован (есть распределение)']);
}
```

### 5.4. `Period SaveCommand` — нельзя открыть закрытый период

```
if ($period->isClosed() && !$isClosed) {
    throw new ValidationException(['is_closed' => 'Нельзя открыть уже закрытый период']);
}
```

---

## 6. UI / Frontend

### 6.1. `PeriodsBlock.vue` — кнопка "Закрыть период"

- В колонке "Действия" добавить кнопку закрытия (только для открытых периодов)
- Confirm-диалог "Вы уверены? Это закроет период и перенесёт долги в следующий"
- После закрытия — disabled для всех действий редактирования

### 6.2. `PeriodEditDialog.vue` — readonly для закрытых

- Если период закрыт — все поля readonly
- Убрать чекбокс isClosed (закрытие только через отдельную кнопку)

### 6.3. `InvoicesBlock.vue` (profile) — multi-select периодов

- Чекбоксы или multi-select для выбора нескольких периодов
- Агрегированная сумма к оплате
- При нажатии "Оплатить" — передача всех invoiceId

### 6.4. Invoice/Claim/Payment cards — `isLocked`

- Если `is_locked` → скрыть кнопки "Редактировать", "Удалить"
- Показать иконку замка и тултип "Период закрыт" / "Есть распределённые транзакции"

---

## 7. Миграции

```php
// 2026_06_25_000001_add_closed_at_closed_by_to_periods_table.php
Schema::table('periods', function (Blueprint $table) {
    $table->timestamp('closed_at')->nullable()->after('is_closed');
    $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
    $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
});
```

---

## 8. Приоритет реализации

| # | Что | Зачем |
|---|---|---|
| 1 | `RecalcClaimsPaidCommand` — каскад по периодам | Без этого новый долг не будет гаситься правильно |
| 2 | `ClosePeriodCommand` + эндпоинт | Бизнес-событие закрытия |
| 3 | Guard-ы блокировки в SaveCommand | Безопасность данных |
| 4 | `getMigratingClaimsToNewPeriod` — искать закрытый период | Корректный перенос долга |
| 5 | Domain: isLocked в Invoice/Claim/Payment | Фундамент для UI и валидации |
| 6 | UI: кнопка "Закрыть период" вместо чекбокса | UX админки |
| 7 | UI: isLocked в карточках | UX админки |
| 8 | UI: multi-select периодов в профиле | Оплата сразу за несколько периодов |
