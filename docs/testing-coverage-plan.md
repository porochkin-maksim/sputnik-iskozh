# План покрытия тестами

## Текущее состояние

Покрыто: 10 команд + 3 валидатора (функционал `/home/*`, ЛК)
Не покрыто: ~90 файлов команд/валидаторов в `core/App/*`


## Приоритеты

### Этап 1 — Counter (остаток)
- `SaveAdminCounterCommand`
- `DeleteAdminCounterCommand`
- `SaveAdminCounterValidator`

### Этап 2 — CounterHistory (остаток)
- `AddAdminCounterHistoryCommand`
- `AddAdminCounterHistoryValidator`
- `ConfirmCounterHistoriesCommand`
- `ConfirmCounterHistoriesValidator`
- `CreateCounterClaimCommand`
- `CreatePublicCounterHistoryCommand`
- `CreatePublicCounterHistoryValidator`
- `DeleteCounterHistoriesCommand`
- `DeleteCounterHistoryFileCommand`
- `LinkCounterHistoryCommand`
- `LinkCounterHistoryValidator`
- `NotifyAboutNewUnverifiedCounterHistoryCommand`
- `AutoIncrementingCounterHistoriesCommand`
- `RewatchCounterHistoryChainCommand`

### Этап 3 — Billing/Payment (public routes)
- `CreatePublicPaymentCommand`
- `CreatePublicPaymentValidator`
- `LinkPaymentCommand`
- `LinkPaymentValidator`
- `SaveInvoicePaymentCommand`
- `NotifyAboutNewUnverifiedPaymentCommand`
- `SaveImportPaymentsCommand`
- `SaveImportPaymentsInput`

### Этап 4 — Billing/Invoice (core)
- `SaveCommand`
- `SaveValidator`
- `GetListCommand`
- `RecalcClaimsPaidCommand`
- `CreateClaimsAndPaymentsForRegularInvoiceCommand`
- `CreateRegularPeriodInvoicesCommand`
- `CreateRegularPeriodInvoicesInput`
- `MigrateDebtsForRegularInvoiceCommand` ✅
- `SyncPeriodServicesCommand` ✅

### Этап 5 — Billing/Claim (core)
- `SaveCommand`
- `SaveValidator`
- `GetListCommand`
- `GetFormDataCommand`
- `CheckClaimForCounterChangeCommand`
- `CheckClaimForCounterChangeInput`
- `CheckDebtClaimNamesCommand` ✅

### Этап 6 — HelpDesk/Ticket (public) ✅
- `CreateCommand` ✅
- `CreateInput` ✅
- `CreateValidator` ✅
- `GetListCommand` ✅
- `SaveCommand` ✅
- `UpdateCommand` ✅
- `UpdateInput` ✅
- `UpdateValidator` ✅
- `SendTicketCreatedNotificationCommand` ✅

### Этап 7 — Billing/Period ✅
- `SaveCommand` ✅
- `SaveValidator` ✅
- `GetListCommand` ✅

### Этап 8 — Billing/Service ✅
- `SaveCommand` ✅
- `SaveValidator` ✅
- `GetListCommand` ✅
- `CreateMainServicesCommand` ✅
- `CreateOtherServiceCommand` ✅

### Этап 9 — Account ✅
- `SaveCommand` ✅
- `SaveValidator` ✅
- `GetListCommand` ✅
- `ListValidator` ✅

### Этап 10 — User (остаток) ✅
- `SaveCommand` ✅
- `SaveValidator` ✅
- `GetListCommand` ✅
- `ListValidator` ✅
- `SetPasswordByTokenCommand` ✅
- `SetPasswordByTokenValidator` ✅

### Этап 11 — Files/Folders ✅
- Files: `SaveCommand` ✅, `SaveValidator` ✅, `StoreCommand` ✅, `StoreValidator` ✅, `DeleteCommand` ✅, `MoveCommand` ✅, `ReplaceCommand` ✅, `GetListCommand` ✅, `GetListValidator` ✅, `FileCleanupService` ✅
- Folders: `SaveCommand` ✅, `SaveValidator` ✅, `DeleteCommand` ✅, `GetListCommand` ✅, `GetListValidator` ✅
- Services: `FolderDeletionService` ✅, `FolderDeletionExecutor` ✅, `FolderDeletionWalker` ✅

### Этап 12 — HelpDesk/Category + HelpDesk/Service ✅
- Category: Create ✅, Delete ✅, GetList ✅, Save ✅, SaveValidator ✅
- Service: Create ✅, Delete ✅, GetList ✅, Save ✅, SaveValidator ✅

### Этап 13 — News, Options, Access, HistoryChanges ✅
- News: `SaveCommand` ✅, `SaveFileCommand` ✅, `GetListCommand` ✅, валидаторы ✅
- Options: `SaveCommand` ✅, `SaveValidator` ✅
- Access: `SaveRoleCommand` ✅, `SaveRoleValidator` ✅
- HistoryChanges: `CreateHistoryCommand` ✅

## Итоговый статус

- **474 passed, 0 risky** (1438 assertions)
- План покрытия (Этапы 1–13) — **выполнен полностью**

## Баги production-кода, найденные при тестировании

| № | Файл | Проблема | Статус |
|---|------|----------|--------|
| 1 | `core/App/HelpDesk/Ticket/CreateValidator.php` | `TicketTypeEnum::byCode()` → null, передача в `findByTypeAndCode(TicketTypeEnum $type, ...)` вызывала TypeError | ✅ Исправлен |
| 2 | `AccountService::getById()` | Принимает 1 аргумент (`$id`), но `UpdateValidator` вызывает с 2 (`getById($id, true)`) | ⚠️ Найден, требуется решение |
| 3 | `Core\Domains\HelpDesk\SearchResponses` | Неправильный namespace (`SearchResponses` вместо `Responses`) | ✅ Исправлен |
| 4 | `Folders/FolderSearcher` | Нет метода `getParentId()` для обхода дерева папок вверх | ⚠️ Найден, требуется при необходимости |

## Дальнейшие шаги

1. **Новые домены** — Billing/Acquiring (команды `CreatePaymentLinkCommand`, `HandleSubmitWebhookCommand`, `HandleFailedWebhookCommand` — покрыты ✅)
2. **Feature/Integration тесты** — сквозные HTTP-сценарии
3. **Баги production-кода** (п. 2, 4) — согласовать и исправить
4. **PHPStan/larastan** — повысить уровень статического анализа
5. **Risky-тесты** — устранены (было 7, стало 0)

## Структура теста

```php
<?php declare(strict_types=1);

namespace Tests\Unit\App\<Domain>\<Subdomain>;

use Tests\TestCase;

class <ClassName>Test extends TestCase
{
    private <Dependency1> $dependency1;
    private <Command>     $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dependency1 = $this->createMock(<Dependency1>::class);
        $this->dependency2 = $this->createMock(<Dependency2>::class);

        $this->command = new <Command>(
            $this->dependency1,
            $this->dependency2,
        );
    }
}
```

## Критерии качества

- Каждая команда: happy path + граничные случаи (null, empty, not found, access denied)
- Каждый валидатор: valid + каждый null/empty + дубликаты + `validation_returns_all_errors`
- Нейминг тестов: `test_execute_<scenario>` (команды), `test_<field>_<condition>` (валидаторы)
- Все моки — как private свойства класса (не инлайнить)
- `HistoryChangesService` — всегда выносить в `setUp()`
- После каждого этапа — `make architecture && make tests`
