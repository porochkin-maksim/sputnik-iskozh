# Frontend Standardization Plan

План основан на текущем Laravel + Vite + Vue 3 проекте и backend-generated frontend artifacts.

## Текущее состояние

- Vue components: около 100 файлов.
- Composition API уже преобладает: 74 компонента используют `<script setup>`.
- Options API остаётся примерно в 28 компонентах.
- Новый generated API слой есть: `resources/js/api/index.js`, `resources/js/api/helpers.js`, alias `@api`.
- Legacy route слой ещё жив: `resources/js/routes-functions.js`, `resources/js/routes.json`, `resources/js/utils/Url.js`.
- Direct axios/window usage остаётся примерно в 20 JS/Vue файлах.
- jQuery используется глобально в `bootstrap.js`, `utils/common.js`, `utils/menus/*` и частично в Blade legacy.
- Store на Vuex содержит только глобальные модули `alerts`, `auth`, `permissions`.
- `front:export-request-arguments-command` сейчас отключён ранним `return`, при этом `resources/js/utils/request-arguments.js` существует как ручной/устаревший контракт.
- Есть крупные SFC, которые уже требуют декомпозиции: `PeriodPaymentsImportBlock.vue` 655 строк, `UserItemView.vue` 645, `FoldersBlock.vue` 528, `CounterHistoryBlock.vue` 520, `TicketsView.vue` 461.
- `scripts/check-architecture.sh` теперь фиксирует frontend baseline: новые direct axios, legacy route helpers, jQuery в Vue, Options API и компоненты больше 320 строк блокируются через `scripts/frontend-architecture-allowlist.txt`.
- Frontend и PHP tooling должны запускаться через Docker/Sail: `make yarn-build`, `make yarn-watch`, `make artisan ...`, `make composer ...`.

## Цель

Сделать frontend предсказуемым для backend-разработчика:
- routes/enums/request keys синхронизируются backend-командами;
- Vue components не собирают URL и payload contracts вручную;
- HTTP, ошибки, permissions, formatting имеют единые patterns;
- legacy jQuery/Options API мигрируют постепенно, без большой переписи.

## Рабочий порядок

Все дальнейшие frontend-рефакторинги выполняются в этом порядке:

1. Сначала синхронизировать `scripts/frontend-architecture-allowlist.txt` с реальным состоянием кода.
   - Удалять из allowlist уже исправленные компоненты.
   - Оставлять только актуальные исключения с одной явной причиной.
   - Не использовать allowlist как архив старого debt.
2. Затем добирать runtime и legacy compatibility layer.
   - Остаточные `window.axios`, legacy route helper consumers, generated contract drift, compatibility imports.
3. Затем приводить `common`-слой к канонической структуре.
   - Убирать domain-specific blocks.
   - Распиливать перегруженные shared components.
4. Затем чистить entrypoints и registrations.
   - `app.js`, `admin.js`, `profile.js`.
5. И только после этого делать новые большие structural распилы Vue-blocks.
   - Новые распилы не должны отвлекать от актуализации allowlist и runtime debt.

Обязательный ритуал перед каждым новым frontend-рефакторингом:

1. Сверить чек-лист остатка с текущим кодом.
2. Обновить статусы чек-листа по фактическому состоянию.
3. Синхронизировать `scripts/frontend-architecture-allowlist.txt`, если затрагиваются крупные Vue blocks.
4. Только после этого переходить к коду.

Если чек-лист не обновлён, новый рефакторинг не считается начатым.

## Чек-лист остатка

Этот список живой. Перед каждым новым frontend-рефакторингом сначала обновляй его по фактическому коду.

### Allowlist

- [x] Синхронизировать `scripts/frontend-architecture-allowlist.txt` с текущим кодом.
- [x] Удалить из allowlist компоненты, которые уже исправлены.
- [x] Оставить только актуальные нарушения с одной явной причиной на файл.

### Runtime / legacy JS

- [ ] Убрать оставшиеся `window.axios` consumers.
- [x] Довести `resources/js/routes-functions.js` и `resources/js/utils/Url.js` до окончательного legacy-removal состояния.
- [x] Довести `resources/js/utils/request-arguments.js` до одного канонического контракта или удалить, если он больше не нужен.
- [x] Добить remaining direct axios / jQuery usage в Vue-коде.
- [x] Привести все access gates к виду `const canEdit = has('section', 'edit')`.
- [x] Отделить item-state `actions.*` от permissions gates и не смешивать их в одном шаблоне.

### Common layer

- [ ] Вынести domain-specific blocks из `resources/js/components/common`.
- [x] Разбить `form/CustomCalendar.vue` на smaller components/composables.
- [x] Разбить `form/SearchSelect.vue` на smaller components/composables.
- [ ] Разбить `files/FileUploader.vue` на smaller components/composables.

### Entrypoints

- [x] Вынести formatter logic из `resources/js/app.js`.
- [x] Вынести registrations из `resources/js/app.js`, `resources/js/admin.js`, `resources/js/profile.js`.
- [x] Свести entrypoints к thin composition roots.

### Blade / layout

- [ ] Довести `public/news/*`, `public/contacts/*`, `public/system/token-auth.blade.php`, `auth/*` до общего shell pattern.
- [ ] Нормализовать shared visual patterns через Sass utilities и partials.

### Vue structural debt

- [x] Пройтись по remaining oversized Vue blocks после sync allowlist.
- [x] Декомпозировать только те blocks, которые всё ещё нарушают лимиты.
- [x] Не начинать новый structural split, пока allowlist и runtime debt не актуализированы.

### Current remaining size debt

- [x] `resources/js/components/common/form/CustomCalendar.vue`
- [x] `resources/js/components/profile/counters/CounterItem.vue`

## Специальный срез: Vite и `components/common`

Анализ по `vite.config.js`, entrypoints и `resources/js/components/common` показывает отдельный пласт долга, который
нужно планировать не как мелкие правки, а как структурную миграцию.

### Наблюдения

- `vite.config.js` в целом настроен корректно: multi-entry (`app.js`, `admin.js`, `profile.js`), Vue plugin, alias layer.
- Проблема не в Vite как таковом, а в том, что он собирает слишком разнородный `common` слой.
- `resources/js/components/common` сейчас смешивает:
  - UI primitives (`CustomInput`, `CustomSelect`, `CustomTextarea`, `ErrorsList`);
  - infra/adapters (`ViewDialog`, `Wrapper`, editor wrappers, chart wrappers, file uploader);
  - domain-ish blocks (`SummaryBlock`, `CountersChartBlock`, `CounterItemChartBlock`, `AccountSearchSelect`).
- Крупнейшие common-компоненты уже являются architecture debt:
  - `form/CustomCalendar.vue` — 412 строк;
  - `form/SearchSelect.vue` — 379 строк;
  - `files/FileUploader.vue` — 233 строки;
  - `blocks/SummaryBlock.vue` — 239 строк;
  - `blocks/CountersChartBlock.vue` — 199 строк.
- Entry points остаются composition root, но уже содержат лишнюю инфраструктурную и formatting-логику:
  - глобальные formatter functions в `resources/js/app.js`;
  - длинные списки `app.component(...)` в `app.js`, `admin.js`, `profile.js`;
  - зависимость от глобального `window.userPermissions`.
- Generated API слой существует, но `resources/js/api/index.js` по-прежнему жёстко опирается на `window.axios`.
- `resources/js/bootstrap.js` остаётся глобальным legacy adapter слоем для `axios`, `jquery`, `bootstrap`, `lightbox` и
  `DOMContentLoaded`-инициализации.

### Отдельная цель по этой зоне

Сделать `common` действительно shared-слоем, а не складом всех повторяющихся компонентов, и привести entrypoints/Vite
сборку к роли чистого composition root без глобальной бизнес- и formatting-логики.

## Blade / public / profile HTML blocks

### Текущее состояние

- `resources/views/layouts/*` уже стандартизированы лучше, чем public pages:
  - `app-layout`, `admin-layout`, `profile-layout` используют один и тот же composition pattern;
  - общие social/logo/background patterns частично вынесены в Sass utilities;
  - `metrics`, `nav`, `top-nav`, `footer-nav` остаются thin partials.
- `resources/views/public/*` и `resources/views/layouts/partial/profile/*` всё ещё смешивают:
  - server-side page composition;
  - OpenGraph/route setup;
  - JSON props для Vue;
  - локальную разметку layout blocks;
  - точечные inline style/legacy helper calls.
- Наиболее стандартизированы страницы, где blade уже выполняет только page shell:
  - `public/search.blade.php`;
  - `public/index.blade.php`;
  - `public/news/show.blade.php`;
  - `public/help-desk/*.blade.php`;
  - `public/contacts/*.blade.php` частично.
- Наименее стандартизированы страницы, где blade ещё содержит много ручной сборки контракта страницы:
  - `public/news/show.blade.php` и `public/contacts/*.blade.php` из-за прямой сборки OpenGraph/data props;
  - `public/privacy.blade.php` из-за большой static content page;
  - `public/system/token-auth.blade.php` и auth blade pages, где legacy form markup ещё живёт отдельно.

### Цель

Свести Blade pages к единому стандарту:
- layout partials должны быть thin и повторяемыми;
- public/profile pages должны либо отдавать Vue props в одном месте, либо собирать чистый server-rendered HTML, но не смешивать оба подхода без причины;
- shared visual patterns для layouts и common blocks должны жить в Sass utilities, а не в scattered inline styles.

### План стандартизации Blade / HTML blocks

1. Зафиксировать canonical page shell pattern:
   - layout partials только для `head/meta/nav/footer/metrics/social`;
   - page Blade только для page-specific heading, Vue props и breadcrumbs;
   - не добавлять page-specific CSS/JS в layout partials.
2. Вынести повторяемые visual patterns layouts в Sass:
   - background/logo wrappers;
   - development strip;
   - social/logo image utilities;
   - common spacing wrappers, если они повторяются между `app/admin/profile`.
3. Разделить public pages по уровню стандартизации:
   - `clean shell pages` - `search`, `index`, `help-desk/*`, `contacts/*`;
   - `mixed pages` - `news/show`, `contacts/payment`, `contacts/counter`;
   - `static legacy pages` - `privacy`, `system/token-auth`, `auth/*`.
4. Для mixed pages определить, что уходит в controller/view-model:
   - OpenGraph сборка;
   - route/resource preparation для Vue props;
   - page title/description/url contract;
   - repeated `@json(new Resource(...))` patterns.
5. Для static/legacy pages не трогать всё подряд:
   - сначала выделять общий partial/layout;
   - только потом трогать разметку внутри страницы;
   - не переписывать большие static pages без понятного выигрыша.

### Definition of done

- layouts/partials не содержат случайных inline style overrides;
- page blades не строят один и тот же prop/view contract по-разному;
- `public` и `profile` страницы следуют одинаковому conventions set по композиции и visual utility classes;
- если page всё ещё mixed, это явно отмечено как legacy boundary, а не как случайный код.

## Этап 3A. Пересобрать `common` слой

1. Разделить `resources/js/components/common` на чёткие подслои:
   - `common/ui/*` — чистые primitives и небольшие presentational components;
   - `common/infra/*` — адаптеры для Bootstrap/editor/chart/file-upload;
   - domain-specific блоки вынести из `common` в feature directories.
2. Убрать из `common` всё, что знает о предметной области:
   - `blocks/SummaryBlock.vue`;
   - `blocks/CountersChartBlock.vue`;
   - `blocks/CounterItemChartBlock.vue`;
   - `app/AccountSearchSelect.vue`.
3. Зафиксировать правило: если компонент нельзя использовать без domain context или backend-shaped props, он не должен
   жить в `common`.
4. Для `common` оставить только 3 категории:
   - form primitives;
   - visual feedback (`Alerts`, `LoadingOverlay`, `LoadingSpinner`);
   - neutral wrappers/adapters.

Definition of done:
- в `common` не остаётся domain-specific blocks;
- структура `common` читается по назначению, а не по историческим накоплениям;
- новые reusable components попадают только в `ui` или `infra`.

## Этап 3B. Декомпозиция базовых перегруженных common-компонентов

1. Разделить `form/CustomCalendar.vue`:
   - container orchestration;
   - input/display adapter;
   - dropdown shell;
   - time picker block;
   - calendar navigation/grid composables.
2. Разделить `form/SearchSelect.vue`:
   - input + dropdown shell;
   - selection state composable;
   - keyboard navigation composable;
   - multi-select rendering отдельно от single-select поведения.
3. Разделить `files/FileUploader.vue`:
   - file validation/composable;
   - pending files list;
   - existing files list;
   - uploader trigger component.
4. Проверить `ViewDialog.vue` и `Wrapper.vue` на overlap responsibility и оставить один канонический pattern для overlay
   / modal shell.

Definition of done:
- `CustomCalendar.vue` и `SearchSelect.vue` перестают быть монолитами;
- повторяемая логика вынесена в composables;
- modal/overlay поведение не дублируется в двух разных базовых компонентах без причины.

## Этап 3C. Очистить entrypoints

1. Вынести formatter functions из `resources/js/app.js` в `resources/js/composables/useFormat.js` или
   `resources/js/utils/format/*`.
2. Оставить `app.config.globalProperties` только как deprecated compatibility shim для существующих компонентов.
3. Разделить глобальные registrations:
   - `resources/js/registrations/app.js`;
   - `resources/js/registrations/admin.js`;
   - `resources/js/registrations/profile.js`.
4. Проверить, какие компоненты действительно должны быть глобально зарегистрированы из Blade, а какие можно перевести
   на локальный import в page/root components.
5. Не добавлять новую UI/business logic в entrypoints; их роль — только root app setup.

Definition of done:
- `app.js`, `admin.js`, `profile.js` остаются короткими composition roots;
- formatter-ы и registries живут вне entrypoints;
- новые компоненты не добавляются в глобальные registration lists без необходимости.

## Этап 3D. Нормализовать Vite/API runtime границу

1. Оставить `vite.config.js` минимальным и инфраструктурным:
   - aliases только для реально поддерживаемых слоёв;
   - без накопления объясняющих комментариев и временных workaround notes.
2. После стабилизации структуры рассмотреть aliases:
   - `@ui`;
   - `@infra`;
   - `@features`;
   вместо дальнейшего разрастания `@common`.
3. Перевести generated API с `window.axios` на `resources/js/api/client.js`.
4. Оставить `window.axios`, `window.$`, `window.bootstrap` в `bootstrap.js` только как legacy compatibility layer.
5. Не допускать новых зависимостей common-компонентов от `window.*`.

Definition of done:
- Vite config остаётся thin infrastructure file;
- generated API не зависит напрямую от глобала `window.axios`;
- common/shared components не тянут глобальный runtime напрямую.

## Практический backlog по этой зоне

1. Вынести `SummaryBlock`, `CountersChartBlock`, `CounterItemChartBlock`, `AccountSearchSelect` из `common`.
2. Разбить `CustomCalendar.vue`.
3. Разбить `SearchSelect.vue`.
4. Разбить `FileUploader.vue`.
5. Свести `Wrapper.vue` и `ViewDialog.vue` к одному согласованному modal/overlay standard.
6. Вынести formatter-ы из `app.js`.
7. Вынести component registration lists из `app.js`, `admin.js`, `profile.js`.
8. Перевести `resources/js/api/index.js` на `api/client.js`.

Definition of done:
- после каждой задачи `common` становится уже и чище;
- новые feature components не попадают в `common` по инерции;
- entrypoints и runtime слой сокращаются, а не разрастаются.

## Приоритетный backlog на 1-2 спринта

### P0

1. Вынести из `common` все domain-specific компоненты:
   - `blocks/SummaryBlock.vue`;
   - `blocks/CountersChartBlock.vue`;
   - `blocks/CounterItemChartBlock.vue`;
   - `app/AccountSearchSelect.vue`.
2. Разбить `form/CustomCalendar.vue` на container + subcomponents + composables.
3. Разбить `form/SearchSelect.vue` на shell + selection logic + keyboard navigation.
4. Перевести generated API с `window.axios` на `resources/js/api/client.js`.

Критерий завершения:
- `common` перестаёт содержать очевидные feature/domain блоки;
- самые тяжёлые shared form-компоненты перестают быть монолитами;
- новый API слой больше не зависит напрямую от `window.axios`.

### P0.1

1. Перед любым новым распилом сначала сверять `scripts/frontend-architecture-allowlist.txt`.
2. Если компонент уже исправлен, убирать его из allowlist сразу в том же проходе.
3. Если allowlist растёт, это должно быть временным следствием нового debt, а не способом скрыть невыполненный рефакторинг.

Критерий завершения:
- allowlist отражает только текущий debt;
- ни один исправленный компонент не остаётся в allowlist по инерции;
- новый structural debt не смешивается со старым.

### P1

1. Разбить `files/FileUploader.vue`.
2. Вынести formatter-ы из `resources/js/app.js` в `useFormat` или `utils/format/*`.
3. Вынести глобальные registrations из `app.js`, `admin.js`, `profile.js` в отдельные registration modules.
4. Свести `Wrapper.vue` и `ViewDialog.vue` к одному каноническому modal/overlay pattern.

Критерий завершения:
- entrypoints становятся короче и остаются composition root;
- modal/overlay паттерн в common не дублируется;
- file/upload logic не размазана по одному большому компоненту.

### P2

1. Очистить `resources/js/bootstrap.js` до legacy adapter слоя без дальнейшего роста.
2. После стабилизации структуры пересмотреть alias-слой в `vite.config.js`:
   - `@ui`;
   - `@infra`;
   - `@features`.
3. Ужесточить quality gates:
   - запрет новых feature-компонентов в `common`;
   - запрет новых зависимостей от `window.*` в Vue;
   - контроль размера shared-компонентов.

Критерий завершения:
- legacy runtime globals не расширяются;
- структура импортов соответствует новым слоям;
- архитектурные проверки начинают удерживать новую структуру автоматически.

## Этап 0. Зафиксировать baseline и не увеличивать legacy

1. Держать `scripts/frontend-architecture-allowlist.txt` как список известных нарушений, а не как постоянное разрешение.
2. Если строка в allowlist меняется из-за роста legacy-компонента, не обновлять baseline автоматически: сначала декомпозировать или вынести HTTP/API/composable.
3. Новый SFC больше 320 строк не допускается.
4. Новый direct `window.axios`/`axios.*` в Vue components не допускается.
5. Новый `Url.Routes`/`Url.Generator`/`routes-functions` в Vue components не допускается.
6. Новый Options API в Vue components не допускается, кроме обоснованного adapter-слоя для внешней библиотеки.
7. `make yarn-build` остаётся обязательным для frontend-задач; `bash scripts/check-architecture.sh` или `make architecture` обязателен для backend/frontend architecture-задач.

Definition of done:
- architecture check проходит без расширения frontend allowlist;
- если allowlist сокращён, это считается частью результата задачи;
- если allowlist расширен, в diff есть явное объяснение в плане миграции.

## Этап 1. Зафиксировать generated contracts

1. Привести все generators в `app/Console/Commands/Front` к единому формату header.
2. Убрать timestamp из `front:export-enum`, чтобы генерация была идемпотентной.
3. Сделать `ExportRouteListCommand` и `ExportRouteFunctionsListCommand` использующими общий route metadata builder, чтобы route filtering/args/methods не расходились.
4. Исправить `makeQuery` в `resources/js/api/helpers.js`: использовать `URLSearchParams`, корректно сохранять `0`, `false`, пустые строки по явным правилам.
5. Проверить `front:export-request-arguments-command`: либо восстановить импорт/enum и включить генерацию, либо удалить команду и заменить контракт другим generated источником.
6. Использовать `make yarn-build` как основной frontend sync/build flow; если нужен отдельный sync без build, доработать существующие `make js-routes` / generator targets.

Definition of done:
- повторный запуск generators без backend изменений не меняет diff;
- generated files помечены как read-only для ручных правок;
- новый код использует `@api`.

## Этап 2. Нормализовать HTTP слой

1. Создать единый axios instance/module, например `resources/js/api/client.js`.
2. Оставить `window.axios` только как legacy compatibility в `bootstrap.js`.
3. Сгенерированный `resources/js/api/index.js` должен использовать `client`, а не `window.axios`.
4. Вынести нормализацию ошибок в один модуль, чтобы `useResponseError` и legacy `ResponseError` не расходились.
5. Перевести компоненты с direct `axios/window.axios` на `@api` или feature API service.

Definition of done:
- direct axios в `.vue` не появляется в новом коде;
- `422/403/404/5xx` обрабатываются одинаково;
- uploads продолжают работать через `prepareRequestData`.

## Этап 3. Упорядочить entrypoints

1. Вынести глобальные formatter functions из `app.js` в `useFormat`/pure utility и оставить globalProperties только как deprecated shim.
2. Разделить registration lists для `app.js`, `admin.js`, `profile.js` на небольшие modules.
3. Проверить, какие компоненты реально должны быть глобальными, а какие можно импортировать локально.
4. Тяжёлые editors/charts подключать локально или dynamic import там, где это снижает bundle.

Definition of done:
- entrypoints остаются composition root, без бизнес-логики;
- новые components регистрируются локально, если не нужны в Blade как root tags.

## Этап 4. Стабилизировать компонентные patterns

1. Для новых компонентов использовать `<script setup>`, props/emits/composables.
2. Existing Options API не переписывать без причины, но при значимой правке переводить на Composition API.
3. Убрать `ResponseError` mixin из новых компонентов; использовать `useResponseError`.
4. Для форм ввести единый pattern: `form`, `errors`, `loading`, `submit`, `reset`, backend validation mapping.
5. Для списков ввести единый pattern: `items`, `total`, `filters`, `sort`, `pagination`, `loading`, `empty`, `error`.
6. Разделить container/list/item/form/dialog роли: `*Block` загружает и координирует, `*List` отображает коллекцию, `*Item` отображает одну сущность, `*Form` владеет model/errors/submit.
7. Декомпозировать компоненты больше 400 строк перед значимыми feature-правками.
8. Первые кандидаты на split: `PeriodPaymentsImportBlock.vue`, `UserItemView.vue`, `FoldersBlock.vue`, `CounterHistoryBlock.vue`, `TicketsView.vue`, `UsersBlock.vue`, `PaymentsBlock.vue`, `RolesBlock.vue`, `CustomCalendar.vue`, `InvoicesBlock.vue`.

Definition of done:
- новые feature blocks выглядят одинаково по loading/error/form/list mechanics;
- store не используется для локального form/list state.
- компоненты из top-10 постепенно выходят из `scripts/frontend-architecture-allowlist.txt`.

## Этап 5. Ограничить jQuery и Bootstrap runtime

1. Зафиксировать `utils/menus/*` и `utils/common.js` как legacy adapter слой.
2. Не использовать `$()` внутри Vue-controlled DOM.
3. Для Bootstrap modal/dropdown/tooltip в Vue сделать composables/wrapper components с cleanup.
4. Заменить copy-to-clipboard из `utils/common.js` на native Clipboard API в composable.

Definition of done:
- новый Vue code не зависит от jQuery;
- dynamic Vue markup не полагается на `DOMContentLoaded` initializers.

## Этап 6. Frontend quality gates

1. Добавить `make yarn-build` в обязательную проверку frontend задач.
2. Добавить ESLint для JS/Vue.
3. Добавить Prettier или другой единый formatter для JS/Vue/Sass.
4. Добавить grep-based frontend architecture check:
   - новые `window.axios` в `.vue`;
   - новые `$(` / `jQuery` в `.vue`;
   - ручные imports из `routes-functions.js`;
   - новые `Url.Routes`/`Url.Generator` в `.vue`;
   - новый Options API;
   - Vue components больше 320 строк;
   - ручные правки generated headers.
5. Подключить frontend checks рядом с `make architecture`.

Definition of done:
- CI ловит frontend architectural regressions до review.
- allowlist сокращается, а не растёт.

## Этап 7. CSS/Sass порядок

1. Разделить global Sass на base/layout/components/utilities.
2. Убрать `crutches.scss` постепенно: каждое правило либо переносится в нормальный слой, либо удаляется.
3. Для Vue components оставить scoped CSS только для локальных деталей.
4. Не добавлять новые глобальные стили из feature components.

Definition of done:
- глобальные стили предсказуемы;
- feature change не ломает unrelated pages.

## Рекомендуемый порядок первых задач

1. Починить generated route/enums artifacts: header, idempotency, shared route metadata builder.
2. Перевести generated API с `window.axios` на `api/client.js`.
3. Перевести `QueueManager.vue` с direct axios и Options API на `@api` + `<script setup>`.
4. Перевести file components с `Url.Routes`/`routes-functions.js` на `@api`.
5. Добавить `make yarn-build` и frontend architecture grep в общий checklist.
