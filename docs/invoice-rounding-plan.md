# Округление счетов

## Термины

- **rounding / roundCorrection** — величина доводки до круглой суммы (может быть + или −)
- **fullCost** = `cost + rounding` — сумма к оплате

## Вариант A: Свойство `rounding` на InvoiceEntity

**Суть:** новое поле `rounding DECIMAL(20,2)` в invoices. `cost` остаётся точной суммой услуг. `fullCost = cost + rounding` — то что показываем пользователю. Рассчитывается в `RecalcClaimsPaidCommand`.

**Плюсы:** минимум изменений, не влияет на claims, не трогает advance/debt.
**Минусы:** `fullCost` нужно вычислять везде при выводе; `isPaid()` нужно сверять с `fullCost`, а не с `cost`.

### Что менять

| Файл | Изменение |
|---|---|
| `InvoiceEntity.php` | Поле `rounding`, геттер/сеттер, вычисляемый `getFullCost()` |
| миграция | `ALTER TABLE invoices ADD rounding DECIMAL(20,2) NOT NULL DEFAULT 0` |
| `Invoice.php` (model) | Eloquent cast |
| `InvoiceEloquentMapper.php` | Чтение/запись поля |
| `RecalcClaimsPaidCommand.php` | После суммирования (строки 117-127): `rounding = round(cost/10)*10 - cost` |
| `InvoiceResource.php` (admin + profile) | Добавить `rounding`, `fullCost` |
| `InvoicesExport` / `DetailSheet` | `fullCost` вместо `cost` |
| `InvoiceSummaryPanel.vue`, `InvoicesList.vue`, `InvoicesBlock.vue` | Показывать `fullCost` |

---

## Вариант B: Claim с услугой «Прочее» (только положительное округление)

**Суть:** существующий тип услуги `OTHER = 4` создаётся для каждого периода. Добавить кнопку на фронте «Добавить округление», которая создаёт claim с `service = Прочее, quantity = 1, name = Округление` и произвольным `tariff` (положительным).

**Ограничение:** только положительное округление вверх. Отрицательное ломает payment distribution в `RecalcClaimsPaidCommand` (см. анализ ниже).

**Плюсы:** без новых сущностей, прозрачная строка в детализации, не трогает advance/debt.
**Минусы:** не работает для округления вниз; нужно ослабить `SaveValidator` (разрешить `tariff < 0` и `cost < 0` — но тогда любая услуга может быть с минусом).

### Что менять

| Файл | Изменение |
|---|---|
| `SaveValidator.php` | Разрешить `tariff < 0` и `cost < 0` (проверки `>= 0` убрать или сделать опциональными) |
| `ClaimBlock.vue` | Кнопка «Добавить округление», предзаполняет редактор: `service = Прочее, name = Округление, quantity = 1` |
| `ClaimEditor.vue` | Разрешить ввод отрицательного tariff (убрать принудительное ограничение `>= 0`) |
| `ClaimRow.vue` | Отображать отрицательные суммы корректно (знак минус) |

### Анализ отрицательного округления (почему нет)

Payment distribution в `RecalcClaimsPaidCommand` (строки 66-80) завязан на монотонном убывании `remaining`. С отрицательным cost:

1. **remaining = 0 до обработки rounding** — цикл делает `break`, rounding не оплачен, его cost = −50 висит. `invoice.cost = cost + (−50)`, `invoice.paid` не содержит −50 — расхождение.
2. **remaining > 0 при обработке rounding** — `remaining(0) − cost(−50) = +50`. `isPositive() → true`. claim получает `paid = −50`, `remaining` становится +50. Создаётся ложный аванс. Суммы не сходятся.

**Вывод:** только положительное округление (rounding up) для варианта B.

---

## Вариант C: «Прочее» + обработка в RecalcClaimsPaidCommand

Как B, но `RecalcClaimsPaidCommand` явно обрабатывает rounding-claims (например, исключает их из основного цикла распределения и применяет после). Сложнее, но позволяет и положительное, и отрицательное округление.

Пока не прорабатывался.
