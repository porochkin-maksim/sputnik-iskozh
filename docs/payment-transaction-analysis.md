# Анализ: Транзакции как прослойка между Payment и Claim

## Текущая архитектура

```
Payment.invoice_id → Invoice
                   Invoice → Claim[].paid = RecalcClaimsPaidCommand(cумма verified-платежей)
```

- `claim.paid` **не вводится вручную**, только пересчёт
- один Payment на один Invoice
- распределение по claims — алгоритмическое, не ручное
- 10 мест в коде вызывают `InvoiceService::recalcInvoice()`
- `RecalcClaimsPaidCommand` — единственный, кто пишет `claim.paid`

## Предложение: Transaction

Сущность, которая явно фиксирует «сколько из какого платежа ушло на какую услугу».

```
Payment (без invoice_id)
  └─ Transaction (payment_id, claim_id, cost)
       ├─ если claim_id IS NOT NULL → оплата конкретной услуги
       └─ если claim_id IS NULL → аванс/кредит на лицевом счёте

claim.paid = SUM(transaction.cost WHERE claim_id = :this)
invoice.paid = SUM(claim.paid WHERE invoice_id = :this)
```

## Что меняется

### Сущности и БД

| Что | Сейчас | Станет |
|---|---|---|
| `Payment.invoice_id` | обязателен для verified | **nullable** всегда, не обязателен |
| `claim.paid` | пишет `RecalcClaimsPaidCommand` | вычисляется как SUM транзакций |
| `RecalcClaimsPaidCommand` | распределяет алгоритмически | упрощается или удаляется |
| `Transaction` | нет | новая таблица `payment_transactions` |
| Advance | тип услуги `ADVANCE_PAYMENT` | транзакции с `claim_id IS NULL` |
| Debt | тип услуги `DEBT` | остаётся, не меняется |

### `payment_transactions`

```sql
CREATE TABLE payment_transactions (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL REFERENCES payments(id),
    claim_id   BIGINT UNSIGNED NULL REFERENCES claims(id),
    cost       DECIMAL(20,2) NOT NULL,
    created_at TIMESTAMP NULL
);
```

### Backend (ключевые файлы)

| Файл | Изменение |
|---|---|
| `PaymentEntity` | удалить `invoiceId` (или сделать опциональным) |
| `PaymentEloquentMapper` | не маппить `invoice_id` |
| `RecalcClaimsPaidCommand` | **удалить**. Вместо него: при создании/изменении payment создаются транзакции |
| `ClaimEntity` | `getPaid()` = сумма транзакций (или вычисляется через сервис) |
| `InvoiceService::recalcInvoice()` | перестаёт диспатчить `RecalcClaimsPaidJob`. Новое: пересчитывает invoice из транзакций |
| `SaveImportPaymentsCommand` | не требует `invoiceId`, создаёт транзакции по алгоритму |
| `LinkPaymentCommand` | привязывает платёж не к invoice, а создаёт транзакции |
| `PaymentSearcher` | убрать `setInvoiceId()`, добавить `setAccountId()` |
| `PaymentController`, `NewPaymentController` | убрать выбор счёта, добавить распределение по claims |
| `PaymentObserver`, `ClaimObserver` | убрать вызов `recalcInvoice` |

### Frontend

| Компонент | Изменение |
|---|---|
| `PaymentDialog.vue` | вместо выбора счёта — выбор claims и сумм на каждый |
| `PaymentsBlock.vue` (в invoice) | показывать транзакции, а не просто сумму |
| `PeriodPaymentsImportBlock.vue` | распределение импорта по периодам/услугам |
| `InvoicesBlock.vue`, `InvoicesList.vue` | `invoice.paid` = сумма транзакций |

## Плюсы

1. **Явное распределение.** Каждая копейка знает, на какую услугу ушла. Аудит.
2. **Один платёж на несколько счетов.** Банковская выписка → один Payment → транзакции по разным периодам.
3. **Advance прозрачен.** Не нужен тип услуги. Транзакция без claim = аванс. Можно в любой момент назначить на claim.
4. **Частичная оплата услуги.** Можно заплатить половину членского взноса.
5. **Импорт без привязки к счёту.** Сумма разносится по периодам автоматически или вручную.
6. **Упрощение кода.** Убирается `RecalcClaimsPaidCommand` (сложный алгоритм с edge cases), 10 вызовов `recalcInvoice()`.

## Минусы и риски

1. **UI усложняется.** Вместо «выбрать счёт» — интерфейс распределения суммы по услугам.
2. **Миграция данных.** Текущие `claim.paid` нужно разложить в транзакции. Тысячи записей — разовая операция.
3. **Производительность.** `claim.paid` = SUM транзакций. На списке из 1000 claims может быть медленно без кэша/дениормализации. Решение: хранить `claim.paid_denormalized` и обновлять триггером.
4. **ClaimObserver.** Сейчас реагирует на изменение `claim.paid`, которого не будет — триггер пересчёта invoice нужно переосмыслить.
5. **Экспорты.** Все читают `claim.getPaid()` — нужно чтобы он возвращал сумму транзакций (через сервис или денормализацию).

## Стратегия миграции

1. Создать таблицу `payment_transactions`
2. Написать artisan-команду: для каждого существующего `claim.paid > 0` создать транзакцию
3. Добавить вычисление `claim.paid` через транзакции (с денормализацией для скорости)
4. Переписать `RecalcClaimsPaidCommand` — убрать распределение, оставить только пересчёт `cost`
5. Переписать PaymentDialog и импорт
6. Убрать `invoice_id` из Payment (опционально, оставить nullable для обратной совместимости)

## Резюме

Transaction-ы делают систему гибче и прозрачнее, но требуют:
- новой таблицы
- миграции данных
- переписывания `RecalcClaimsPaidCommand`
- нового UI для распределения платежей

Объём работ — значительный. Если будешь готов — начнём с миграции БД и команды для разложения существующих данных.
