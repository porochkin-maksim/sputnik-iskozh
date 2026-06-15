# План рефакторинга: SetPasswordController и связанный бекенд

## Проблемы

### P1. `getEmail(null)` при просроченном токене в `set()`
**Файл:** `app/Http/Controllers/Auth/SetPasswordController.php:48-50`
**Описание:** `getEmail()` возвращает `null` для просроченного/невалидного токена. `set()` передаёт `null` в `UserService::getByEmail(null)` — зависит от реализации, может упасть или вернуть не того пользователя.
**Решение:** Добавить проверку `if ( ! $email) return $this->sendResetFailedResponse(...)` в `set()`.

### P2. Лишний SQL-запрос при `Auth::login()`
**Файл:** `app/Http/Controllers/Auth/SetPasswordController.php:64`
**Описание:** `Auth::login(User::findOrFail($user->getId()))` делает второй запрос к БД. Уже есть `$user->getId()` из `UserEntity`.
**Решение:** `Auth::loginUsingId($user->getId())`.

### P3. Нет сообщения при `$result === false`
**Файл:** `app/Http/Controllers/Auth/SetPasswordController.php:67-71`
**Описание:** Если `SetPasswordByTokenCommand::execute()` вернул `false`, пользователь редиректится на HOME без сообщения об ошибке.
**Решение:** Для JSON — вернуть ошибку 422 с сообщением; для HTML — `return redirect()->back()->withErrors(...)`.

### P4. Неиспользуемый трейт `ResetsPasswords`
**Файл:** `app/Http/Controllers/Auth/SetPasswordController.php:19`
**Описание:** `use ResetsPasswords` подмешивает методы (`reset`, `showResetForm`, etc.), которые не используются. Маршруты `/password/reset` разруливаются отдельным `ResetPasswordController`.
**Решение:** Удалить `use ResetsPasswords`.

### P5. Нет HTTP/feature тестов для SetPasswordController
**Файл:** `tests/` — нет файлов, тестирующих маршруты `GET|POST /password/set`
**Описание:** Контроллер полностью не покрыт.
**Решение:** Написать тесты для:
- GET валидный/просроченный/невалидный токен
- POST успешная установка
- POST просроченный токен
- POST несовпадающие пароли
- POST слабый пароль
- POST JSON-ответ

### P6. Дублирование политики пароля на фронте и беке
**Описание:** `PasswordPolicyValidator` и `usePasswordValidation.js` содержат одинаковые правила (≥8, буквы+цифры).
**Решение:** (низкий приоритет) Можно вынести enum с правилами в общее место, но это отдельная крупная задача.

## Выполнение

- [x] P1 — проверка email в `set()`
- [x] P2 — `Auth::loginUsingId`
- [x] P3 — сообщение об ошибке при ошибках команды/валидации
- [x] P4 — удалить `use ResetsPasswords`
- [x] P5 — feature-тесты для SetPasswordController
- [x] P6 — вынести общие правила (низкий приоритет)