import { makeQuery, prepareRequestData } from './helpers.js';
import apiClient from './client.js';

export function ApiAcquringInvoiceCreate(invoiceId,amount, getParams = {}, postData = null) {
    // see acquring.invoice.create
    // controller: App\Http\Controllers\Public\Requests\AcquringController@create
    return apiClient.post(makeQuery('/home/acquring/create/'+invoiceId+'/'+amount+'', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountCreate(getParams = {}, postData = null) {
    // see admin.account.create
    // controller: App\Http\Controllers\Admin\Account\AccountsController@create
    return apiClient.get(makeQuery('/admin/accounts/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountDelete(id, getParams = {}, postData = null) {
    // see admin.account.delete
    // controller: App\Http\Controllers\Admin\Account\AccountsController@delete
    return apiClient.delete(makeQuery('/admin/accounts/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountExport(getParams = {}, postData = null) {
    // see admin.account.export
    // controller: App\Http\Controllers\Admin\Account\AccountsController@export
    return apiClient.get(makeQuery('/admin/accounts/export', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountGet(accountId, getParams = {}, postData = null) {
    // see admin.account.get
    // controller: App\Http\Controllers\Admin\Account\AccountsController@get
    return apiClient.get(makeQuery('/admin/accounts/json/view/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountImportIndex(getParams = {}, postData = null) {
    // see admin.account.import.index
    // controller: App\Http\Controllers\Admin\Account\AccountsImportController@index
    return apiClient.get(makeQuery('/admin/accounts/import', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountImportParseFile(getParams = {}, postData = null) {
    // see admin.account.import.parse-file
    // controller: App\Http\Controllers\Admin\Account\AccountsImportController@parseFile
    return apiClient.post(makeQuery('/admin/accounts/import/parse-file', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountImportSave(getParams = {}, postData = null) {
    // see admin.account.import.save
    // controller: App\Http\Controllers\Admin\Account\AccountsImportController@save
    return apiClient.post(makeQuery('/admin/accounts/import/save', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountIndex(getParams = {}, postData = null) {
    // see admin.account.index
    // controller: App\Http\Controllers\Admin\Account\AccountsController@index
    return apiClient.get(makeQuery('/admin/accounts', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountInvoiceList(accountId, getParams = {}, postData = null) {
    // see admin.account.invoice.list
    // controller: App\Http\Controllers\Admin\Account\InvoiceController@list
    return apiClient.get(makeQuery('/admin/accounts/view/'+accountId+'/invoices/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountList(getParams = {}, postData = null) {
    // see admin.account.list
    // controller: App\Http\Controllers\Admin\Account\AccountsController@list
    return apiClient.get(makeQuery('/admin/accounts/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountSave(getParams = {}, postData = null) {
    // see admin.account.save
    // controller: App\Http\Controllers\Admin\Account\AccountsController@save
    return apiClient.post(makeQuery('/admin/accounts/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountView(accountId, getParams = {}, postData = null) {
    // see admin.account.view
    // controller: App\Http\Controllers\Admin\Account\AccountsController@view
    return apiClient.get(makeQuery('/admin/accounts/view/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.create
    // controller: App\Http\Controllers\Admin\Billing\ClaimController@create
    return apiClient.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/create', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimDelete(invoiceId,id, getParams = {}, postData = null) {
    // see admin.claim.delete
    // controller: App\Http\Controllers\Admin\Billing\ClaimController@delete
    return apiClient.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimList(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.list
    // controller: App\Http\Controllers\Admin\Billing\ClaimController@list
    return apiClient.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/list', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimResetPaid(invoiceId,id, getParams = {}, postData = null) {
    // see admin.claim.reset-paid
    // controller: App\Http\Controllers\Admin\Billing\ClaimController@resetPaid
    return apiClient.post(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/reset-paid/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimSave(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.save
    // controller: App\Http\Controllers\Admin\Billing\ClaimController@save
    return apiClient.post(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/save', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimView(invoiceId,claimId, getParams = {}, postData = null) {
    // see admin.claim.view
    // controller: App\Http\Controllers\Admin\Billing\ClaimController@get
    return apiClient.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/get/'+claimId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterAddValue(getParams = {}, postData = null) {
    // see admin.counter.add-value
    // controller: App\Http\Controllers\Admin\Account\CounterController@addValue
    return apiClient.post(makeQuery('/admin/accounts/counters/json/add-value', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterDelete(counterId, getParams = {}, postData = null) {
    // see admin.counter.delete
    // controller: App\Http\Controllers\Admin\Account\CounterController@delete
    return apiClient.post(makeQuery('/admin/accounts/counters/json/delete/'+counterId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterHistoryList(counterId, getParams = {}, postData = null) {
    // see admin.counter.history.list
    // controller: App\Http\Controllers\Admin\Account\CounterHistoryController@list
    return apiClient.get(makeQuery('/admin/counters/json/'+counterId+'/history/list', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterList(accountId, getParams = {}, postData = null) {
    // see admin.counter.list
    // controller: App\Http\Controllers\Admin\Account\CounterController@list
    return apiClient.get(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterRewatch(counterId, getParams = {}, postData = null) {
    // see admin.counter.rewatch
    // controller: App\Http\Controllers\Admin\Account\CounterController@rewatch
    return apiClient.post(makeQuery('/admin/accounts/counters/json/rewatch/'+counterId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterSave(getParams = {}, postData = null) {
    // see admin.counter.save
    // controller: App\Http\Controllers\Admin\Account\CounterController@save
    return apiClient.post(makeQuery('/admin/accounts/counters/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterView(counterId, getParams = {}, postData = null) {
    // see admin.counter.view
    // controller: App\Http\Controllers\Admin\Account\CounterController@view
    return apiClient.get(makeQuery('/admin/accounts/counters/view/'+counterId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminDashboardCards(getParams = {}, postData = null) {
    // see admin.dashboard.cards
    // controller: App\Http\Controllers\Admin\AdminDashboardController@cards
    return apiClient.get(makeQuery('/admin/cards', getParams), prepareRequestData(postData));
}

export function ApiAdminDocumentReceiptInvoice(id, getParams = {}, postData = null) {
    // see admin.document.receipt.invoice
    // controller: App\Http\Controllers\Common\Documents\ReceiptController@makeByInvoiceId
    return apiClient.get(makeQuery('/admin/invoices/view/'+id+'/invoice-receipt', getParams), prepareRequestData(postData));
}

export function ApiAdminEmailsDestroy(email, getParams = {}, postData = null) {
    // see admin.emails.destroy
    // controller: App\Http\Controllers\Admin\System\SentEmailController@destroy
    return apiClient.delete(makeQuery('/admin/emails/'+email+'', getParams), prepareRequestData(postData));
}

export function ApiAdminEmailsIndex(getParams = {}, postData = null) {
    // see admin.emails.index
    // controller: App\Http\Controllers\Admin\System\SentEmailController@index
    return apiClient.get(makeQuery('/admin/emails', getParams), prepareRequestData(postData));
}

export function ApiAdminEmailsShow(email, getParams = {}, postData = null) {
    // see admin.emails.show
    // controller: App\Http\Controllers\Admin\System\SentEmailController@show
    return apiClient.get(makeQuery('/admin/emails/'+email+'', getParams), prepareRequestData(postData));
}

export function ApiAdminErrorLogsDetails(filename,index, getParams = {}, postData = null) {
    // see admin.error-logs.details
    // controller: App\Http\Controllers\Admin\System\ErrorLogsController@details
    return apiClient.get(makeQuery('/admin/error-logs/'+filename+'/details/'+index+'', getParams), prepareRequestData(postData));
}

export function ApiAdminErrorLogsIndex(getParams = {}, postData = null) {
    // see admin.error-logs.index
    // controller: App\Http\Controllers\Admin\System\ErrorLogsController@index
    return apiClient.get(makeQuery('/admin/error-logs', getParams), prepareRequestData(postData));
}

export function ApiAdminErrorLogsShow(filename, getParams = {}, postData = null) {
    // see admin.error-logs.show
    // controller: App\Http\Controllers\Admin\System\ErrorLogsController@show
    return apiClient.get(makeQuery('/admin/error-logs/'+filename+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskIndex(getParams = {}, postData = null) {
    // see admin.help-desk.index
    // controller: App\Http\Controllers\Admin\HelpDesk\IndexPageController
    return apiClient.get(makeQuery('/admin/help-desk', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettings(getParams = {}, postData = null) {
    // see admin.help-desk.settings
    // controller: Closure
    return apiClient.get(makeQuery('/admin/help-desk/settings', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsCategoriesCreate(type, getParams = {}, postData = null) {
    // see admin.help-desk.settings.categories.create
    // controller: App\Http\Controllers\Admin\HelpDesk\CategoryController@create
    return apiClient.get(makeQuery('/admin/help-desk/settings/ajax/categories/create/'+type+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsCategoriesDelete(id, getParams = {}, postData = null) {
    // see admin.help-desk.settings.categories.delete
    // controller: App\Http\Controllers\Admin\HelpDesk\CategoryController@delete
    return apiClient.delete(makeQuery('/admin/help-desk/settings/ajax/categories/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsCategoriesGet(id, getParams = {}, postData = null) {
    // see admin.help-desk.settings.categories.get
    // controller: App\Http\Controllers\Admin\HelpDesk\CategoryController@get
    return apiClient.get(makeQuery('/admin/help-desk/settings/ajax/categories/get/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsCategoriesList(getParams = {}, postData = null) {
    // see admin.help-desk.settings.categories.list
    // controller: App\Http\Controllers\Admin\HelpDesk\CategoryController@list
    return apiClient.get(makeQuery('/admin/help-desk/settings/ajax/categories/list', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsCategoriesSave(getParams = {}, postData = null) {
    // see admin.help-desk.settings.categories.save
    // controller: App\Http\Controllers\Admin\HelpDesk\CategoryController@save
    return apiClient.post(makeQuery('/admin/help-desk/settings/ajax/categories/save', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsServicesCreate(categoryId, getParams = {}, postData = null) {
    // see admin.help-desk.settings.services.create
    // controller: App\Http\Controllers\Admin\HelpDesk\ServiceController@create
    return apiClient.get(makeQuery('/admin/help-desk/settings/ajax/services/create/'+categoryId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsServicesDelete(id, getParams = {}, postData = null) {
    // see admin.help-desk.settings.services.delete
    // controller: App\Http\Controllers\Admin\HelpDesk\ServiceController@delete
    return apiClient.delete(makeQuery('/admin/help-desk/settings/ajax/services/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsServicesList(categoryId, getParams = {}, postData = null) {
    // see admin.help-desk.settings.services.list
    // controller: App\Http\Controllers\Admin\HelpDesk\ServiceController@list
    return apiClient.get(makeQuery('/admin/help-desk/settings/ajax/services/'+categoryId+'/list', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsServicesSave(getParams = {}, postData = null) {
    // see admin.help-desk.settings.services.save
    // controller: App\Http\Controllers\Admin\HelpDesk\ServiceController@save
    return apiClient.post(makeQuery('/admin/help-desk/settings/ajax/services/save', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskSettingsTypesList(getParams = {}, postData = null) {
    // see admin.help-desk.settings.types.list
    // controller: App\Http\Controllers\Admin\HelpDesk\TicketTypeController@list
    return apiClient.get(makeQuery('/admin/help-desk/settings/ajax/types/list', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskTicketsDelete(id, getParams = {}, postData = null) {
    // see admin.help-desk.tickets.delete
    // controller: App\Http\Controllers\Admin\HelpDesk\TicketController@delete
    return apiClient.delete(makeQuery('/admin/help-desk/tickets/ajax/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskTicketsList(getParams = {}, postData = null) {
    // see admin.help-desk.tickets.list
    // controller: App\Http\Controllers\Admin\HelpDesk\TicketController@list
    return apiClient.get(makeQuery('/admin/help-desk/tickets/ajax/list', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskTicketsSave(getParams = {}, postData = null) {
    // see admin.help-desk.tickets.save
    // controller: App\Http\Controllers\Admin\HelpDesk\TicketController@save
    return apiClient.post(makeQuery('/admin/help-desk/tickets/ajax/save', getParams), prepareRequestData(postData));
}

export function ApiAdminHelpDeskTicketsView(id, getParams = {}, postData = null) {
    // see admin.help-desk.tickets.view
    // controller: App\Http\Controllers\Admin\HelpDesk\TicketController@view
    return apiClient.get(makeQuery('/admin/help-desk/tickets/view/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminIndex(getParams = {}, postData = null) {
    // see admin.index
    // controller: App\Http\Controllers\Admin\AdminDashboardController@index
    return apiClient.get(makeQuery('/admin', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceCreate(getParams = {}, postData = null) {
    // see admin.invoice.create
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@create
    return apiClient.get(makeQuery('/admin/invoices/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceCreateRegularInvoices(periodId, getParams = {}, postData = null) {
    // see admin.invoice.create-regular-invoices
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@createRegularInvoices
    return apiClient.post(makeQuery('/admin/invoices/json/create-regular-invoices/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceDelete(id, getParams = {}, postData = null) {
    // see admin.invoice.delete
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@delete
    return apiClient.delete(makeQuery('/admin/invoices/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceExport(getParams = {}, postData = null) {
    // see admin.invoice.export
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@export
    return apiClient.get(makeQuery('/admin/invoices/export', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceGet(id, getParams = {}, postData = null) {
    // see admin.invoice.get
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@get
    return apiClient.get(makeQuery('/admin/invoices/json/get/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceGetAccountsCountWithoutRegular(periodId, getParams = {}, postData = null) {
    // see admin.invoice.get-accounts-count-without-regular
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@getAccountCountWithoutRegular
    return apiClient.get(makeQuery('/admin/invoices/json/get-without-regular/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceImportPaymentsIndex(periodId, getParams = {}, postData = null) {
    // see admin.invoice.import-payments.index
    // controller: App\Http\Controllers\Admin\Billing\InvoiceImportController@index
    return apiClient.get(makeQuery('/admin/invoices/import-payments/period-'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceImportPaymentsParseFile(periodId, getParams = {}, postData = null) {
    // see admin.invoice.import-payments.parse-file
    // controller: App\Http\Controllers\Admin\Billing\InvoiceImportController@parseFile
    return apiClient.post(makeQuery('/admin/invoices/import-payments/period-'+periodId+'/parse-file', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceImportPaymentsSave(periodId, getParams = {}, postData = null) {
    // see admin.invoice.import-payments.save
    // controller: App\Http\Controllers\Admin\Billing\InvoiceImportController@save
    return apiClient.post(makeQuery('/admin/invoices/import-payments/period-'+periodId+'/save', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceIndex(getParams = {}, postData = null) {
    // see admin.invoice.index
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@index
    return apiClient.get(makeQuery('/admin/invoices', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceList(getParams = {}, postData = null) {
    // see admin.invoice.list
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@list
    return apiClient.get(makeQuery('/admin/invoices/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceRecalc(id, getParams = {}, postData = null) {
    // see admin.invoice.recalc
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@recalc
    return apiClient.post(makeQuery('/admin/invoices/json/recalc/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceRecalcPeriod(periodId, getParams = {}, postData = null) {
    // see admin.invoice.recalc-period
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@recalcPeriod
    return apiClient.post(makeQuery('/admin/invoices/json/recalc-period/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceResetPaymentsPeriod(periodId, getParams = {}, postData = null) {
    // see admin.invoice.reset-payments-period
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@resetPaymentsPeriod
    return apiClient.post(makeQuery('/admin/invoices/json/reset-payments-period/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceSave(getParams = {}, postData = null) {
    // see admin.invoice.save
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@save
    return apiClient.post(makeQuery('/admin/invoices/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceSyncServices(periodId, getParams = {}, postData = null) {
    // see admin.invoice.sync-services
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@syncServices
    return apiClient.post(makeQuery('/admin/invoices/json/sync-services/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceView(id, getParams = {}, postData = null) {
    // see admin.invoice.view
    // controller: App\Http\Controllers\Admin\Billing\InvoiceController@view
    return apiClient.get(makeQuery('/admin/invoices/view/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminLoginLink(userId,pin, getParams = {}, postData = null) {
    // see admin.login-link
    // controller: App\Http\Controllers\Admin\System\QrCodeController@makeLoginLink
    return apiClient.post(makeQuery('/admin/users/json/qr/login/'+userId+'/'+pin+'', getParams), prepareRequestData(postData));
}

export function ApiAdminLoginLinkSend(userId, getParams = {}, postData = null) {
    // see admin.login-link.send
    // controller: App\Http\Controllers\Admin\System\QrCodeController@makeLoginLinkAndSendEmail
    return apiClient.post(makeQuery('/admin/users/json/qr/send-login-link/'+userId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentDelete(id, getParams = {}, postData = null) {
    // see admin.new-payment.delete
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@delete
    return apiClient.delete(makeQuery('/admin/invoices/payments/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentGetInvoices(accountId,periodId, getParams = {}, postData = null) {
    // see admin.new-payment.get-invoices
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@getInvoices
    return apiClient.get(makeQuery('/admin/invoices/payments/json/get-invoices/'+accountId+'/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentIndex(getParams = {}, postData = null) {
    // see admin.new-payment.index
    // controller: App\Http\Controllers\Admin\Requests\NewPaymentController@index
    return apiClient.get(makeQuery('/admin/invoices/payments', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentList(getParams = {}, postData = null) {
    // see admin.new-payment.list
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@list
    return apiClient.get(makeQuery('/admin/invoices/payments/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentSave(getParams = {}, postData = null) {
    // see admin.new-payment.save
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@save
    return apiClient.post(makeQuery('/admin/invoices/payments/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentView(paymentId, getParams = {}, postData = null) {
    // see admin.new-payment.view
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@get
    return apiClient.get(makeQuery('/admin/invoices/payments/json/get/'+paymentId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminOptionsIndex(getParams = {}, postData = null) {
    // see admin.options.index
    // controller: App\Http\Controllers\Admin\System\OptionsController@index
    return apiClient.get(makeQuery('/admin/options', getParams), prepareRequestData(postData));
}

export function ApiAdminOptionsList(getParams = {}, postData = null) {
    // see admin.options.list
    // controller: App\Http\Controllers\Admin\System\OptionsController@list
    return apiClient.get(makeQuery('/admin/options/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminOptionsSave(getParams = {}, postData = null) {
    // see admin.options.save
    // controller: App\Http\Controllers\Admin\System\OptionsController@save
    return apiClient.post(makeQuery('/admin/options/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.create
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@create
    return apiClient.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/create', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentDelete(invoiceId,id, getParams = {}, postData = null) {
    // see admin.payment.delete
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@delete
    return apiClient.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentList(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.list
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@list
    return apiClient.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/list', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageAccountBalance(accountId, getParams = {}, postData = null) {
    // see admin.payment.manage.account-balance
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@accountBalance
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/account-balance/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageCanPayAll(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.manage.can-pay-all
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@canPayAll
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/can-pay-all/'+invoiceId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageCreate(getParams = {}, postData = null) {
    // see admin.payment.manage.create
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@create
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageDelete(id, getParams = {}, postData = null) {
    // see admin.payment.manage.delete
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@delete
    return apiClient.delete(makeQuery('/admin/invoices/payments/manage/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageGetInvoices(accountId,periodId, getParams = {}, postData = null) {
    // see admin.payment.manage.get-invoices
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@getInvoices
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/get-invoices/'+accountId+'/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageList(getParams = {}, postData = null) {
    // see admin.payment.manage.list
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@list
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManagePayAll(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.manage.pay-all
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@payAll
    return apiClient.post(makeQuery('/admin/invoices/payments/manage/json/pay-all/'+invoiceId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManagePayClaim(getParams = {}, postData = null) {
    // see admin.payment.manage.pay-claim
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@payClaim
    return apiClient.post(makeQuery('/admin/invoices/payments/manage/json/pay-claim', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageSave(getParams = {}, postData = null) {
    // see admin.payment.manage.save
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@save
    return apiClient.post(makeQuery('/admin/invoices/payments/manage/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageUnallocatedTransactions(accountId, getParams = {}, postData = null) {
    // see admin.payment.manage.unallocated-transactions
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@unallocatedTransactions
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/unallocated-transactions/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentManageView(paymentId, getParams = {}, postData = null) {
    // see admin.payment.manage.view
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@get
    return apiClient.get(makeQuery('/admin/invoices/payments/manage/json/get/'+paymentId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentSave(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.save
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@save
    return apiClient.post(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/save', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentView(invoiceId,paymentId, getParams = {}, postData = null) {
    // see admin.payment.view
    // controller: App\Http\Controllers\Admin\Billing\PaymentManageController@get
    return apiClient.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/get/'+paymentId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodClose(id, getParams = {}, postData = null) {
    // see admin.period.close
    // controller: App\Http\Controllers\Admin\Billing\PeriodController@close
    return apiClient.post(makeQuery('/admin/periods/json/'+id+'/close', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodCreate(getParams = {}, postData = null) {
    // see admin.period.create
    // controller: App\Http\Controllers\Admin\Billing\PeriodController@create
    return apiClient.get(makeQuery('/admin/periods/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodDelete(id, getParams = {}, postData = null) {
    // see admin.period.delete
    // controller: App\Http\Controllers\Admin\Billing\PeriodController@delete
    return apiClient.delete(makeQuery('/admin/periods/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodIndex(getParams = {}, postData = null) {
    // see admin.period.index
    // controller: App\Http\Controllers\Admin\Billing\PeriodController@index
    return apiClient.get(makeQuery('/admin/periods', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodList(getParams = {}, postData = null) {
    // see admin.period.list
    // controller: App\Http\Controllers\Admin\Billing\PeriodController@list
    return apiClient.get(makeQuery('/admin/periods/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodSave(getParams = {}, postData = null) {
    // see admin.period.save
    // controller: App\Http\Controllers\Admin\Billing\PeriodController@save
    return apiClient.post(makeQuery('/admin/periods/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminQrView(uid, getParams = {}, postData = null) {
    // see admin.qr.view
    // controller: App\Http\Controllers\Admin\System\QrCodeController@view
    return apiClient.get(makeQuery('/admin/qr/view/'+uid+'', getParams), prepareRequestData(postData));
}

export function ApiAdminQueue(getParams = {}, postData = null) {
    // see admin.queue
    // controller: App\Http\Controllers\Admin\QueueController@index
    return apiClient.get(makeQuery('/admin/queue', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueClear(getParams = {}, postData = null) {
    // see admin.queue.clear
    // controller: App\Http\Controllers\Admin\QueueController@clear
    return apiClient.post(makeQuery('/admin/queue/clear', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueStart(getParams = {}, postData = null) {
    // see admin.queue.start
    // controller: App\Http\Controllers\Admin\QueueController@start
    return apiClient.post(makeQuery('/admin/queue/start', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueStatus(getParams = {}, postData = null) {
    // see admin.queue.status
    // controller: App\Http\Controllers\Admin\QueueController@status
    return apiClient.get(makeQuery('/admin/queue/status', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueStop(getParams = {}, postData = null) {
    // see admin.queue.stop
    // controller: App\Http\Controllers\Admin\QueueController@stop
    return apiClient.post(makeQuery('/admin/queue/stop', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryConfirm(getParams = {}, postData = null) {
    // see admin.requests.counter-history.confirm
    // controller: App\Http\Controllers\Admin\Requests\CounterController@confirm
    return apiClient.post(makeQuery('/admin/counter-history/json/confirm', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryConfirmDelete(getParams = {}, postData = null) {
    // see admin.requests.counter-history.confirm-delete
    // controller: App\Http\Controllers\Admin\Requests\CounterController@confirmDelete
    return apiClient.post(makeQuery('/admin/counter-history/json/confirm-delete', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryCreateClaim(historyId, getParams = {}, postData = null) {
    // see admin.requests.counter-history.create-claim
    // controller: App\Http\Controllers\Admin\Account\CounterController@createClaim
    return apiClient.post(makeQuery('/admin/counter-history/json/create-claim/'+historyId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryDelete(historyId, getParams = {}, postData = null) {
    // see admin.requests.counter-history.delete
    // controller: App\Http\Controllers\Admin\Requests\CounterController@delete
    return apiClient.delete(makeQuery('/admin/counter-history/json/delete/'+historyId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryIndex(getParams = {}, postData = null) {
    // see admin.requests.counter-history.index
    // controller: App\Http\Controllers\Admin\Account\CounterController@index
    return apiClient.get(makeQuery('/admin/counter-history', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryLink(getParams = {}, postData = null) {
    // see admin.requests.counter-history.link
    // controller: App\Http\Controllers\Admin\Requests\CounterController@link
    return apiClient.post(makeQuery('/admin/counter-history/json/link', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryList(getParams = {}, postData = null) {
    // see admin.requests.counter-history.list
    // controller: App\Http\Controllers\Admin\Requests\CounterController@list
    return apiClient.get(makeQuery('/admin/counter-history/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleCreate(getParams = {}, postData = null) {
    // see admin.role.create
    // controller: App\Http\Controllers\Admin\System\RolesController@create
    return apiClient.get(makeQuery('/admin/roles/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleDelete(id, getParams = {}, postData = null) {
    // see admin.role.delete
    // controller: App\Http\Controllers\Admin\System\RolesController@delete
    return apiClient.delete(makeQuery('/admin/roles/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleIndex(getParams = {}, postData = null) {
    // see admin.role.index
    // controller: App\Http\Controllers\Admin\System\RolesController@index
    return apiClient.get(makeQuery('/admin/roles', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleList(getParams = {}, postData = null) {
    // see admin.role.list
    // controller: App\Http\Controllers\Admin\System\RolesController@list
    return apiClient.get(makeQuery('/admin/roles/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleSave(getParams = {}, postData = null) {
    // see admin.role.save
    // controller: App\Http\Controllers\Admin\System\RolesController@save
    return apiClient.post(makeQuery('/admin/roles/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsAccounts(getParams = {}, postData = null) {
    // see admin.selects.accounts
    // controller: App\Http\Controllers\Admin\SelectCollectionsController@accounts
    return apiClient.get(makeQuery('/admin/json/selects/accounts', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsCounters(accountId, getParams = {}, postData = null) {
    // see admin.selects.counters
    // controller: App\Http\Controllers\Admin\SelectCollectionsController@counters
    return apiClient.get(makeQuery('/admin/json/selects/counters/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsPeriods(getParams = {}, postData = null) {
    // see admin.selects.periods
    // controller: App\Http\Controllers\Admin\SelectCollectionsController@periods
    return apiClient.get(makeQuery('/admin/json/selects/periods', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsRoles(getParams = {}, postData = null) {
    // see admin.selects.roles
    // controller: App\Http\Controllers\Admin\SelectCollectionsController@roles
    return apiClient.get(makeQuery('/admin/json/selects/roles', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsServicesTypes(getParams = {}, postData = null) {
    // see admin.selects.services-types
    // controller: App\Http\Controllers\Admin\SelectCollectionsController@servicesTypes
    return apiClient.get(makeQuery('/admin/json/selects/services-types', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceCreate(getParams = {}, postData = null) {
    // see admin.service.create
    // controller: App\Http\Controllers\Admin\Billing\ServiceController@create
    return apiClient.get(makeQuery('/admin/services/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceDelete(id, getParams = {}, postData = null) {
    // see admin.service.delete
    // controller: App\Http\Controllers\Admin\Billing\ServiceController@delete
    return apiClient.delete(makeQuery('/admin/services/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceIndex(getParams = {}, postData = null) {
    // see admin.service.index
    // controller: App\Http\Controllers\Admin\Billing\ServiceController@index
    return apiClient.get(makeQuery('/admin/services', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceList(getParams = {}, postData = null) {
    // see admin.service.list
    // controller: App\Http\Controllers\Admin\Billing\ServiceController@list
    return apiClient.get(makeQuery('/admin/services/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceSave(getParams = {}, postData = null) {
    // see admin.service.save
    // controller: App\Http\Controllers\Admin\Billing\ServiceController@save
    return apiClient.post(makeQuery('/admin/services/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminTopPanelIndex(getParams = {}, postData = null) {
    // see admin.top-panel.index
    // controller: App\Http\Controllers\Admin\TopPanelController@index
    return apiClient.get(makeQuery('/admin/json/top-panel', getParams), prepareRequestData(postData));
}

export function ApiAdminTopPanelSearch(getParams = {}, postData = null) {
    // see admin.top-panel.search
    // controller: App\Http\Controllers\Admin\TopPanelController@search
    return apiClient.post(makeQuery('/admin/json/top-panel', getParams), prepareRequestData(postData));
}

export function ApiAdminUserDelete(id, getParams = {}, postData = null) {
    // see admin.user.delete
    // controller: App\Http\Controllers\Admin\System\UsersController@delete
    return apiClient.delete(makeQuery('/admin/users/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminUserExport(getParams = {}, postData = null) {
    // see admin.user.export
    // controller: App\Http\Controllers\Admin\System\UsersController@export
    return apiClient.get(makeQuery('/admin/users/export', getParams), prepareRequestData(postData));
}

export function ApiAdminUserGenerateEmail(getParams = {}, postData = null) {
    // see admin.user.generate-email
    // controller: App\Http\Controllers\Admin\System\UsersController@generateEmail
    return apiClient.post(makeQuery('/admin/users/json/generate-email', getParams), prepareRequestData(postData));
}

export function ApiAdminUserGet(id, getParams = {}, postData = null) {
    // see admin.user.get
    // controller: App\Http\Controllers\Admin\System\UsersController@get
    return apiClient.get(makeQuery('/admin/users/json/get/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminUserImportIndex(getParams = {}, postData = null) {
    // see admin.user.import.index
    // controller: App\Http\Controllers\Admin\System\UsersImportController@index
    return apiClient.get(makeQuery('/admin/users/import', getParams), prepareRequestData(postData));
}

export function ApiAdminUserImportParseFile(getParams = {}, postData = null) {
    // see admin.user.import.parse-file
    // controller: App\Http\Controllers\Admin\System\UsersImportController@parseFile
    return apiClient.post(makeQuery('/admin/users/import/parse-file', getParams), prepareRequestData(postData));
}

export function ApiAdminUserImportSave(getParams = {}, postData = null) {
    // see admin.user.import.save
    // controller: App\Http\Controllers\Admin\System\UsersImportController@save
    return apiClient.post(makeQuery('/admin/users/import/save', getParams), prepareRequestData(postData));
}

export function ApiAdminUserIndex(getParams = {}, postData = null) {
    // see admin.user.index
    // controller: App\Http\Controllers\Admin\System\UsersController@index
    return apiClient.get(makeQuery('/admin/users', getParams), prepareRequestData(postData));
}

export function ApiAdminUserList(getParams = {}, postData = null) {
    // see admin.user.list
    // controller: App\Http\Controllers\Admin\System\UsersController@list
    return apiClient.get(makeQuery('/admin/users/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminUserRestore(id, getParams = {}, postData = null) {
    // see admin.user.restore
    // controller: App\Http\Controllers\Admin\System\UsersController@restore
    return apiClient.patch(makeQuery('/admin/users/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminUserSave(getParams = {}, postData = null) {
    // see admin.user.save
    // controller: App\Http\Controllers\Admin\System\UsersController@save
    return apiClient.post(makeQuery('/admin/users/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminUserSendInviteWithPassword(getParams = {}, postData = null) {
    // see admin.user.send.invite-with-password
    // controller: App\Http\Controllers\Admin\System\UsersController@sendInviteWithPassword
    return apiClient.post(makeQuery('/admin/users/json/send-invite-password', getParams), prepareRequestData(postData));
}

export function ApiAdminUserSendRestorePassword(getParams = {}, postData = null) {
    // see admin.user.send.restore.password
    // controller: App\Http\Controllers\Admin\System\UsersController@sendRestorePassword
    return apiClient.post(makeQuery('/admin/users/json/sendRestorePassword', getParams), prepareRequestData(postData));
}

export function ApiAdminUserView(id, getParams = {}, postData = null) {
    // see admin.user.view
    // controller: App\Http\Controllers\Admin\System\UsersController@view
    return apiClient.get(makeQuery('/admin/users/view/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAjaxSelectsAccounts(getParams = {}, postData = null) {
    // see ajax.selects.accounts
    // controller: App\Http\Controllers\Public\SelectsController@accounts
    return apiClient.get(makeQuery('/ajax/selects/accounts', getParams), prepareRequestData(postData));
}

export function ApiAjaxSelectsCounters(accountId, getParams = {}, postData = null) {
    // see ajax.selects.counters
    // controller: App\Http\Controllers\Public\SelectsController@counters
    return apiClient.get(makeQuery('/ajax/selects/counters/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAnnouncementsIndex(getParams = {}, postData = null) {
    // see announcements.index
    // controller: App\Http\Controllers\Public\News\AnnouncementController@index
    return apiClient.get(makeQuery('/announcements', getParams), prepareRequestData(postData));
}

export function ApiAnnouncementsList(getParams = {}, postData = null) {
    // see announcements.list
    // controller: App\Http\Controllers\Public\News\AnnouncementController@list
    return apiClient.get(makeQuery('/announcements/json/list', getParams), prepareRequestData(postData));
}

export function ApiAnnouncementsShow(id, getParams = {}, postData = null) {
    // see announcements.show
    // controller: App\Http\Controllers\Public\News\AnnouncementController@show
    return apiClient.get(makeQuery('/announcements/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiCommonSummary(getParams = {}, postData = null) {
    // see common.summary
    // controller: App\Http\Controllers\Common\SummaryController@summary
    return apiClient.get(makeQuery('/json/summary', getParams), prepareRequestData(postData));
}

export function ApiCommonSummaryDetailing(type, getParams = {}, postData = null) {
    // see common.summary.detailing
    // controller: App\Http\Controllers\Common\SummaryController@summaryDetailing
    return apiClient.get(makeQuery('/json/summary/'+type+'', getParams), prepareRequestData(postData));
}

export function ApiContacts(getParams = {}, postData = null) {
    // see contacts
    // controller: App\Http\Controllers\Public\PagesController@contacts
    return apiClient.get(makeQuery('/contacts', getParams), prepareRequestData(postData));
}

export function ApiCookiePolicy(getParams = {}, postData = null) {
    // see cookie-policy
    // controller: App\Http\Controllers\Public\PagesController@cookiePolicy
    return apiClient.get(makeQuery('/cookie', getParams), prepareRequestData(postData));
}

export function ApiCookieAgreement(getParams = {}, postData = null) {
    // see cookie_agreement
    // controller: App\Http\Controllers\CookieController@cookieAgreement
    return apiClient.post(makeQuery('/cookie-agreement', getParams), prepareRequestData(postData));
}

export function ApiCounter(getParams = {}, postData = null) {
    // see counter
    // controller: App\Http\Controllers\Public\RequestsPagesController@counter
    return apiClient.get(makeQuery('/contacts/requests/counter', getParams), prepareRequestData(postData));
}

export function ApiCounterCreate(getParams = {}, postData = null) {
    // see counter.create
    // controller: App\Http\Controllers\Public\Requests\CounterController@create
    return apiClient.post(makeQuery('/contacts/requests/counter', getParams), prepareRequestData(postData));
}

export function ApiDocumentReceiptBlank(getParams = {}, postData = null) {
    // see document.receipt.blank
    // controller: App\Http\Controllers\Common\Documents\ReceiptController@makeForBlank
    return apiClient.get(makeQuery('/document/invoice-receipt/blank', getParams), prepareRequestData(postData));
}

export function ApiDocumentReceiptInvoice(uid, getParams = {}, postData = null) {
    // see document.receipt.invoice
    // controller: App\Http\Controllers\Common\Documents\ReceiptController@makeForInvoice
    return apiClient.get(makeQuery('/document/invoice-receipt/'+uid+'', getParams), prepareRequestData(postData));
}

export function ApiFilesDelete(id, getParams = {}, postData = null) {
    // see files.delete
    // controller: App\Http\Controllers\Public\Files\FileController@delete
    return apiClient.delete(makeQuery('/files/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFilesDown(id, getParams = {}, postData = null) {
    // see files.down
    // controller: App\Http\Controllers\Public\Files\FileController@down
    return apiClient.post(makeQuery('/files/json/down/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFilesEdit(id, getParams = {}, postData = null) {
    // see files.edit
    // controller: App\Http\Controllers\Public\Files\FileController@edit
    return apiClient.get(makeQuery('/files/json/edit/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFilesIndex(folder, getParams = {}, postData = null) {
    // see files.index
    // controller: App\Http\Controllers\Public\Files\FolderController@index
    return apiClient.get(makeQuery('/files/'+folder+'', getParams), prepareRequestData(postData));
}

export function ApiFilesList(getParams = {}, postData = null) {
    // see files.list
    // controller: App\Http\Controllers\Public\Files\FileController@list
    return apiClient.get(makeQuery('/files/json/list', getParams), prepareRequestData(postData));
}

export function ApiFilesMove(getParams = {}, postData = null) {
    // see files.move
    // controller: App\Http\Controllers\Public\Files\FileController@move
    return apiClient.post(makeQuery('/files/json/move', getParams), prepareRequestData(postData));
}

export function ApiFilesReplace(getParams = {}, postData = null) {
    // see files.replace
    // controller: App\Http\Controllers\Public\Files\FileController@replace
    return apiClient.post(makeQuery('/files/json/replace', getParams), prepareRequestData(postData));
}

export function ApiFilesSave(getParams = {}, postData = null) {
    // see files.save
    // controller: App\Http\Controllers\Public\Files\FileController@save
    return apiClient.post(makeQuery('/files/json/save', getParams), prepareRequestData(postData));
}

export function ApiFilesStore(getParams = {}, postData = null) {
    // see files.store
    // controller: App\Http\Controllers\Public\Files\FileController@store
    return apiClient.post(makeQuery('/files/json/store', getParams), prepareRequestData(postData));
}

export function ApiFilesUp(id, getParams = {}, postData = null) {
    // see files.up
    // controller: App\Http\Controllers\Public\Files\FileController@up
    return apiClient.post(makeQuery('/files/json/up/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFoldersDelete(id, getParams = {}, postData = null) {
    // see folders.delete
    // controller: App\Http\Controllers\Public\Files\FolderController@delete
    return apiClient.delete(makeQuery('/folders/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFoldersInfo(id, getParams = {}, postData = null) {
    // see folders.info
    // controller: App\Http\Controllers\Public\Files\FolderController@info
    return apiClient.get(makeQuery('/folders/json/info/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFoldersList(getParams = {}, postData = null) {
    // see folders.list
    // controller: App\Http\Controllers\Public\Files\FolderController@list
    return apiClient.get(makeQuery('/folders/json/list', getParams), prepareRequestData(postData));
}

export function ApiFoldersSave(getParams = {}, postData = null) {
    // see folders.save
    // controller: App\Http\Controllers\Public\Files\FolderController@save
    return apiClient.post(makeQuery('/folders/json/save', getParams), prepareRequestData(postData));
}

export function ApiFoldersShow(id, getParams = {}, postData = null) {
    // see folders.show
    // controller: App\Http\Controllers\Public\Files\FolderController@show
    return apiClient.get(makeQuery('/folders/json/show/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiGarbage(getParams = {}, postData = null) {
    // see garbage
    // controller: App\Http\Controllers\Public\PagesController@garbage
    return apiClient.get(makeQuery('/garbage', getParams), prepareRequestData(postData));
}

export function ApiHelpDesk(getParams = {}, postData = null) {
    // see help-desk
    // controller: App\Http\Controllers\Public\HelpDesk\HelpDeskController@index
    return apiClient.get(makeQuery('/contacts/requests/help-desk', getParams), prepareRequestData(postData));
}

export function ApiHelpDeskCategory(type,category, getParams = {}, postData = null) {
    // see help-desk.category
    // controller: App\Http\Controllers\Public\HelpDesk\HelpDeskController@category
    return apiClient.get(makeQuery('/contacts/requests/help-desk/'+type+'/'+category+'', getParams), prepareRequestData(postData));
}

export function ApiHelpDeskService(type,category,service, getParams = {}, postData = null) {
    // see help-desk.service
    // controller: App\Http\Controllers\Public\HelpDesk\HelpDeskController@form
    return apiClient.get(makeQuery('/contacts/requests/help-desk/'+type+'/'+category+'/'+service+'', getParams), prepareRequestData(postData));
}

export function ApiHelpDeskTicket(type,category,service, getParams = {}, postData = null) {
    // see help-desk.ticket
    // controller: App\Http\Controllers\Public\HelpDesk\HelpDeskController@ticket
    return apiClient.post(makeQuery('/contacts/requests/help-desk/'+type+'/'+category+'/'+service+'', getParams), prepareRequestData(postData));
}

export function ApiHelpDeskType(type, getParams = {}, postData = null) {
    // see help-desk.type
    // controller: App\Http\Controllers\Public\HelpDesk\HelpDeskController@type
    return apiClient.get(makeQuery('/contacts/requests/help-desk/'+type+'', getParams), prepareRequestData(postData));
}

export function ApiHome(getParams = {}, postData = null) {
    // see home
    // controller: App\Http\Controllers\Profile\HomeController@index
    return apiClient.get(makeQuery('/home', getParams), prepareRequestData(postData));
}

export function ApiIndex(getParams = {}, postData = null) {
    // see index
    // controller: App\Http\Controllers\Public\PagesController@index
    return apiClient.get(makeQuery('/', getParams), prepareRequestData(postData));
}

export function ApiInfraHistoryChanges(getParams = {}, postData = null) {
    // see infra.history-changes
    // controller: App\Http\Controllers\Admin\HistoryChangesViewController
    return apiClient.get(makeQuery('/admin/history/changes', getParams), prepareRequestData(postData));
}

export function ApiLogin(getParams = {}, postData = null) {
    // see login
    // controller: App\Http\Controllers\Auth\LoginController@login
    return apiClient.post(makeQuery('/login', getParams), prepareRequestData(postData));
}

export function ApiLoginDo(token, getParams = {}, postData = null) {
    // see login.do
    // controller: App\Http\Controllers\Auth\LoginController@token
    return apiClient.post(makeQuery('/login/'+token+'', getParams), prepareRequestData(postData));
}

export function ApiLogout(getParams = {}, postData = null) {
    // see logout
    // controller: App\Http\Controllers\Auth\LogoutController
    return apiClient.get(makeQuery('/logout', getParams), prepareRequestData(postData));
}

export function ApiNewsCreate(getParams = {}, postData = null) {
    // see news.create
    // controller: App\Http\Controllers\Public\News\NewsController@create
    return apiClient.get(makeQuery('/news/json/create', getParams), prepareRequestData(postData));
}

export function ApiNewsDelete(id, getParams = {}, postData = null) {
    // see news.delete
    // controller: App\Http\Controllers\Public\News\NewsController@delete
    return apiClient.delete(makeQuery('/news/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsEdit(id, getParams = {}, postData = null) {
    // see news.edit
    // controller: App\Http\Controllers\Public\News\NewsController@edit
    return apiClient.get(makeQuery('/news/json/edit/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsFileDelete(id, getParams = {}, postData = null) {
    // see news.file.delete
    // controller: App\Http\Controllers\Public\News\NewsController@deleteFile
    return apiClient.delete(makeQuery('/news/json/file/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsFileSave(getParams = {}, postData = null) {
    // see news.file.save
    // controller: App\Http\Controllers\Public\News\NewsController@saveFile
    return apiClient.post(makeQuery('/news/json/file/save', getParams), prepareRequestData(postData));
}

export function ApiNewsFileUpload(id, getParams = {}, postData = null) {
    // see news.file.upload
    // controller: App\Http\Controllers\Public\News\NewsController@uploadFile
    return apiClient.post(makeQuery('/news/json/file/upload/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsForm(id, getParams = {}, postData = null) {
    // see news.form
    // controller: App\Http\Controllers\Public\News\NewsController@formPage
    return apiClient.get(makeQuery('/news/form/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsIndex(getParams = {}, postData = null) {
    // see news.index
    // controller: App\Http\Controllers\Public\News\NewsController@index
    return apiClient.get(makeQuery('/news', getParams), prepareRequestData(postData));
}

export function ApiNewsList(getParams = {}, postData = null) {
    // see news.list
    // controller: App\Http\Controllers\Public\News\NewsController@list
    return apiClient.get(makeQuery('/news/json/list', getParams), prepareRequestData(postData));
}

export function ApiNewsListIndex(getParams = {}, postData = null) {
    // see news.list.index
    // controller: App\Http\Controllers\Public\News\NewsController@indexList
    return apiClient.get(makeQuery('/news/json/list/index', getParams), prepareRequestData(postData));
}

export function ApiNewsListLocked(getParams = {}, postData = null) {
    // see news.list.locked
    // controller: App\Http\Controllers\Public\News\NewsController@lockedNews
    return apiClient.get(makeQuery('/news/json/list/locked', getParams), prepareRequestData(postData));
}

export function ApiNewsSave(getParams = {}, postData = null) {
    // see news.save
    // controller: App\Http\Controllers\Public\News\NewsController@save
    return apiClient.post(makeQuery('/news/json/save', getParams), prepareRequestData(postData));
}

export function ApiNewsShow(id, getParams = {}, postData = null) {
    // see news.show
    // controller: App\Http\Controllers\Public\News\NewsController@show
    return apiClient.get(makeQuery('/news/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiPasswordConfirm(getParams = {}, postData = null) {
    // see password.confirm
    // controller: App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm
    return apiClient.get(makeQuery('/password/confirm', getParams), prepareRequestData(postData));
}

export function ApiPasswordEmail(getParams = {}, postData = null) {
    // see password.email
    // controller: App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail
    return apiClient.post(makeQuery('/password/email', getParams), prepareRequestData(postData));
}

export function ApiPasswordRequest(getParams = {}, postData = null) {
    // see password.request
    // controller: App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm
    return apiClient.get(makeQuery('/password/reset', getParams), prepareRequestData(postData));
}

export function ApiPasswordReset(token, getParams = {}, postData = null) {
    // see password.reset
    // controller: App\Http\Controllers\Auth\ResetPasswordController@showResetForm
    return apiClient.get(makeQuery('/password/reset/'+token+'', getParams), prepareRequestData(postData));
}

export function ApiPasswordSave(getParams = {}, postData = null) {
    // see password.save
    // controller: App\Http\Controllers\Auth\SetPasswordController@set
    return apiClient.post(makeQuery('/password/set', getParams), prepareRequestData(postData));
}

export function ApiPasswordSet(getParams = {}, postData = null) {
    // see password.set
    // controller: App\Http\Controllers\Auth\SetPasswordController@index
    return apiClient.get(makeQuery('/password/set', getParams), prepareRequestData(postData));
}

export function ApiPasswordUpdate(getParams = {}, postData = null) {
    // see password.update
    // controller: App\Http\Controllers\Auth\ResetPasswordController@reset
    return apiClient.post(makeQuery('/password/reset', getParams), prepareRequestData(postData));
}

export function ApiPayment(getParams = {}, postData = null) {
    // see payment
    // controller: App\Http\Controllers\Public\RequestsPagesController@payment
    return apiClient.get(makeQuery('/contacts/requests/payment', getParams), prepareRequestData(postData));
}

export function ApiPaymentCreate(getParams = {}, postData = null) {
    // see payment.create
    // controller: App\Http\Controllers\Public\Requests\PaymentsController@create
    return apiClient.post(makeQuery('/contacts/requests/payment', getParams), prepareRequestData(postData));
}

export function ApiPaymentsInfo(getParams = {}, postData = null) {
    // see payments-info
    // controller: App\Http\Controllers\Public\PagesController@paymentsInfo
    return apiClient.get(makeQuery('/payments-info', getParams), prepareRequestData(postData));
}

export function ApiPersonalDataConsent(getParams = {}, postData = null) {
    // see personal-data-consent
    // controller: App\Http\Controllers\Public\PagesController@personalDataConsent
    return apiClient.get(makeQuery('/personal-data-consent', getParams), prepareRequestData(postData));
}

export function ApiPrivacy(getParams = {}, postData = null) {
    // see privacy
    // controller: App\Http\Controllers\Public\PagesController@privacy
    return apiClient.get(makeQuery('/privacy', getParams), prepareRequestData(postData));
}

export function ApiProfileAccountSwitch(getParams = {}, postData = null) {
    // see profile.account.switch
    // controller: App\Http\Controllers\Profile\ProfileController@switchAccount
    return apiClient.post(makeQuery('/home/profile/switch-account', getParams), prepareRequestData(postData));
}

export function ApiProfileAccountsSearch(getParams = {}, postData = null) {
    // see profile.accounts.search
    // controller: App\Http\Controllers\Profile\AccountsController@search
    return apiClient.get(makeQuery('/home/accounts/search', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterAddValue(getParams = {}, postData = null) {
    // see profile.counter.add-value
    // controller: App\Http\Controllers\Profile\CounterController@addValue
    return apiClient.post(makeQuery('/home/counters/json/add-value', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterCreate(getParams = {}, postData = null) {
    // see profile.counter.create
    // controller: App\Http\Controllers\Profile\CounterController@create
    return apiClient.post(makeQuery('/home/counters/json/create', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterHistoryList(getParams = {}, postData = null) {
    // see profile.counter.history-list
    // controller: App\Http\Controllers\Profile\CounterController@history
    return apiClient.post(makeQuery('/home/counters/json/history', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterList(getParams = {}, postData = null) {
    // see profile.counter.list
    // controller: App\Http\Controllers\Profile\CounterController@list
    return apiClient.get(makeQuery('/home/counters/json/list', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterPassport(getParams = {}, postData = null) {
    // see profile.counter.passport
    // controller: App\Http\Controllers\Profile\CounterController@passportSave
    return apiClient.post(makeQuery('/home/counters/json/passport', getParams), prepareRequestData(postData));
}

export function ApiProfileCountersIncrementSave(getParams = {}, postData = null) {
    // see profile.counters.increment-save
    // controller: App\Http\Controllers\Profile\CounterController@incrementSave
    return apiClient.post(makeQuery('/home/counters/json/increment', getParams), prepareRequestData(postData));
}

export function ApiProfileCountersIndex(getParams = {}, postData = null) {
    // see profile.counters.index
    // controller: App\Http\Controllers\Profile\CounterController@index
    return apiClient.get(makeQuery('/home/counters', getParams), prepareRequestData(postData));
}

export function ApiProfileCountersView(counter, getParams = {}, postData = null) {
    // see profile.counters.view
    // controller: App\Http\Controllers\Profile\CounterController@view
    return apiClient.get(makeQuery('/home/counters/'+counter+'', getParams), prepareRequestData(postData));
}

export function ApiProfileHelpDeskIndex(getParams = {}, postData = null) {
    // see profile.help-desk.index
    // controller: App\Http\Controllers\Profile\HelpDeskController@index
    return apiClient.get(makeQuery('/home/help-desk', getParams), prepareRequestData(postData));
}

export function ApiProfileHelpDeskList(getParams = {}, postData = null) {
    // see profile.help-desk.list
    // controller: App\Http\Controllers\Profile\HelpDeskController@list
    return apiClient.get(makeQuery('/home/help-desk/json/list', getParams), prepareRequestData(postData));
}

export function ApiProfileHelpDeskView(id, getParams = {}, postData = null) {
    // see profile.help-desk.view
    // controller: App\Http\Controllers\Profile\HelpDeskController@view
    return apiClient.get(makeQuery('/home/help-desk/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiProfileInvoicesIndex(getParams = {}, postData = null) {
    // see profile.invoices.index
    // controller: App\Http\Controllers\Profile\HomeController@invoices
    return apiClient.get(makeQuery('/home/invoices', getParams), prepareRequestData(postData));
}

export function ApiProfileInvoicesJson(getParams = {}, postData = null) {
    // see profile.invoices.json
    // controller: App\Http\Controllers\Profile\HomeController@invoicesJson
    return apiClient.get(makeQuery('/home/invoices/json', getParams), prepareRequestData(postData));
}

export function ApiProfileLoginLink(getParams = {}, postData = null) {
    // see profile.login-link
    // controller: App\Http\Controllers\Profile\ProfileController@makeLoginLink
    return apiClient.post(makeQuery('/home/profile/login-link', getParams), prepareRequestData(postData));
}

export function ApiProfileLoginLinkGet(getParams = {}, postData = null) {
    // see profile.login-link.get
    // controller: App\Http\Controllers\Profile\ProfileController@getLoginLink
    return apiClient.get(makeQuery('/home/profile/login-link', getParams), prepareRequestData(postData));
}

export function ApiProfilePaymentsIndex(getParams = {}, postData = null) {
    // see profile.payments.index
    // controller: App\Http\Controllers\Profile\PaymentHistoryController@index
    return apiClient.get(makeQuery('/home/payments', getParams), prepareRequestData(postData));
}

export function ApiProfilePaymentsList(getParams = {}, postData = null) {
    // see profile.payments.list
    // controller: App\Http\Controllers\Profile\PaymentHistoryController@list
    return apiClient.get(makeQuery('/home/payments/json/list', getParams), prepareRequestData(postData));
}

export function ApiProfileSavePassword(getParams = {}, postData = null) {
    // see profile.save.password
    // controller: App\Http\Controllers\Profile\ProfileController@savePassword
    return apiClient.post(makeQuery('/home/profile/password', getParams), prepareRequestData(postData));
}

export function ApiRegulation(getParams = {}, postData = null) {
    // see regulation
    // controller: App\Http\Controllers\Public\PagesController@regulation
    return apiClient.get(makeQuery('/regulation', getParams), prepareRequestData(postData));
}

export function ApiRequests(getParams = {}, postData = null) {
    // see requests
    // controller: App\Http\Controllers\Public\RequestsPagesController@index
    return apiClient.get(makeQuery('/contacts/requests', getParams), prepareRequestData(postData));
}

export function ApiSanctumCsrfCookie(getParams = {}, postData = null) {
    // see sanctum.csrf-cookie
    // controller: Laravel\Sanctum\Http\Controllers\CsrfCookieController@show
    return apiClient.get(makeQuery('/sanctum/csrf-cookie', getParams), prepareRequestData(postData));
}

export function ApiSearch(getParams = {}, postData = null) {
    // see search
    // controller: App\Http\Controllers\Public\PagesController@search
    return apiClient.get(makeQuery('/search', getParams), prepareRequestData(postData));
}

export function ApiSearchSite(getParams = {}, postData = null) {
    // see search.site
    // controller: App\Http\Controllers\Public\SearchController@search
    return apiClient.post(makeQuery('/search/json/search', getParams), prepareRequestData(postData));
}

export function ApiSessionStore(getParams = {}, postData = null) {
    // see session.store
    // controller: App\Http\Controllers\SessionController@store
    return apiClient.post(makeQuery('/session', getParams), prepareRequestData(postData));
}

export function ApiTemplateGet(getParams = {}, postData = null) {
    // see template.get
    // controller: App\Http\Controllers\Public\TemplateController@get
    return apiClient.post(makeQuery('/pages/json/edit', getParams), prepareRequestData(postData));
}

export function ApiTemplateUpdate(getParams = {}, postData = null) {
    // see template.update
    // controller: App\Http\Controllers\Public\TemplateController@update
    return apiClient.patch(makeQuery('/pages/json/edit', getParams), prepareRequestData(postData));
}

export function ApiTerms(getParams = {}, postData = null) {
    // see terms
    // controller: App\Http\Controllers\Public\PagesController@terms
    return apiClient.get(makeQuery('/terms', getParams), prepareRequestData(postData));
}

export function ApiToken(token, getParams = {}, postData = null) {
    // see token
    // controller: App\Http\Controllers\TokenController@token
    return apiClient.get(makeQuery('/token/'+token+'', getParams), prepareRequestData(postData));
}

export function ApiTreasuryCharges(accountId, getParams = {}, postData = null) {
    // see treasury.charges
    // controller: App\Http\Controllers\Treasury\TreasuryController@charges
    return apiClient.get(makeQuery('/treasury/accounts/'+accountId+'/charges', getParams), prepareRequestData(postData));
}

export function ApiTreasuryCounterAddValue(getParams = {}, postData = null) {
    // see treasury.counter.add-value
    // controller: App\Http\Controllers\Treasury\TreasuryController@addCounterValue
    return apiClient.post(makeQuery('/treasury/counter/add-value', getParams), prepareRequestData(postData));
}

export function ApiTreasuryCounterCreate(getParams = {}, postData = null) {
    // see treasury.counter.create
    // controller: App\Http\Controllers\Treasury\TreasuryController@createCounter
    return apiClient.post(makeQuery('/treasury/counter/create', getParams), prepareRequestData(postData));
}

export function ApiTreasuryCounterUpdate(getParams = {}, postData = null) {
    // see treasury.counter.update
    // controller: App\Http\Controllers\Treasury\TreasuryController@updateCounter
    return apiClient.post(makeQuery('/treasury/counter/update', getParams), prepareRequestData(postData));
}

export function ApiTreasuryCounters(accountId, getParams = {}, postData = null) {
    // see treasury.counters
    // controller: App\Http\Controllers\Treasury\TreasuryController@counters
    return apiClient.get(makeQuery('/treasury/accounts/'+accountId+'/counters', getParams), prepareRequestData(postData));
}

export function ApiTreasuryIndex(getParams = {}, postData = null) {
    // see treasury.index
    // controller: App\Http\Controllers\Treasury\TreasuryController@index
    return apiClient.get(makeQuery('/treasury', getParams), prepareRequestData(postData));
}

export function ApiTreasuryPay(getParams = {}, postData = null) {
    // see treasury.pay
    // controller: App\Http\Controllers\Treasury\TreasuryController@pay
    return apiClient.post(makeQuery('/treasury/pay', getParams), prepareRequestData(postData));
}

export function ApiTreasurySearch(getParams = {}, postData = null) {
    // see treasury.search
    // controller: App\Http\Controllers\Treasury\TreasuryController@search
    return apiClient.post(makeQuery('/treasury/search', getParams), prepareRequestData(postData));
}

export function ApiVerificationNotice(getParams = {}, postData = null) {
    // see verification.notice
    // controller: App\Http\Controllers\Auth\VerificationController@show
    return apiClient.get(makeQuery('/email/verify', getParams), prepareRequestData(postData));
}

export function ApiVerificationResend(getParams = {}, postData = null) {
    // see verification.resend
    // controller: App\Http\Controllers\Auth\VerificationController@resend
    return apiClient.post(makeQuery('/email/resend', getParams), prepareRequestData(postData));
}

export function ApiVerificationVerify(id,hash, getParams = {}, postData = null) {
    // see verification.verify
    // controller: App\Http\Controllers\Auth\VerificationController@verify
    return apiClient.get(makeQuery('/email/verify/'+id+'/'+hash+'', getParams), prepareRequestData(postData));
}

export function ApiWebhookAcquringFailed(acquringId,salt, getParams = {}, postData = null) {
    // see webhook.acquring.failed
    // controller: App\Http\Controllers\Webhook\AcquiringController@failed
    return apiClient.delete(makeQuery('/webhook/acquring/failed/'+acquringId+'/'+salt+'', getParams), prepareRequestData(postData));
}

export function ApiWebhookAcquringSubmit(acquringId,salt, getParams = {}, postData = null) {
    // see webhook.acquring.submit
    // controller: App\Http\Controllers\Webhook\AcquiringController@submit
    return apiClient.delete(makeQuery('/webhook/acquring/submit/'+acquringId+'/'+salt+'', getParams), prepareRequestData(postData));
}