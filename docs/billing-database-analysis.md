# Анализ биллинговой системы и схемы БД

## 1. Таблицы БД

### `periods`
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| name | varchar(255) | NO | |
| start_at | datetime | NO | |
| end_at | datetime | NO | |
| is_closed | tinyint(1) | NO | false |
| deleted_at | timestamp | YES | NULL (soft delete) |
| created_at/updated_at | timestamp | YES | NULL |

### `services`
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| type | smallint unsigned | NO | ServiceTypeEnum |
| period_id | bigint unsigned | NO | FK → periods RESTRICT |
| name | varchar(255) | NO | |
| cost | decimal(20) | NO | 0 |
| active | tinyint(1) | NO | true |
| deleted_at | timestamp | YES | NULL (soft delete) |

### `invoices`
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| period_id | bigint unsigned | NO | FK → periods RESTRICT |
| account_id | bigint unsigned | NO | FK → accounts RESTRICT |
| service_id | bigint unsigned | YES | FK → services SET NULL |
| type | smallint unsigned | NO | InvoiceTypeEnum |
| cost | decimal(20) | NO | 0 |
| paid | decimal(20) | NO | 0 |
| name | varchar(255) | YES | NULL |
| advance | decimal(20) | NO | 0 |
| debt | decimal(20) | NO | 0 |
| comment | text | YES | NULL |

**Индексы:** `(period_id, account_id, type, service_id)` — составной.

### `claims` (бывш. `transactions`)
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| invoice_id | bigint unsigned | NO | FK → invoices RESTRICT |
| service_id | bigint unsigned | NO | FK → services RESTRICT |
| name | varchar(255) | YES | NULL |
| tariff | decimal(20) | NO | 0 |
| cost | decimal(20) | NO | 0 |
| paid | decimal(20) | NO | 0 |

### `claim_to_objects` (бывш. `transaction_to_objects`)
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| claim_id | bigint unsigned | NO | FK → claims CASCADE |
| type | tinyint unsigned | NO | ClaimObjectTypeEnum |
| reference_id | bigint unsigned | NO | |

**Индексы:** `type` (typeIndex), `reference_id` (referenceIdIndex).

### `payments`
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| account_id | bigint unsigned | YES | FK → accounts RESTRICT |
| invoice_id | bigint unsigned | YES | FK → invoices RESTRICT |
| cost | decimal(20) | NO | 0 |
| moderated | tinyint(1) | NO | false |
| verified | tinyint(1) | NO | false |
| name | varchar(255) | YES | NULL |
| data | json | YES | NULL |
| paid_at | date | YES | NULL |
| comment | text | YES | NULL |

### `acquiring`
| Колонка | Тип | Nullable | По умолчанию |
|---|---|---|---|
| id | bigint unsigned PK | NO | auto |
| invoice_id | bigint unsigned | NO | FK → invoices RESTRICT |
| user_id | bigint unsigned | NO | FK → users RESTRICT |
| payment_id | bigint unsigned | YES | FK → payments RESTRICT |
| provider | tinyint unsigned | NO | ProviderEnum |
| status | tinyint unsigned | NO | StatusEnum |
| amount | decimal(20) | NO | |
| data | json | NO | |

---

## 2. Связи (ERD)

```
periods ──1:N── services
periods ──1:N── invoices
accounts ──1:N── invoices
accounts ──1:N── payments
invoices ──1:N── claims
invoices ──1:N── payments
invoices ──1:N── acquiring
services ──1:N── claims
services ──1:N── invoices (service_id nullable)
claims ──1:N── claim_to_objects
payments ──1:N── acquiring (payment_id nullable)
users ──1:N── acquiring
```

**Бизнес-связь:** Один `INVOICE` → много `CLAIM` (строк начисления) + много `PAYMENT`. `RecalcClaimsPaidCommand` распределяет сумму verified-платежей между claims пропорционально.

---

## 3. Enum-ы

### InvoiceTypeEnum
| Case | Value | Описание |
|---|---|---|
| REGULAR | 1 | Периодный счёт (account + period) |
| INCOME | 2 | Дополнительный доход |
| OUTCOME | 3 | Расход |

### ServiceTypeEnum
| Case | Value | Описание |
|---|---|---|
| MEMBERSHIP_FEE | 1 | Членский взнос |
| ELECTRIC_TARIFF | 2 | Электричество |
| TARGET_FEE | 3 | Целевой сбор |
| OTHER | 4 | Прочее |
| DEBT | 5 | Долг |
| ADVANCE_PAYMENT | 6 | Аванс |
| PERSONAL_FEE | 7 | Персональный взнос |

**Методы:** `isAdvance()`, `isDebt()`.

### Acquiring StatusEnum
| Case | Value |
|---|---|
| NEW | 1 |
| PROCESS | 2 |
| CANCELED | 3 |
| PAID | 4 |

### Acquiring ProviderEnum
| Case | Value |
|---|---|
| VTB | 1 |

### ClaimObjectTypeEnum
| Case | Value |
|---|---|
| COUNTER_HISTORY | 1 |

### Payment statuses (boolean-поля, не enum)
- `moderated` — прошёл модерацию
- `verified` — подтверждён (верифицирован)

---

## 4. Domain Model → DB mapping

| Entity | Файл | Таблица |
|---|---|---|
| `PeriodEntity` | `core/Domains/Billing/Period/` | `periods` |
| `ServiceEntity` | `core/Domains/Billing/Service/` | `services` |
| `InvoiceEntity` | `core/Domains/Billing/Invoice/` | `invoices` |
| `ClaimEntity` | `core/Domains/Billing/Claim/` | `claims` |
| `ClaimToObjectEntity` | `core/Domains/Billing/ClaimToObject/` | `claim_to_objects` |
| `PaymentEntity` | `core/Domains/Billing/Payment/` | `payments` |
| `AcquiringEntity` | `core/Domains/Billing/Acquiring/` | `acquiring` |

---

## 5. Application Commands

### Invoice
| Command | Назначение |
|---|---|
| `CreateRegularPeriodInvoicesCommand` | Создаёт REGULAR-инвойсы для всех accounts в периоде (батчами по 50) |
| `CreateClaimsAndPaymentsForRegularInvoiceCommand` | Создаёт claims для REGULAR-инвойса, переносит аванс/долг |
| `RecalcClaimsPaidCommand` | Перераспределяет verified-платежи по claims пропорционально |
| `SaveCommand` | Создание/редактирование инвойса |
| `GetListCommand` | Список с фильтрацией/пагинацией |
| `InvoiceImportService` | Парсинг Excel-файлов импорта платежей |

### Claim
| Command | Назначение |
|---|---|
| `SaveCommand` | Создание/редактирование claim-строки |
| `GetFormDataCommand` | Форма для claim (список услуг) |
| `GetListCommand` | Список claim-строк инвойса |
| `CheckClaimForCounterChangeCommand` | Автосоздание claim по показаниям счётчика |

### Payment
| Command | Назначение |
|---|---|
| `SaveInvoicePaymentCommand` | Создание verified-платежа для инвойса (из админки) |
| `LinkPaymentCommand` | Привязка непривязанного платежа к инвойсу |
| `SaveImportPaymentsCommand` | Сохранение импортированных из Excel платежей |
| `CreatePublicPaymentCommand` | Платёж из публичной заявки |
| `NotifyAboutNewUnverifiedPaymentCommand` | Уведомление о новом неподтверждённом платеже |

### Acquiring
| Command | Назначение |
|---|---|
| `CreatePaymentLinkCommand` | Создание acquiring-записи, получение ссылки на оплату |
| `HandleSubmitWebhookCommand` | Обработка успешного вебхука (→ verified payment) |
| `HandleFailedWebhookCommand` | Обработка failed-вебхука (→ CANCELED) |

### Period / Service
| Command | Назначение |
|---|---|
| `Period\SaveCommand` | Создание/редактирование периода |
| `Period\GetListCommand` | Список периодов |
| `Service\SaveCommand` | Создание/редактирование услуги |
| `Service\GetListCommand` | Список услуг |
| `Service\CreateMainServicesCommand` | Стандартный набор услуг для периода |
| `Service\CreateOtherServiceCommand` | Создание услуги OTHER для периода |

---

## 6. Архитектурные проблемы

### 6.1 ~~`decimal(20,0)` — нет копеек~~ **ИСПРАВЛЕНО**
~~Все денежные поля (`cost`, `paid`, `tariff`, `advance`, `debt`, `amount`) объявлены как `decimal(20)` без второго аргумента. В MySQL это `decimal(20,0)` — **0 знаков после запятой**. Хранятся только целые рубли, копейки теряются.~~

Миграция `2026_05_31_000002_change_decimal_precision_to_2.php` перевела все денежные колонки на `decimal(20,2)`.

### 6.2 Денормализация invoice ↔ claims
`invoices.cost` = `SUM(claims.cost)`, `invoices.paid` = `SUM(claims.paid)` и т.д. Нет автоматической синхронизации — при прямом изменении claim данные на invoice рассинхронизируются.

### 6.3 Не хватает индексов
- `payments.invoice_id` — есть авто-индекс от `foreignId()`, но нет отдельного для частых запросов списка платежей по invoice
- `payments.account_id` — аналогично
- `claim_to_objects(type, reference_id)` — нет составного, хотя используется для поиска claim по reference
- `acquiring.invoice_id` — нет отдельного индекса
- `acquiring.status` — нет индекса для фильтрации по статусу

### 6.4 Nullable FK на payments
`payments.account_id` и `payments.invoice_id` — nullable. Платёж может существовать без привязки. Это бизнес-требование (непривязанные платежи ожидают ручного link).

### 6.5 `paid_at` — date, не datetime
Nullable date. В маппере fallback на `created_at`, что размывает семантику.

### 6.6 Acquiring — только VTB
`ProviderEnum` содержит единственного провайдера. `makeHash()` для вебхуков использует md5.

---

## 7. Бизнес-инварианты (важно)

- `REGULAR` invoice — основной периодный счёт для `account + period`. Не смешивать с сервисными начислениями.
- `serviceId` на invoice — опциональная связь для service-specific счетов (например, электричество).
- `ClaimEntity` — атомарная строка начисления, не агрегатор разных услуг.
- `DEBT` и `ADVANCE_PAYMENT` — служебные типы услуг для переноса долга/аванса между периодами.
- Поиск/создание invoice по: `accountId + periodId + type + serviceId` (если задан).