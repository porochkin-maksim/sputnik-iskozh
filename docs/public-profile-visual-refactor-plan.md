ye;yj # Public/Profile Visual Refactor Plan

Цель: довести `resources/views` и `resources/js/components/public|profile` до одного фирменного визуального языка без ломки существующих контрактов, маршрутов и поведения.

## 1. Что уже видно по аудиту

- `resources/views` и `resources/js/components/public|profile` уже имеют базовую структуру, но визуально остаются разнородными.
- В `Blade` смешаны:
  - page-level шаблоны,
  - shared partials,
  - legacy-обвязка в `layouts/partial/*`,
  - локальные карточки/таблицы без единого ритма.
- В `Vue` повторяются разные визуальные паттерны:
  - карточки и панели с разными отступами,
  - таблицы с разной плотностью,
  - ссылки/кнопки без общего контракта,
  - формы и списки с локальными style-исключениями.
- Public и profile визуально решают похожие задачи, но выглядят как разные подсистемы.

## 2. Основные цели

1. Свести public/profile к общему design language.
2. Уплотнить таблицы и списки без потери читаемости.
3. Сделать карточки, панели и формы визуально более фирменными.
4. Убрать Bootstrap-default ощущение там, где это бросается в глаза.
5. Не ломать рабочие контракты, валидацию, маршруты и права доступа.

## 3. Приоритеты работ

1. Shell и общие layout-слои.
2. Shared visual patterns: page-title, cards, link/button styles, empty states.
3. Public pages: index, contacts, help-desk, news, files, search, legal pages.
4. Profile pages: account summary, invoices, counters, password/profile forms.
5. Vue common layer: `common/*`, `public/*`, `profile/*`.

## 4. План по `resources/views`

### 4.1. Shell-обвязка

- Свести к общему визуальному ритму:
  - [layouts/app-layout.blade.php](/web/sputnik-iskozh/resources/views/layouts/app-layout.blade.php)
  - [layouts/profile-layout.blade.php](/web/sputnik-iskozh/resources/views/layouts/profile-layout.blade.php)
- Проверить:
  - ширину контейнеров,
  - spacing вокруг `<main>`,
  - footer placement,
  - поведение sticky/navigation blocks.
- Убрать разницу между public/profile, если она не несёт смысловой нагрузки.

### 4.2. Public pages

- Привести к единому визуальному ритму:
  - `pages/public/index.blade.php`
  - `pages/public/contacts/*.blade.php`
  - `pages/public/news/*.blade.php`
  - `pages/public/help-desk/*.blade.php`
  - `pages/public/search.blade.php`
  - `pages/public/files/index.blade.php`
  - `pages/public/garbage.blade.php`
- Нормализовать:
  - hero/lead blocks,
  - request cards,
  - list pages,
  - page headings,
  - content spacing,
  - table/empty-state presentation.

### 4.3. Legal pages

- `privacy`, `terms`, `personal-data-consent`, `payments-info`, `cookie`:
  - единый спокойный typography pattern,
  - более понятная иерархия секций,
  - менее «простынный» ритм,
  - выделение важных блоков и ссылок.

### 4.4. Profile pages

- Сделать profile более “личным” и менее шаблонным:
  - account summary blocks,
  - invoices pages,
  - counters pages,
  - profile/password forms.
- Избавиться от визуальной смеси табличных и карточных паттернов без причины.

### 4.5. Shared partials/components

- Привести к единому стилю:
  - `partials/public/*`
  - `partials/profile/*`
  - `layouts/partial/*`
  - `components/*`
- Приоритетные элементы:
  - page-title,
  - legal-links,
  - request-card,
  - requests-section,
  - social-strip,
  - auth form cards/rows/actions,
  - admin/profile shared card patterns.

## 5. План по `resources/js/components/public`

### 5.1. Public page blocks

- [resources/js/components/public/pages/IndexPage.vue](/web/sputnik-iskozh/resources/js/components/public/pages/IndexPage.vue)
- [resources/js/components/public/pages/SingleColumnPage.vue](/web/sputnik-iskozh/resources/js/components/public/pages/SingleColumnPage.vue)
- [resources/js/components/public/pages/TwoColumnsPage.vue](/web/sputnik-iskozh/resources/js/components/public/pages/TwoColumnsPage.vue)
- [resources/js/components/public/news/*](/web/sputnik-iskozh/resources/js/components/public/news)
- [resources/js/components/public/help-desk/*](/web/sputnik-iskozh/resources/js/components/public/help-desk)
- [resources/js/components/public/files/*](/web/sputnik-iskozh/resources/js/components/public/files)
- [resources/js/components/public/search/*](/web/sputnik-iskozh/resources/js/components/public/search)

### 5.2. Public forms

- Выровнять:
  - `CounterForm.vue`
  - `PaymentForm.vue`
  - `HelpDeskForm.vue`
- Закрепить один UX-контракт:
  - одинаковые поля,
  - одинаковая подача ошибок,
  - одинаковые CTA,
  - одинаковые consent/policy patterns.

### 5.3. Public visual helpers

- Перевести повторяющиеся куски в shared-компоненты:
  - request card,
  - request section,
  - empty state,
  - list header,
  - file/news blocks.

## 6. План по `resources/js/components/profile`

### 6.1. Profile blocks

- [resources/js/components/profile/account/*](/web/sputnik-iskozh/resources/js/components/profile/account)
- [resources/js/components/profile/counters/*](/web/sputnik-iskozh/resources/js/components/profile/counters)
- [resources/js/components/profile/PasswordBlock.vue](/web/sputnik-iskozh/resources/js/components/profile/PasswordBlock.vue)
- [resources/js/components/profile/ProfilePassword.vue](/web/sputnik-iskozh/resources/js/components/profile/ProfilePassword.vue)

### 6.2. Что унифицировать

- account summary cards,
- invoices/counters tables,
- action bars,
- modal/dialog styles,
- empty/loading states,
- profile forms.

## 7. План по `resources/sass`

### 7.1. Роли файлов

- `variables.scss` оставить источником токенов.
- `common.scss` использовать для cross-page helpers.
- `layout.scss` использовать для shell/background spacing.
- `form.scss` использовать для общих форм/ошибок.
- `template.scss` и `crutches.scss` продолжить разбирать и вычищать.

### 7.2. Что стандартизировать

- radius,
- shadows,
- border tone,
- spacing scale,
- hover/active states,
- table density,
- card density,
- link/button variants.

### 7.3. Общие визуальные паттерны

- `link-firm` как единый link-style класс.
- `admin-table-firm` для админских таблиц.
- `admin-toolbar` для action/tool blocks.
- `table-thin-column` для узких технических колонок.
- единый card look для public/profile.

## 8. Стандарты, которые нужно зафиксировать

- Любая новая page-level разметка сначала проверяется на наличие shared partial/component.
- Любая новая table/list должна использовать общий table style и `table-thin-column` там, где это узкая техническая колонка.
- Любая action-link/button визуализация должна идти через общий класс, а не локальную импровизацию.
- Любой `btn-link` в public/profile должен считаться временным, если это не встроенный системный паттерн.
- Любой inline style в blade должен быть кандидатом на вынос в Sass.
- Любой повторяющийся pattern в Vue должен быть вынесен в shared component/composable.
- Public и profile визуально должны ощущаться одним продуктом, а не двумя разными подсистемами.

## 9. Порядок внедрения

1. Сначала shell + shared partials.
2. Затем public pages и public Vue blocks.
3. Потом profile pages и profile Vue blocks.
4. После этого вычистить хвосты в Sass.
5. Финально прогнать `make prod` и smoke-check основных public/profile маршрутов.

## 10. Definition of Done

- Public и profile страницы читаются как единый визуальный язык.
- Основные страницы имеют ровный ритм, понятные секции и согласованные карточки/таблицы.
- Shared classes используются последовательно.
- Старые точечные визуальные исключения сведены к минимуму.
- `make prod` проходит без регрессий.

## 11. Статус выполнения

- Выполнен первый пакет:
  - shell-слой `layouts/app-layout.blade.php` и `layouts/profile-layout.blade.php` переведён на `page-shell`;
  - public landing и ключевые public страницы получили `page-hero`, `page-card`, `info-table`;
  - profile landing и profile counters/invoices получили общий визуальный ритм;
  - shared public partials `request-card`, `requests-grid`, `requests-section`, `social-strip` приведены к более цельному виду;
  - добавлены базовые фирменные блоки в Sass: `page-shell`, `page-hero`, `page-card`, `page-section`, `info-table`.
- Выполнен второй пакет:
  - `news/show`, `contacts/requests`, `contacts/counter`, `contacts/payment`, `help-desk/*`, `search`, `announcement`, `garbage` приведены к единому `page-hero/page-card/page-section` каркасу;
  - help-desk categories list получил более фирменные `link-firm` ссылки и карточный ритм;
  - public формы и сервисные страницы стали визуально плотнее и спокойнее без изменения логики;
  - оставшиеся явные bootstrap-default блоки на публичных страницах сокращены.
- Выполнен третий пакет:
  - `privacy`, `terms`, `cookie`, `personal-data-consent`, `payments-info` приведены к общему `page-hero/page-card` паттерну;
  - `profile/index`, `profile/counters/index` получили более цельный hero/card ритм;
  - legal pages стали выглядеть как часть того же продукта, а не отдельные простыни;
  - базовые public/profile страницы теперь сильнее завязаны на единый визуальный каркас.
- Выполнен пакет public files/folders:
  - раздел файлов и папок переведён на секции `Папки`/`Документы` с отдельными счётчиками;
  - dropdown-меню в папках и файлах заменены на явные inline action buttons;
  - папки стали grid-коллекцией карточек, документы - более плотным card-list;
  - старый `components/folders.scss` импорт убран из общего bundle, чтобы не перебивал новый public card-слой.
- Выполнен пакет public files page:
  - [resources/js/components/public/files/FilesBlock.vue](/web/sputnik-iskozh/resources/js/components/public/files/FilesBlock.vue) получил собственную шапку с заголовком, счётчиком и upload action;
  - список файлов стал цельным блоком без ощущения пустой карточки;
  - карточки документов и управление ими теперь визуально согласованы с папками и остальной public-частью.
- Выполнен readonly pass для files/folders:
  - убраны лишние подписи внутри карточек папок и файлов;
  - папки вернулись в более тонкий list-вид вместо плитки;
  - readonly-режим стал компактнее по вертикальным отступам;
  - названия длинных папок теперь не ломают композицию плитки.
  - верхняя навигация вынесена отдельно от controls и оформлена как breadcrumb-pill;
  - readonly скрывает только действия управления, но не навигацию по папкам.
- Выполнен auth/system shell pass:
  - `public/system/token-auth.blade.php` и `auth/*` переведены на более цельный auth-form card shell;
  - `components/auth/form-card`, `form-row`, `form-actions` получили общий фирменный каркас;
  - login/register/reset/confirm/verify/token-auth больше не выглядят как старый bootstrap scaffold;
  - вторичные auth-ссылки и action bar приведены к одному стилю.
- Осталось добить:
  - финальный микро-pass по `public/news/*`, `public/contacts/*`, `public/help-desk/*`, `public/search.blade.php`, `public/files/index.blade.php`, если где-то ещё остались лишние отступы или bootstrap-default детали;
  - оставшиеся shared Sass overrides и точечные layout-исключения в `template.scss` / `crutches.scss`, чтобы базовый visual language был полностью сводим к общим utility-классам;
  - финальный smoke-check public/profile на desktop/mobile и с модалками.
