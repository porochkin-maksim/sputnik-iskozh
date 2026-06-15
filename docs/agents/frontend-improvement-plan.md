# План улучшения фронтенда (публичная часть + профиль)

## Условные обозначения

| Метка | Значение |
|-------|----------|
| 🔴 | Критически — прямо влияет на UX |
| 🟡 | Важно — техдолг, дублирование |
| 🟢 | Будущее — когда дойдут руки |

---

## 🔴 1. Убрать `location.reload()` из форм

**Где:** `CounterForm`, `PaymentForm`, `HelpDeskForm`, `CounterItem`, `CounterPassportUploader`, `AccountSwitcher`, `Login`, `Register`

**Проблема:** После отправки данных страница перезагружается — моргание, потеря скролла, плохой UX.

**Решение:**
- Сбросить форму + показать success-алерт через `store.dispatch('alerts/addMessage', ...)`
- Перезагрузку оставить только для Login (меняется весь стейт авторизации)

**Пример (`CounterForm.vue`):**
```diff
- location.reload();
+ formReset();
+ store.dispatch('alerts/addMessage', { id: genId(), type: 'success', text: 'Показания отправлены' });
```

---

## 🔴 2. Мобильное подменю (`site-subnav`)

**Где:** `app-layout.blade.php:65` → `resources/views/layouts/partial/public-subnav.blade.php`

**Проблема:** Класс `d-none d-lg-block` прячет подменю на мобильных. Пользователи телефонов не видят ссылки «Файлы», «Мусор», «Регламент».

**Решение:** Перенести ссылки подменю в мобильное меню (`public-topnav`) или показывать в `offcanvas`.

---

## 🟡 3. Объединить `NewsList.vue` и `AnnouncementsList.vue`

**Где:**
- `resources/js/components/public/news/list/NewsList.vue`
- `resources/js/components/public/news/announcements/AnnouncementsList.vue`

**Проблема:** 95% кода идентично, разница только в вызове API.

**Решение:** Один компонент `ContentList.vue` с пропом `fetchFn`:

```vue
<content-list :fetch-fn="newsApi.getList"></content-list>
<content-list :fetch-fn="announcementsApi.getList"></content-list>
```

---

## 🟡 4. Убрать дубликат `Restore.vue` / `RestorePage.vue`

**Где:**
- `resources/js/components/public/auth/Restore.vue`
- `resources/js/components/public/auth/RestorePage.vue`

**Проблема:** Два одинаковых компонента (по 43 строки).

**Решение:** RestorePage переиспользует Restore через импорт либо удалить дубликат.

---

## 🟡 5. `IndexPage.vue` — переиспользовать `NewsList`

**Где:** `resources/js/components/public/pages/IndexPage.vue`

**Проблема:** На главной — собственная рукописная сетка новостей, не совпадающая по стилю со страницей `/news`.

**Решение:** Вставить `<news-list :short="true" :limit="6"></news-list>` вместо `v-for` с карточками.

---

## 🟡 6. Дубликаты в CSS

**Где:** `resources/sass/components/public.scss`

- `.news-item` определён дважды (строка 219 и строка 453)
- `.public-folders-block--readonly` объявлен дважды (строка 702 и 974)
- Отсутствуют CSS-переменные для радиусов, теней, отступов

**Решение:** Убрать дубликаты, вынести повторяющиеся значения в `variables.scss` как CSS-переменные `--radius-card`, `--radius-pill`, `--shadow-card`.

---

## 🟢 7. Стек уведомлений (Alerts)

**Где:** `resources/js/components/common/Alerts.vue` + `resources/js/store/alerts.js`

**Проблема:** Нет ограничения на количество — 10 сообщений сломают вёрстку. `addError` и `addMessage` делают одно и то же.

**Решение:**
- Лимит 3-4 одновременных сообщения
- Убрать избыточный `addError` (оставить `addMessage`)
- Разный таймаут: success 5s, warning 10s, danger 15s

---

## 🟢 8. Загрузка файлов без индикатора прогресса

**Где:** `CounterForm.vue`, `PaymentForm.vue`

**Проблема:** При загрузке файла пользователь видит только текст «Отправка...», нет progress bar.

**Решение:** Использовать `XMLHttpRequest` с `upload.onprogress` или `axios` с `onUploadProgress`.

---

## 🟢 9. Accessibility

- `<table>` в `StateSchedule.vue` без `scope` на `<th>` и без `<caption>`
- Нет focus-visible стилей для клавиатурной навигации
- Нет `aria-label` на иконках-кнопках (кроме `ShareButton`)

---

## 🟢 10. Заменить `confirm()` на модальный диалог

**Где:** `NewsItem`, `PageEditor`, `FileItem`, `CountersBlock`

**Проблема:** Нативный `confirm('Вы уверены?')` не стилизуется, выглядит инородно.

**Решение:** Создать `ConfirmDialog.vue` — промис-обёртку:

```vue
<!-- Использование -->
<confirm-dialog ref="confirmDialog"></confirm-dialog>

const confirmed = await confirmDialog.value.open('Удалить новость?');
if (confirmed) { /* delete */ }
```

---

## Что уже хорошо (не трогать)

- Единая система карточек (одинаковые `border-radius`, тени, hover-эффекты)
- BEM-префиксы (`.public-*`, `.profile-*`) — хорошая изоляция
- Сезонный фон spring/summer/autumn/winter
- Breadcrumbs на всех страницах
- Cookie-баннер по 152-ФЗ
- `ShareButton` с Web Share API + clipboard fallback
- Формы используют единые `CustomInput`, `CustomSelect`, `CustomCheckbox`
- Аккуратная работа с `overflow-x: hidden` и `min-width: 0` для длинных строк

---

## Порядок выполнения (приоритет)

| # | Задача | Метка | Время | Эффект |
|---|--------|-------|-------|--------|
| 1 | Убрать `location.reload()` из форм заявок | 🔴 | 1ч | ✅ |
| 2 | Мобильное подменю | 🔴 | 1ч | ✅ |
| 3 | Объединить NewsList / AnnouncementsList | 🟡 | 1ч | ✅ |
| 4 | Убрать Restore-дубликат | 🟡 | 15м | — |
| 5 | IndexPage → NewsList | 🟡 | 1ч | ✅ |
| 6 | CSS-дубликаты + переменные | 🟡 | 1ч | ✅ |
| 7 | Стек уведомлений | 🟢 | 1ч | ✅ |
| 8 | Progress bar загрузки | 🟢 | 2ч | ✅ |
| 9 | A11y | 🟢 | 1ч | ✅ |
| 10 | Заменить confirm() на модалку | 🔴 | 2ч | ➡️ перенесён |