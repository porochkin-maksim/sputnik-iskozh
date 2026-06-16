# План выноса событий из Observers в DDD Events/Listeners

## Проблема

Бизнес-логика (side-effects) размазана по Eloquent observers в `app/Observers/`.
Это нарушает DDD, неочевидно, привязано к Eloquent вместо домена.

## Текущие observers и их side-effects

| Observer | Папка | Side-effects (помимо логов истории) |
|---|---|---|
| `ClaimObserver` | `Billing/` | Пересчёт invoice при create/update/delete claim |
| `PaymentObserver` | `Billing/` | Установка `paid_at`, пересчёт invoice, уведомление о неподтверждённом |
| `InvoiceObserver` | `Billing/` | Создание claims и payment для regular invoice |
| `PeriodObserver` | `Billing/` | Создание базовых услуг периода |
| `CounterHistoryObserver` | `Counter/` | Уведомление админа, пересчёт цепочки, удаление claim и файлов |
| `UserObserver` | (корень) | Сброс `email_verified_at` при смене email |
| `RoleObserver` | `Access/` | Только логирование |
| `CounterObserver` | `Counter/` | Только логирование |
| `ServiceObserver` | `Billing/` | Только логирование |
| `AccountObserver` | `Account/` | Только логирование |
| `TicketObserver` | `HelpDesk/` | Только логирование |
| `TicketCategoryObserver` | `HelpDesk/` | Только логирование |
| `TicketServiceObserver` | `HelpDesk/` | Только логирование |
| `FileObserver` | `Files/` | Только логирование |

Все регистрируются в `app/Providers/ObserverServiceProvider.php`.

## Инфраструктура

### 1. Базовый класс события

```
core/Events/DomainEvent.php
```

```php
<?php declare(strict_types=1);

namespace Core\Events;

use Carbon\Carbon;

abstract readonly class DomainEvent
{
    public Carbon $occurredAt;

    public function __construct()
    {
        $this->occurredAt = Carbon::now();
    }

    abstract public function eventName(): string;
}
```

### 2. Диспатчер

Использовать нативный Laravel `Event::dispatch()`, но с конвенцией:

```php
Event::dispatch(new ClaimCreated(claimId: $claim->id, invoiceId: $claim->invoice_id, cost: $claim->cost));
```

Listeners регистрировать в `EventServiceProvider`.

## Доменные события и listeners

### Claim (`core/Domains/Billing/Claim/Events/`)

| Событие | Поля | Listener | Действие |
|---|---|---|---|
| `ClaimCreated` | `claimId`, `invoiceId`, `cost` | `RecalcInvoiceListener` | Пересчёт invoice |
| `ClaimCostChanged` | `claimId`, `invoiceId`, `oldCost`, `newCost` | `RecalcInvoiceListener` | Пересчёт invoice |
| `ClaimDeleted` | `claimId`, `invoiceId` | `RecalcInvoiceListener` | Пересчёт invoice |

Listener: `core/Domains/Billing/Claim/Listeners/RecalcInvoiceListener.php`

### Payment (`core/Domains/Billing/Payment/Events/`)

| Событие | Поля | Listener | Действие |
|---|---|---|---|
| `PaymentCreated` | `paymentId`, `invoiceId`, `cost`, `isVerified` | `RecalcInvoiceListener` | Пересчёт invoice (если verified) |
| `PaymentCreated` | `paymentId`, `invoiceId`, `cost`, `isVerified` | `NotifyUnverifiedPaymentListener` | Job-уведомление админа |
| `PaymentCostChanged` | `paymentId`, `invoiceId` | `RecalcInvoiceListener` | Пересчёт invoice |

Listeners: `core/Domains/Billing/Payment/Listeners/`

### Invoice (`core/Domains/Billing/Invoice/Events/`)

| Событие | Поля | Listener | Действие |
|---|---|---|---|
| `InvoiceCreated` | `invoiceId` | `CreateClaimsAndPaymentsListener` | Job для regular invoice |

Listener: `core/Domains/Billing/Invoice/Listeners/CreateClaimsAndPaymentsListener.php`

### Period (`core/Domains/Billing/Period/Events/`)

| Событие | Поля | Listener | Действие |
|---|---|---|---|
| `PeriodCreated` | `periodId` | `CreateMainServicesListener` | Job создания услуг |

Listener: `core/Domains/Billing/Period/Listeners/CreateMainServicesListener.php`

### CounterHistory (`core/Domains/Counter/Events/`)

| Событие | Поля | Listener | Действие |
|---|---|---|---|
| `CounterHistoryCreated` | `historyId`, `isVerified` | `NotifyUnverifiedCounterHistoryListener` | Job-уведомление |
| `CounterHistoryCreated` | `historyId`, `isVerified` | `RewatchChainListener` | Job пересчёта цепочки |
| `CounterHistoryUpdated` | `historyId` | `RewatchChainListener` | Job пересчёта цепочки |
| `CounterHistoryDeleted` | `historyId`, `claimId` | `DeleteClaimForCounterHistoryListener` | Удалить claim |
| `CounterHistoryDeleted` | `historyId`, `claimId` | `DeleteFilesForCounterHistoryListener` | Удалить файлы |

Listeners: `core/Domains/Counter/Listeners/`

### User (`core/Domains/User/Events/`)

| Событие | Поля | Listener | Действие |
|---|---|---|---|
| `UserEmailChanged` | `userId`, `newEmail` | `ResetEmailVerificationListener` | Сбросить `email_verified_at` |

Listener: `core/Domains/User/Listeners/ResetEmailVerificationListener.php`

## Преобразование observers

Каждый observer перестаёт делать side-effects сам, вместо этого диспатчит доменное событие.

**Было (ClaimObserver):**
```php
public function created(Model $item): void
{
    $this->logCreated($item);
    if ($item->cost > 0) {
        $this->invoiceService->recalcInvoice($item->invoice_id);
    }
}
```

**Стало:**
```php
public function created(Model $item): void
{
    $this->logCreated($item);
    Event::dispatch(new ClaimCreated(
        claimId: $item->id,
        invoiceId: $item->invoice_id,
        cost: $item->cost,
    ));
}
```

Observers без side-effects (`RoleObserver`, `ServiceObserver`, `AccountObserver`, `TicketObserver`, `TicketCategoryObserver`, `TicketServiceObserver`, `FileObserver`, `CounterObserver`) остаются как есть — только логирование.

## Очерёдность реализации

1. Инфраструктура: `DomainEvent`, регистрация в `EventServiceProvider`
2. **Claim** — самый частый side-effect (3 события → 1 listener)
3. **Payment** — 2 события, 2 listener'а
4. **CounterHistory** — 3 события, 4 listener'а
5. **Invoice** — 1 событие, 1 listener
6. **Period** — 1 событие, 1 listener
7. **User** — 1 событие, 1 listener
8. Очистка: убрать `InvoiceService` из конструкторов observers, если больше не нужен
9. `make architecture` + `make tests` после каждого шага
