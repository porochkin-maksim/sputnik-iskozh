# Legal Pages & Consent Plan

## 1. Inventory Current State

- [x] Проверить текущие страницы: `privacy`, `contacts`, public/profile/admin формы.
- [x] Зафиксировать, какие персональные данные собираются в каждой форме.

## 2. Define Mandatory Baseline

- [x] Утвердить обязательные документы:
  - [x] Политика обработки ПДн.
  - [x] Согласие на обработку ПДн.
  - [x] Сведения об операторе.
  - [x] Условия использования сайта/ЛК.
  - [x] Условия по оплатам/возвратам.
  - [x] Cookie-политика (если применимо).

## 3. Route & View Structure

- [x] Добавить/проверить маршруты:
  - [x] `/privacy`
  - [x] `/terms`
  - [x] `/personal-data-consent`
  - [x] `/payments-info`
  - [x] `/cookie`
- [x] Утвердить, где Blade и где Vue.

## 4. Global Legal Links

- [x] Вынести единый legal-links partial.
- [x] Подключить в layout (footer/nav) на всех страницах.
- [x] Гарантировать доступ к документам не глубже 1–2 кликов.

## 5. Forms & Consent UX

- [x] Добавить/проверить чекбокс согласия на всех формах сбора ПДн.
- [x] Добавить ссылки на `privacy`/`consent` рядом с submit.
- [x] Блокировать отправку без согласия, где требуется.

## 6. Backend Validation & Audit

- [x] Добавить серверную валидацию consent-полей.
- [ ] Добавить (при необходимости) хранение факта согласия:
  - [ ] timestamp
  - [ ] форма/контекст
  - [ ] версия текста согласия
- [x] Проверить API-контракты: без consent запрос не проходит.
- [x] Граница слоёв: в `FormRequest` для public consent проверяется только `consent`; доменные поля валидируются в use case.

## 7. Content Quality

- [x] Обновить текст Политики ПДн под реальные процессы обработки.
- [x] Обновить Terms для сценариев сайта и ЛК.
- [x] Описать оплату/возвраты в `payments-info`.
- [x] Описать cookie/аналитику в `cookie`.

## 8. QA & Release Gate

- [x] Проверить ссылки, маршруты, breadcrumbs.
- [ ] Проверить отображение на desktop/mobile.
- [x] Прогнать `make architecture`.
- [x] Прогнать `make prod`.

## 9. Process Rule

- [x] Зафиксировать правило: новая форма = consent + ссылка на policy + backend validation.
- [x] Внести этот пункт в release checklist.

## 10. Execution Order

- [x] Этап A: маршруты + шаблоны страниц.
- [x] Этап B: layout/partials + интеграция в формы.
- [x] Этап C: backend validation + audit trail.
- [x] Этап D: финальные тексты + QA + `make prod`.
