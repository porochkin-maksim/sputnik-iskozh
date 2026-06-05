# Frontend Architecture Standard

Этот документ дополняет backend правила из `ARCHITECTURE.md` и фиксирует стандарты разработки frontend части проекта на
Laravel + Vite + Vue 3.
Команды frontend tooling запускать только через Docker/Sail (`make yarn ...`, `make yarn-build`, `make yarn-watch`), не
через локальные `npm`/`yarn`/`node`.

Цель:

- держать Vue-код предсказуемым и тестируемым;
- не смешивать Blade, jQuery, Bootstrap runtime и бизнес-сценарии внутри компонентов;
- постепенно снижать legacy-глобалы без большой переписи.

## 1. Текущий стек

- Laravel Blade остаётся server-rendered оболочкой страниц.
- Vite собирает frontend entrypoints.
- Vue 3 используется для интерактивных блоков.
- Vuex уже используется как глобальное состояние. Новый state management не вводить без отдельного решения.
- Axios уже подключён в `resources/js/bootstrap.js`.
- Bootstrap 5 используется как UI foundation.
- В проекте есть редакторы TinyMCE, TipTap, Quill, Chart.js, Lightbox, Echo/Pusher.

## 2. Слои frontend кода

### Entry points

Файлы `resources/js/app.js`, `resources/js/admin.js`, `resources/js/profile.js` являются composition root.

В entrypoint можно:

- создать Vue app;
- подключить store/plugins;
- зарегистрировать page/root components;
- прочитать минимальные bootstrap данные из `window`.

В entrypoint нельзя:

- писать бизнес-логику;
- делать HTTP-запросы;
- хранить форматирование и domain-specific helper logic;
- раздувать список глобальных регистраций без необходимости.

Если entrypoint растёт, регистрацию компонентов надо выносить в локальные registration modules рядом с entrypoint.

Каждый архитектурный рефакторинг должен завершаться фиксацией найденного правила или паттерна в
`FRONTEND_ARCHITECTURE.md` или `FRONTEND_STANDARDIZATION_PLAN.md`.
Если после изменения появился новый устойчивый стандарт, его нельзя оставлять только в коде или в переписке.
После миграции компонента сразу удаляй неиспользуемые imports; dead imports в `script setup` и Options API файлах
считаются архитектурным мусором.

### Components

Vue components отвечают за UI, user interaction и локальную orchestration.

Компонент может:

- отображать данные;
- управлять локальным UI state;
- вызывать frontend API service/composable;
- эмитить события родителю;
- показывать loading/error/empty states.

Компонент не должен:

- знать Laravel route strings напрямую;
- обращаться к `window.axios` напрямую в новом коде;
- содержать backend business rules;
- использовать jQuery для DOM внутри Vue-controlled subtree;
- использовать jQuery даже для точечного обновления DOM, если это можно сделать через refs или `document.querySelector`;
- мутировать props;
- читать/писать глобальные `window.*`, кроме явно разрешённых bootstrap inputs на границе entrypoint.

### Styles

Глобальные и повторяемые стили shared primitives должны жить в Sass-слоях `resources/sass/*.scss`, а не в scattered
component `<style>` blocks.

Правила:

- если стиль повторяется между несколькими components, он сначала выносится в `resources/sass/form.scss` или
  `resources/sass/components/*.scss`;
- повторяемые admin/page/block styles тоже относятся к Sass-слою: `resources/sass/components/*.scss` является основным
  местом для shared UI styling;
- если для form control уже существует shared primitive в `resources/js/components/common/form/`, raw HTML control в Vue
  component заменяется на primitive; исключения допустимы только для hidden/file/editor surfaces и должны быть явными;
- для compact toolbar/search inputs использовать `InlineInput`; для обычных form/dialog flows использовать
  `CustomInput`;
- `SimpleSelect` допустим для compact/select-only flows; `CustomSelect` использовать, когда нужен label/wrapper/errors
  контракт;
- `hidden`, `file` и editor surfaces считаются отдельными исключениями и не должны превращаться в псевдо-primitive
  только ради единообразия;
- локальный `<style scoped>` в Vue component допустим только для уникального layout/animation/behavior, который не имеет
  смысла тащить в глобальный Sass;
- пустые `<style scoped>` блоки запрещены;
- если style block изменяется ради одного shared primitive, сначала проверить, нельзя ли это выразить общим Sass
  selector-ом на уровне feature tree;
- общие primitives должны ссылаться на один источник визуального правила, а не дублировать одинаковые значения в
  нескольких файлах.
- если repeated layout/styling lives inside one feature block, prefer a dedicated Sass partial over a component-scoped
  style block.
- для повторяющихся auth/public form shell patterns использовать blade components или partials вместо копипасты
  container/row/card scaffolding;
- shared overlay/notification/loading patterns должны жить в Sass-слое, а не в component-scoped styles, если это не
  уникальная анимация или behavior;
- если в Vue component остаётся scoped style, он должен объясняться уникальным local behavior, а не тем, что стиль
  просто не вынесли в Sass.

### Component decomposition

Компонент должен иметь одну причину для изменения. Для feature-компонентов это обычно один из типов:

- page/block container: загрузка данных, permissions, coordination;
- list/table: отображение коллекции, pagination/sort/filter events;
- item/row/card: отображение одной сущности;
- form/dialog: редактирование и submit flow;
- pure UI primitive: input, select, calendar, uploader, empty/error/loading state.

Жёсткие критерии для нового и активно изменяемого кода:

- целевой размер SFC: до 250 строк;
- компонент больше 320 строк считается architecture debt и не должен расти;
- компонент больше 400 строк перед значимой feature-правкой сначала декомпозируется;
- template больше 120 строк надо делить на child components или slots;
- script больше 180 строк надо делить на composables/API services/helpers;
- style больше 120 строк надо переносить в локальные подкомпоненты или нормальный Sass слой;
- больше 8 props или больше 8 emits требует пересмотра границы компонента;
- child component не должен повторно загружать данные, если parent уже владеет списком/формой;
- один компонент не должен одновременно содержать list, item editor, modal lifecycle, HTTP, permissions, formatting и
  file upload.

При декомпозиции сначала выделять стабильные границы:

- `*Block`/`*Page` остаётся container и orchestrator;
- `*List` получает items/loading/empty и эмитит list actions;
- `*Item`/`*Row` получает одну entity и эмитит действия;
- `*Form` владеет form model/errors/submit;
- `*Dialog` владеет open/close/focus lifecycle;
- повторяемые query/form mechanics выносить в `composables`, а не в mixins.

Для shared form-компонентов обязательный паттерн такой:

- основной SFC оставляет публичный контракт, layout и wiring;
- нормализация options/value/search state выносится в composables;
- dropdown/list/menu rendering выносится в child component, если иначе shared input растёт в монолит;
- keyboard navigation, active item и outside-click lifecycle не размазываются по template, а живут в composable.
- общие визуальные правила shared form primitives должны жить в `resources/sass/form.scss`;
- локальные `style scoped` в form primitives допустимы только для уникального поведения, а не для повторяемых базовых
  стилей;
- если стиль повторяется между primitives, его сначала выносят в Sass-слой, а не дублируют в компонентных `<style>`.

Для больших feature-block'ов обязательный паттерн такой:

- Vue container держит только composition root, template wiring и события страницы;
- navigation/history/breadcrumb state выносится в отдельный composable;
- file/item actions выносятся в отдельный composable, если они живут рядом с навигацией;
- если блок уже содержит списки/формы/side effects, он не должен превращаться в один большой `use*Block` hook.
- прямое обновление DOM, например breadcrumb HTML replacement, должно жить в отдельном adapter/composable, а не в
  navigation/state hook.
- если feature decomposed на несколько child components или composables, держи их в отдельном подкаталоге рядом с
  родительским блоком, а не рядом с несвязанными компонентами того же feature;
- для дочерних частей одна feature tree должна иметь свой локальный каталог, например `user-item/*`, `folder/*`,
  `search-select/*`, чтобы распил оставался сгруппированным и не расползался по соседним папкам.
- для тяжёлых admin CRUD-блоков базовый распил такой: `*Block` остаётся orchestrator, `*List` отвечает за список и list
  actions, `*Editor`/`*Form` отвечает за редактирование одной сущности, а сложные permissions/matrix/panel механики
  выносятся в отдельные child components внутри локального feature каталога;

Существующие большие компоненты фиксируются в `scripts/frontend-architecture-allowlist.txt`. Если allowlist падает из-за
роста строки/нового нарушения, правильное действие - декомпозировать или мигрировать компонент, а не расширять baseline
без причины.
`scripts/frontend-architecture-allowlist.txt` не является архивом старого debt:
- если компонент исправлен, он удаляется из allowlist в том же проходе;
- если allowlist всё ещё содержит компонент, у него должна быть одна актуальная причина;
- перед новым frontend-рефакторингом сначала синхронизируй allowlist с текущим кодом.
Перед каждым новым frontend-рефакторингом сначала сверяй и обновляй чек-лист остатка в
`FRONTEND_STANDARDIZATION_PLAN.md`; без этого новый проход не считается начатым.
Нельзя ухудшать читаемость ради прохождения лимита строк: не удалять осмысленные пустые строки и визуальные разделители
только чтобы уложиться в threshold. Если компонент упёрся в лимит, правильное действие - декомпозиция, а не сжатие
форматирования.

### API layer

Для нового кода HTTP-вызовы оформлять как функции в API/service module, а не прямо в компонентах.

Правила:

- компонент вызывает `api`/`service` функцию;
- API function отвечает за route helper, axios, payload shape;
- component отвечает за UI state и обработку результата;
- ошибки нормализуются в одном месте, чтобы формы показывали одинаковые сообщения.

`resources/js/routes-functions.js` и `resources/js/routes.json` считать generated/route helper слоем. Не вносить ручные
бизнес-правки в generated routes.
- Для backend controllers, которые обслуживают frontend screens, держать явную навигационную подсказку в комментарии
  над action-методом:
  - `// vue: resources/js/components/...`
  - `// blade: resources/views/...`
  - если endpoint связан сразу с несколькими child-компонентами, перечислять main shell и local feature children.

### CSRF handling

- Если backend отвечает `419` на axios-запрос, frontend должен автоматически обновить CSRF cookie через
  `/sanctum/csrf-cookie` и повторить запрос один раз.
- Повторный retry после неудачного refresh не выполнять: если refresh не помог, ошибку надо вернуть наверх.
- CSRF retry должен жить в одном runtime adapter/interceptor слое, а не в отдельных компонентах или API функциях.
- Для axios runtime в проекте `withCredentials` должен быть включён, чтобы `XSRF-TOKEN` cookie корректно обновлялся.

### Generated frontend contracts

Backend может синхронизировать данные для frontend, чтобы не дублировать route names, enums и request keys вручную.

Текущие генераторы:

- `front:export-route-list-command` пишет `resources/js/routes.json`;
- `front:export-route-functions-list-command` пишет `resources/js/api/index.js`;
- `front:export-enum` пишет `resources/js/utils/enum.js`;
- `front:export-request-arguments-command` должен писать `resources/js/utils/request-arguments.js`, но сейчас команда
  фактически отключена ранним `return`.

Правила для generated файлов:

- generated файл должен начинаться с явного комментария `AUTO-GENERATED` / `Не редактировать вручную`;
- generated файл не должен содержать дату генерации, если это создаёт шум в diff;
- ручные правки в generated файлах запрещены;
- новый frontend код должен импортировать API из `@api`, а не из legacy `routes-functions.js`;
- shared/cross-cutting imports are mandatory through project aliases:
    - `@common` for shared UI primitives, wrappers and common shared widgets;
    - `@api` for generated API functions;
    - `@composables` for reusable composables;
    - `@utils` for utilities/helpers;
    - `@form` for shared form primitives;
    - `@components` for shared domain UI blocks that are intentionally cross-cutting.
- access modifiers must be expressed as named `can*` booleans derived from `usePermissions().has(section, action)`;
- do not use generic `actions.edit/view/drop` as a permission gate when the intent is access control;
- `PermissionEnum` section/action keys are canonical for frontend access checks;
- if the imported component lives lower in the same feature tree as the parent, import it with a relative path instead
  of an alias;
- do not use deep relative paths like `../../../...` for shared/cross-cutting modules if the same module is available
  through an alias;
- do not mix alias and relative paths for the same import category inside one file: shared layers must stay on aliases,
  same-tree feature children must stay relative;
- keep alias usage stable and predictable:
    - shared UI via `@common`;
    - forms via `@form`;
    - composables via `@composables`;
    - utilities via `@utils`;
    - API via `@api`;
    - shared domain widgets via `@components`;
- `routes-functions.js` считать legacy generated layer и постепенно выводить из использования;
- `routes.json` использовать только для metadata/navigation, не как основной HTTP client;
- PHP генератор должен экранировать JS/JSON значения через безопасные serializers, а не конкатенацией строк;
- генерация должна быть идемпотентной: повторный запуск без backend изменений не меняет diff;
- генераторы должны запускаться перед frontend build, если менялись routes/enums/request contracts;
- канонический build command: `make yarn-build`, потому что он запускает генераторы и затем `yarn run build` внутри
  Sail.

Правила naming для generated API:

- function names: `Api{RouteNameInPascalCase}`;
- route args идут первыми и обязательны;
- `getParams = {}` перед `postData = null`;
- upload payload проходит через `prepareRequestData`;
- компонентам запрещено собирать URI вручную, если route есть в generated API.

### Store

Vuex использовать только для состояния, которое реально разделяется между страницами или несколькими независимыми
ветками компонентов.

Предпочтения:

- локальный `ref/reactive` для состояния одного компонента;
- props/events для parent-child связи;
- composable для повторяемой frontend логики;
- Vuex module для auth/permissions/global dictionaries/cross-page cache.

Не класть в Vuex:

- состояние формы, если оно живёт в одном компоненте;
- временные loading флаги одного запроса;
- копии backend entities без необходимости.

## 3. Vue 3 standards

Для нового Vue-кода использовать SFC и Composition API.

Рекомендуемый стиль:

- `<script setup>` для новых компонентов;
- `defineProps`, `defineEmits`;
- `computed` вместо ручной синхронизации derived state;
- `watch` только для side effects, не для обычных вычислений;
- `onMounted` только для загрузки/инициализации, которую нельзя сделать декларативно;
- `provide/inject` только для локального component tree context, не как service locator.

Options API можно сохранять в существующих компонентах. Не переписывать старый компонент только ради смены стиля, если
задача не требует этого.

## 4. Naming and structure

Компоненты:

- file name: `PascalCase.vue`;
- component name: `PascalCase`;
- Blade/global tag: `kebab-case`;
- event name: `kebab-case`;
- props: camelCase в JS, kebab-case в templates.

Новые директории выбирать по feature/domain, а не по техническому типу:

- хорошо: `components/admin/invoices/*`;
- хуже: `components/forms/*` для всего проекта без domain context.

Повторяемые UI primitives можно держать в common/shared зоне, но только если они действительно переиспользуются.

При переносе компонента, создании proxy/wrapper-обёртки или compatibility shim обязательно:

- сначала проверить `defineProps`/`props` и `defineEmits`/`emits` у целевого компонента;
- явно объявить и прокинуть все обязательные props в обёртке;
- не полагаться на `v-bind="$attrs"` как на единственный механизм сохранения контракта;
- не оставлять фиктивные заглушки вроде пустых строк для required props;
- перед завершением проверить все места использования, где required props могли потеряться после миграции.

## 5. Data flow

Направление данных:

- backend отдаёт primitives/resources;
- entrypoint передаёт начальные данные в root component;
- component загружает данные через API service;
- child получает данные через props;
- child сообщает об изменениях через emits;
- сохранение проходит через API service и backend command.

Для списков обязательно предусматривать:

- loading state;
- empty state;
- error state;
- pagination/sort/filter state;
- защиту от двойной отправки destructive actions.

## 6. Forms

Формы должны быть предсказуемыми:

- отдельная model object/ref для form state;
- отдельный errors object;
- единая обработка validation errors с backend;
- submit button блокируется во время отправки;
- optimistic UI использовать только там, где откат очевиден;
- destructive actions требуют confirmation UI.

Frontend validation допускается только как UX-подсказка. Источник истины по бизнес-валидации остаётся backend
command/validator.

## 7. Permissions and access

`window.userPermissions` допускается только как bootstrap input в entrypoint.

Новый код должен читать permissions через store/composable, а не напрямую из `window`.

UI может скрывать недоступные действия, но backend permission check остаётся обязательным.

## 8. Bootstrap, jQuery and DOM

Bootstrap JS и legacy jQuery допустимы только вне Vue-controlled DOM или в adapter/composable.

Запрещено в новом Vue-коде:

- искать элементы через `$()` внутри компонента;
- вручную менять DOM, которым управляет Vue;
- создавать Bootstrap instances без cleanup при unmount.

Для Bootstrap widgets в Vue:

- использовать declarative wrapper component или composable;
- делать cleanup в `onBeforeUnmount`;
- не полагаться на глобальный `DOMContentLoaded` для динамически созданной Vue-разметки.

## 9. Formatting helpers

Не добавлять новые helpers через `app.config.globalProperties`.

Новые форматтеры:

- выносить в pure functions/composables;
- покрывать простыми тестами при сложной логике;
- не смешивать форматирование денег/дат с компонентной разметкой.

Существующие `$formatMoney`, `$formatDate`, `$formatDateTime` можно оставить как legacy compatibility layer.

## 10. Error handling

Для HTTP ошибок нужен единый frontend convention:

- `422` показывает validation errors у формы;
- `403` показывает access denied message или redirect по текущему UX;
- `404` показывает not found state;
- `5xx` показывает общий recoverable error.

Компоненты не должны разбирать разные backend error shapes каждый по-своему.

## 11. Performance

Новый код должен:

- не грузить heavy editors/charts там, где они не нужны;
- использовать dynamic import для тяжёлых page-only компонентов;
- избегать deep watchers на больших коллекциях;
- использовать stable keys в `v-for`;
- не хранить большие response copies в нескольких местах.

Редакторы TinyMCE/TipTap/Quill и Chart.js подключать локально в компоненте/feature, если это возможно без большого
рефакторинга entrypoint.

## 12. Accessibility and UX baseline

Каждый interactive element должен быть доступен с клавиатуры.

Минимум:

- buttons для actions, links для navigation;
- если элемент ведёт на отдельную страницу, он должен быть `<a href>`, чтобы левый клик работал в текущем контексте, а ПКМ/СКМ сохраняли стандартное поведение браузера;
- label для input/select/textarea;
- понятный focus state;
- disabled/loading states не должны ломать layout;
- modal/dropdown lifecycle должен возвращать focus;
- icon-only buttons должны иметь `aria-label`.

## 13. Laravel integration

Blade отвечает за:

- layout;
- mount points;
- server-provided bootstrap data;
- initial page shell.

Blade не должен:

- собирать сложное Vue state дерево;
- дублировать frontend business logic;
- напрямую вмешиваться в Vue-controlled DOM после mount.

Vue не должен:

- знать имена backend classes;
- зависеть от Eloquent field constants;
- строить URLs вручную, если есть route helper слой.

При изменении Laravel routes/enums/request argument contracts backend-разработчик обязан обновить generated frontend
artifacts и проверить frontend build.

## 14. Migration rule

При работе с legacy frontend кодом приоритет такой:

1. не ухудшать существующие entrypoints;
2. вынести HTTP из компонента в API service;
3. убрать прямой доступ к `window` из компонента;
4. заменить jQuery DOM manipulation внутри Vue subtree;
5. выделить повторяемую логику в composable;
6. добавить loading/error/empty states;
7. только потом менять Options API на Composition API, если это снижает сложность.

Если изменяемый компонент уже больше 320 строк, feature-правка должна включать хотя бы одно уменьшение сложности: вынос
child component, API service, composable или form/list helper. Если это невозможно в текущей задаче, причина фиксируется
в плане миграции.

## 15. Frontend checks

Минимальный набор проверок, который стоит добавить:

- `make yarn-build`;
- `bash scripts/check-architecture.sh`;
- `make artisan front:export-route-list-command`;
- `make artisan front:export-route-functions-list-command`;
- `make artisan front:export-enum`;
- eslint для `resources/js`;
- prettier или единый formatter для Vue/JS;
- архитектурный grep guard на новые `window.axios` в `.vue`;
- архитектурный grep guard на новые `$(...)`/`jQuery` в `.vue`;
- architecture guard на ручное использование `routes-functions.js` в новом коде;
- architecture guard на Vue components больше 320 строк;
- architecture guard на новый Options API в компонентах;
- smoke build в CI вместе с `make architecture`.

Текущий `scripts/check-architecture.sh` уже проверяет frontend baseline:

- новые direct `window.axios`/`axios.*` в `resources/js/components`;
- новые legacy route helpers `Url.Routes`/`Url.Generator`/`routes-functions` в компонентах;
- jQuery внутри Vue components;
- новый `export default` в Vue components;
- Vue components больше 320 строк.

`scripts/frontend-architecture-allowlist.txt` не является разрешением на дальнейший рост legacy. Это список известных
нарушений, который надо сокращать при миграциях.

Перед завершением frontend-задач обязательно запускать `make yarn-build`, если Docker/Sail окружение доступно. Локальный
`npm run build` не использовать как замену.
