# План фича-тестов (Feature/Integration тесты)

> Базовый класс: `tests/Feature/FeatureTestCase.php` (RefreshDatabase + lc::reset)
> Трейт: `tests/Feature/WithAdminAccess.php` (admin-роль с базовыми правами)
> Стиль: HTTP-запросы через `$this->post/get/deleteJson()`, проверка `assertOk/assertStatus/assertDatabaseHas/assertDatabaseMissing`, тест 401/403/302 для неавторизованных

---

### Этап F1 — Auth ✅ (существующий LoginTest)
- [x] `test_login_with_valid_credentials`
- [x] `test_login_with_invalid_password_returns_error`
- [x] `test_access_home_requires_authentication`
- [x] `test_authenticated_user_can_access_home`
- [x] `test_logout`
- [ ] `test_login_with_token` — POST `/login/{token}`
- [ ] `test_password_set_page` — GET `/password/set`
- [ ] `test_password_set_save` — POST `/password/set`

### Этап F2 — Admin / Period
- [x] `test_list_periods`
- [x] `test_create_period`
- [x] `test_delete_period`
- [x] `test_unauthorized_access_returns_redirect`

### Этап F3 — Admin / Service
- [ ] `test_list_services`
- [ ] `test_create_service`
- [ ] `test_delete_service`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F4 — Admin / Account
- [ ] `test_list_accounts`
- [ ] `test_create_account`
- [ ] `test_get_account`
- [ ] `test_view_account_page`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F5 — Admin / Billing / Claim
- [ ] `test_create_claim`
- [ ] `test_list_claims`
- [ ] `test_delete_claim`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F6 — Admin / Billing / Payment
- [ ] `test_create_payment`
- [ ] `test_list_payments`
- [ ] `test_delete_payment`
- [ ] `test_auto_create_payment`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F7 — Admin / Billing / Invoice (расширение)
- [ ] `test_create_regular_invoices`
- [ ] `test_recalc_invoice`
- [ ] `test_export_invoices`
- [ ] `test_import_payments_parse_file`
- [ ] `test_import_payments_save`

### Этап F8 — Admin / Counter + CounterHistory
- [ ] `test_save_counter`
- [ ] `test_delete_counter`
- [ ] `test_list_counter_history`
- [ ] `test_create_claim_from_history`
- [ ] `test_link_counter_history`
- [ ] `test_confirm_counter_history`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F9 — Admin / Users
- [ ] `test_list_users`
- [ ] `test_create_user`
- [ ] `test_delete_user`
- [ ] `test_restore_user`
- [ ] `test_generate_email`
- [ ] `test_send_restore_password`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F10 — Admin / Roles
- [ ] `test_list_roles`
- [ ] `test_create_role`
- [ ] `test_delete_role`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F11 — Admin / Options
- [ ] `test_list_options`
- [ ] `test_save_option`
- [ ] `test_unauthorized_access_returns_redirect`

### Этап F12 — Admin / HelpDesk Settings (расширение TicketCrudTest)
- [ ] `test_category_crud`
- [ ] `test_service_crud`

### Этап F13 — Profile (ЛК)
- [ ] `test_profile_home_page`
- [ ] `test_profile_counters_list`
- [ ] `test_profile_counter_create`
- [ ] `test_profile_invoices_page`

### Этап F14 — Public / News
- [ ] `test_news_list`
- [ ] `test_news_show`
- [ ] `test_announcements_list`

### Этап F15 — Public / Files & Folders
- [ ] `test_folders_list`
- [ ] `test_files_list`

### Этап F16 — Public / Counter + Payment requests
- [ ] `test_create_counter_request`
- [ ] `test_create_payment_request`

### Этап F17 — Public / Pages (статичные)
- [ ] `test_contacts_page`
- [ ] `test_privacy_page`
- [ ] `test_terms_page`
- [ ] `test_search_page`
- [ ] `test_cookie_policy_page`

### Этап F18 — Public / Acquiring webhooks
- [ ] `test_webhook_submit`
- [ ] `test_webhook_failed`