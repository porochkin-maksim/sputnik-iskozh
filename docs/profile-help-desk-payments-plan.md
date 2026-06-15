# План: Help Desk и история платежей в личном кабинете

## 1. Мои заявки (Help Desk для пользователя)

Сейчас: публичная форма создания заявки (`/contacts/requests/help-desk`) есть, но у пользователя в ЛК нет списка его заявок, статусов, истории ответов.

### Что нужно сделать

#### 1.1. API — список заявок пользователя

**Новый маршрут** (в `routes/web/profile.php`):
```
GET /home/help-desk/json/list → Profile\HelpDeskController@list
```

**Новый контроллер** `app/Http/Controllers/Profile/HelpDeskController.php`:
- `list()` — поиск тикетов по `user_id` (текущий пользователь), возвращает пагинированный JSON
- `view($id)` — детальный просмотр тикета (проверка принадлежности пользователю)
- Поля тикета: номер, категория, услуга, статус, дата создания, описание, ответы/комментарии

**API функция** (сгенерируется авто):
- `ApiProfileHelpDeskList(getParams)`
- `ApiProfileHelpDeskView(id)`

#### 1.2. Vue-компоненты

```
resources/js/components/profile/help-desk/
├── TicketsBlock.vue          — список заявок с пагинацией
├── TicketItem.vue            — карточка заявки (статус, категория, дата)
└── TicketView.vue            — детальный просмотр + история ответов
```

- `TicketsBlock` — загружает список, отображает в таблице/списке, фильтр по статусу
- `TicketView` — показывает описание, ответы администратора, статус

**Статусы для отображения пользователю:**
- `new` — «Новая»
- `in_progress` — «В работе»
- `waiting_for_customer` — «Ожидает ответа»
- `closed` — «Закрыта»
- `rejected` — «Отклонена»

#### 1.3. Blade-страница

```
resources/views/pages/profile/help-desk/index.blade.php  — список заявок (<help-desk-tickets-block />)
resources/views/pages/profile/help-desk/view.blade.php    — детальный просмотр (<help-desk-ticket-view />)
```

#### 1.4. Навигация

Добавить пункт «Заявки» (`fa fa-bullhorn`) в `resources/views/partials/profile/top-nav.blade.php`:
```php
@if (lc::account()?->getId())
    // ... существующие пункты
    <li><a href="{{ route('profile.help-desk.index') }}"><i class="fa fa-bullhorn"></i>Заявки</a></li>
@endif
```

#### 1.5. Маршруты (RouteNames)

```php
public const string PROFILE_HELP_DESK_INDEX = 'profile.help-desk.index';
public const string PROFILE_HELP_DESK_VIEW  = 'profile.help-desk.view';
```

#### 1.6. Регистрация компонентов

Добавить в `resources/js/registrations/profile.js`:
```js
import HelpDeskTicketsBlock from '@components/profile/help-desk/TicketsBlock.vue';
import HelpDeskTicketView   from '@components/profile/help-desk/TicketView.vue';
```

---

## 2. Сводная история платежей

Сейчас: платежи видны только внутри конкретного счёта на странице счетов. Нет отдельной страницы со всеми платежами по всем периодам.

### Что нужно сделать

#### 2.1. API — список платежей пользователя

**Новый маршрут** (в `routes/web/profile.php`):
```
GET /home/payments → Profile\PaymentHistoryController@index     (Blade)
GET /home/payments/json/list → Profile\PaymentHistoryController@list  (JSON)
```

**Новый контроллер** `app/Http/Controllers/Profile/PaymentHistoryController.php`:
- `index()` — Blade-страница
- `list()` — JSON с пагинацией, фильтр по периоду, сортировка по дате
- Платежи ищутся по пользователю: через счета пользователя (связка user → accounts → invoices → payments)

**API функция** (сгенерируется авто):
- `ApiProfilePaymentHistoryList(getParams)`

#### 2.2. Vue-компонент

```
resources/js/components/profile/payments/
└── PaymentsHistoryBlock.vue  — сводная таблица платежей
```

- Колонки: дата, период, счёт (номер участка), услуга/назначение, сумма, статус
- Фильтр по периоду (select)
- Пагинация
- Возможно: ссылка на соответствующий счёт

#### 2.3. Blade-страница

```
resources/views/pages/profile/payments/index.blade.php  — <payments-history-block />
```

#### 2.4. Навигация

Добавить пункт «Платежи» (`fa fa-credit-card`) в `top-nav.blade.php`.

#### 2.5. Маршруты (RouteNames)

```php
public const string PROFILE_PAYMENTS_INDEX = 'profile.payments.index';
```

#### 2.6. Регистрация компонентов

```js
import PaymentsHistoryBlock from '@components/profile/payments/PaymentsHistoryBlock.vue';
```

---

## Порядок работ

1. **Help Desk — API**
   - Создать `Profile\HelpDeskController` с методами `list`, `view`
   - Добавить маршруты в `routes/web/profile.php`
   - Добавить константы в `RouteNames`
   - Добавить импорт `TicketService` и `TicketSearcher` с фильтром по `userId`

2. **Help Desk — Frontend**
   - Создать `TicketsBlock.vue`, `TicketItem.vue`, `TicketView.vue`
   - Создать Blade-шаблоны
   - Зарегистрировать в `profile.js`
   - Обновить `top-nav.blade.php`
   - Собрать (`make yarn-build`)

3. **История платежей — API**
   - Создать `Profile\PaymentHistoryController` с методами `index`, `list`
   - Добавить маршруты и константы
   - Поиск платежей через связку `user → accounts → invoices → payments`

4. **История платежей — Frontend**
   - Создать `PaymentsHistoryBlock.vue`
   - Создать Blade-шаблон
   - Зарегистрировать в `profile.js`
   - Обновить `top-nav.blade.php`
   - Собрать (`make yarn-build`)

5. **Финальные проверки**
   - `make architecture`
   - `make tests`
