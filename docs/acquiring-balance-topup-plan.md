# Пополнение баланса через эквайринг — план

> Статус: план, не реализовано.

## Цель

Дать пользователю возможность пополнять баланс своего участка через эквайринг.
Пополненные деньги остаются в нераспределённых транзакциях (баланс аккаунта),
а не списываются автоматически на текущий долг.

## Контекст и ограничения

- Весь acquiring-цикл сейчас жёстко привязан к инвойсу:
  - `acquiring.invoice_id` NOT NULL;
  - `CreatePaymentLinkCommand` требует валидный инвойс и сумму `delta`/`delta×fraction`;
  - вебхук создаёт платёж на инвойс и пересчитывает claims (`RecalcClaimsPaidCommand`).
- `payments.invoice_id` / `payments.account_id` — nullable, платёж может быть только на аккаунт.
- `PaymentTransactionService::saveWithTransaction` уже создаёт транзакцию с `claim_id = null` —
  именно такие транзакции формируют баланс аккаунта (`TransactionService::getBalanceByAccountId`).
- Баланс уже отображается в профиле (`InvoicesBlock.vue`, блок «Баланс»).

## Принятые решения

- Сумма пополнения: произвольная (мин. 1 ₽), ввод в своём поле.
- Деньги остаются только на балансе (НЕ списываются на долг автоматически).
- UI: кнопка «Пополнить баланс» в блоке баланса.

---

## Этап 1 — Схема БД: `account_id` в `acquiring`

Новая миграция (например `2026_08_11_000001_add_account_id_to_acquiring.php`):

- `invoice_id` → nullable;
- добавить `account_id` (nullable FK → accounts);
- инвариант: не оба NULL одновременно (`invoice_id` XOR `account_id`) —
  мягко через check constraint или проверку на уровне команды.

Затронутые файлы:

- `app/Models/Billing/Acquiring.php` — константа `ACCOUNT_ID`, cast;
- `app/Repositories/Billing/AcquiringEloquentMapper.php` — маппинг `account_id`;
- `core/Domains/Billing/Acquiring/AcquiringEntity.php` — поле `accountId`, getter/setter;
- `core/Domains/Billing/Acquiring/Models/AcquiringSearcher.php` — `setAccountId()`.

## Этап 2 — Команда создания ссылки на пополнение баланса

Новая команда `core/App/Billing/Acquiring/CreateBalancePaymentLinkCommand.php`:

- `execute(int $accountId, float $amount, int $userId): ?string`;
- валидация: `amount >= 1`; аккаунт принадлежит пользователю
  (`AccountService::getByUserId` → `searchById`), иначе `abort(403)`;
- создаёт acquiring с `accountId` (без invoiceId), получает ссылку через
  `ProviderGateway::getPaymentLink`, статус `PROCESS`;
- аудит: `HistoryChanges` с `HistoryType::ACCOUNT`.

Роут в `routes/web/home.php`:

- `POST /home/acquring/balance/create/{amount}` → `AcquringController::createBalance`;
- `throttle:10,1`;
- `RouteNames::ACQURING_BALANCE_CREATE`.

Контроллер `AcquringController` — новый метод `createBalance(float $amount)`,
берёт `lc::account()->getId()` и `lc::user()->getId()`.

## Этап 3 — Обработка вебхука для балансового платежа

`core/App/Billing/Acquiring/HandleSubmitWebhookCommand`:

- ветвление: если `$locked->getAccountId() !== null` (пополнение баланса):
  - создать payment с `accountId`, `invoiceId = null`, `verified/modered = true`;
  - `saveWithTransaction` (→ нераспределённая транзакция, попадает в баланс);
  - НЕ вызывать `RecalcClaimsPaidCommand`;
  - аудит с `HistoryType::ACCOUNT`;
- если `invoiceId` — текущая логика без изменений.

## Этап 4 — Фронтенд (блок баланса)

`resources/js/components/profile/invoices/InvoicesBlock.vue`:

- кнопка «Пополнить баланс» в блоке баланса (`acquiringAvailable && !isViewingOther`);
- модалка с полем ввода суммы + подтверждение → POST на новый роут
  (CSRF-токен как у существующих форм), открытие ссылки в новой вкладке;
- URL генерируется через сгенерированный бек API (`@api`), без хардкода.

`app/Http/Resources/Profile/Invoices/InvoicesPageResource.php`:

- добавить `balanceAcquiringUrl` (route) при доступности эквайринга.

## Этап 5 — Тесты

- `CreateBalancePaymentLinkCommandTest` (unit): валидная сумма → ссылка;
  `amount < 1` → null; чужой аккаунт → 403.
- `HandleSubmitWebhookCommandTest`: новый кейс — балансовый платёж создаёт payment
  с accountId и БЕЗ вызова `recalcClaimsPaidCommand`.

## Проверки

- `php -l` изменённых PHP-файлов;
- `make architecture`;
- `sail artisan test --filter Acquiring`.

## Замечание

`invoice_id` станет nullable — существующие данные и логика оплаты счёта не меняются.
Пополнение баланса — отдельный параллельный сценарий, не затрагивающий
IDOR-защиту оплаты счёта (уже реализована).
