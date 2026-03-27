import { makeQuery, prepareRequestData } from './helpers.js';

export function ApiAcquringInvoiceCreate(invoiceId,amount, getParams = {}, postData = null) {
    // see acquring.invoice.create
    return window.axios.post(makeQuery('/home/acquring/create/'+invoiceId+'/'+amount+'', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountCreate(getParams = {}, postData = null) {
    // see admin.account.create
    return window.axios.get(makeQuery('/admin/accounts/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountDelete(id, getParams = {}, postData = null) {
    // see admin.account.delete
    return window.axios.delete(makeQuery('/admin/accounts/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountGet(accountId, getParams = {}, postData = null) {
    // see admin.account.get
    return window.axios.get(makeQuery('/admin/accounts/json/view/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountIndex(getParams = {}, postData = null) {
    // see admin.account.index
    return window.axios.get(makeQuery('/admin/accounts', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountInvoiceList(accountId, getParams = {}, postData = null) {
    // see admin.account.invoice.list
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/invoices/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountList(getParams = {}, postData = null) {
    // see admin.account.list
    return window.axios.get(makeQuery('/admin/accounts/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountSave(getParams = {}, postData = null) {
    // see admin.account.save
    return window.axios.post(makeQuery('/admin/accounts/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminAccountView(accountId, getParams = {}, postData = null) {
    // see admin.account.view
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.create
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/create', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimDelete(invoiceId,id, getParams = {}, postData = null) {
    // see admin.claim.delete
    return window.axios.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimList(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.list
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/list', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimSave(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.save
    return window.axios.post(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/save', getParams), prepareRequestData(postData));
}

export function ApiAdminClaimView(invoiceId,claimId, getParams = {}, postData = null) {
    // see admin.claim.view
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/get/'+claimId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterAddValue(accountId, getParams = {}, postData = null) {
    // see admin.counter.add-value
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/add-value', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterCreate(accountId, getParams = {}, postData = null) {
    // see admin.counter.create
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterDelete(accountId,counterId, getParams = {}, postData = null) {
    // see admin.counter.delete
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/delete/'+counterId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterHistoryList(counterId, getParams = {}, postData = null) {
    // see admin.counter.history.list
    return window.axios.get(makeQuery('/admin/counters/json/'+counterId+'/history/list', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterList(accountId, getParams = {}, postData = null) {
    // see admin.counter.list
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterSave(accountId, getParams = {}, postData = null) {
    // see admin.counter.save
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminCounterView(accountId,counterId, getParams = {}, postData = null) {
    // see admin.counter.view
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/counters/view/'+counterId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminDocumentReceiptInvoice(id, getParams = {}, postData = null) {
    // see admin.document.receipt.invoice
    return window.axios.get(makeQuery('/admin/invoices/view/'+id+'/invoice-receipt', getParams), prepareRequestData(postData));
}

export function ApiAdminEmailsDestroy(email, getParams = {}, postData = null) {
    // see admin.emails.destroy
    return window.axios.delete(makeQuery('/admin/emails/'+email+'', getParams), prepareRequestData(postData));
}

export function ApiAdminEmailsIndex(getParams = {}, postData = null) {
    // see admin.emails.index
    return window.axios.get(makeQuery('/admin/emails', getParams), prepareRequestData(postData));
}

export function ApiAdminEmailsShow(email, getParams = {}, postData = null) {
    // see admin.emails.show
    return window.axios.get(makeQuery('/admin/emails/'+email+'', getParams), prepareRequestData(postData));
}

export function ApiAdminErrorLogsDetails(filename,index, getParams = {}, postData = null) {
    // see admin.error-logs.details
    return window.axios.get(makeQuery('/admin/error-logs/'+filename+'/details/'+index+'', getParams), prepareRequestData(postData));
}

export function ApiAdminErrorLogsIndex(getParams = {}, postData = null) {
    // see admin.error-logs.index
    return window.axios.get(makeQuery('/admin/error-logs', getParams), prepareRequestData(postData));
}

export function ApiAdminErrorLogsShow(filename, getParams = {}, postData = null) {
    // see admin.error-logs.show
    return window.axios.get(makeQuery('/admin/error-logs/'+filename+'', getParams), prepareRequestData(postData));
}

export function ApiAdminIndex(getParams = {}, postData = null) {
    // see admin.index
    return window.axios.get(makeQuery('/admin', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceCreate(getParams = {}, postData = null) {
    // see admin.invoice.create
    return window.axios.get(makeQuery('/admin/invoices/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceCreateRegularInvoices(periodId, getParams = {}, postData = null) {
    // see admin.invoice.create-regular-invoices
    return window.axios.post(makeQuery('/admin/invoices/json/create-regular-invoices/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceDelete(id, getParams = {}, postData = null) {
    // see admin.invoice.delete
    return window.axios.delete(makeQuery('/admin/invoices/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceExport(getParams = {}, postData = null) {
    // see admin.invoice.export
    return window.axios.get(makeQuery('/admin/invoices/export', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceGet(id, getParams = {}, postData = null) {
    // see admin.invoice.get
    return window.axios.get(makeQuery('/admin/invoices/json/get/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceGetAccountsCountWithoutRegular(periodId, getParams = {}, postData = null) {
    // see admin.invoice.get-accounts-count-without-regular
    return window.axios.get(makeQuery('/admin/invoices/json/get-without-regular/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceImportPaymentsIndex(periodId, getParams = {}, postData = null) {
    // see admin.invoice.import-payments.index
    return window.axios.get(makeQuery('/admin/invoices/import-payments/period-'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceImportPaymentsParseFile(periodId, getParams = {}, postData = null) {
    // see admin.invoice.import-payments.parse-file
    return window.axios.post(makeQuery('/admin/invoices/import-payments/period-'+periodId+'/parse-file', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceImportPaymentsSave(periodId, getParams = {}, postData = null) {
    // see admin.invoice.import-payments.save
    return window.axios.post(makeQuery('/admin/invoices/import-payments/period-'+periodId+'/save', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceIndex(getParams = {}, postData = null) {
    // see admin.invoice.index
    return window.axios.get(makeQuery('/admin/invoices', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceList(getParams = {}, postData = null) {
    // see admin.invoice.list
    return window.axios.get(makeQuery('/admin/invoices/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceRecalc(id, getParams = {}, postData = null) {
    // see admin.invoice.recalc
    return window.axios.post(makeQuery('/admin/invoices/json/recalc/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceSave(getParams = {}, postData = null) {
    // see admin.invoice.save
    return window.axios.post(makeQuery('/admin/invoices/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminInvoiceView(id, getParams = {}, postData = null) {
    // see admin.invoice.view
    return window.axios.get(makeQuery('/admin/invoices/view/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminLoginLink(userId,pin, getParams = {}, postData = null) {
    // see admin.login-link
    return window.axios.post(makeQuery('/admin/users/json/qr/login/'+userId+'/'+pin+'', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentDelete(id, getParams = {}, postData = null) {
    // see admin.new-payment.delete
    return window.axios.delete(makeQuery('/admin/invoices/payments/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentGetInvoices(accountId,periodId, getParams = {}, postData = null) {
    // see admin.new-payment.get-invoices
    return window.axios.get(makeQuery('/admin/invoices/payments/json/get-invoices/'+accountId+'/'+periodId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentIndex(getParams = {}, postData = null) {
    // see admin.new-payment.index
    return window.axios.get(makeQuery('/admin/invoices/payments', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentList(getParams = {}, postData = null) {
    // see admin.new-payment.list
    return window.axios.get(makeQuery('/admin/invoices/payments/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentSave(getParams = {}, postData = null) {
    // see admin.new-payment.save
    return window.axios.post(makeQuery('/admin/invoices/payments/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminNewPaymentView(paymentId, getParams = {}, postData = null) {
    // see admin.new-payment.view
    return window.axios.get(makeQuery('/admin/invoices/payments/json/get/'+paymentId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminOptionsIndex(getParams = {}, postData = null) {
    // see admin.options.index
    return window.axios.get(makeQuery('/admin/options', getParams), prepareRequestData(postData));
}

export function ApiAdminOptionsList(getParams = {}, postData = null) {
    // see admin.options.list
    return window.axios.get(makeQuery('/admin/options/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminOptionsSave(getParams = {}, postData = null) {
    // see admin.options.save
    return window.axios.post(makeQuery('/admin/options/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentAutoCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.auto-create
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/auto-create', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.create
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/create', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentDelete(invoiceId,id, getParams = {}, postData = null) {
    // see admin.payment.delete
    return window.axios.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentList(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.list
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/list', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentSave(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.save
    return window.axios.post(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/save', getParams), prepareRequestData(postData));
}

export function ApiAdminPaymentView(invoiceId,paymentId, getParams = {}, postData = null) {
    // see admin.payment.view
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/get/'+paymentId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodCreate(getParams = {}, postData = null) {
    // see admin.period.create
    return window.axios.get(makeQuery('/admin/periods/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodDelete(id, getParams = {}, postData = null) {
    // see admin.period.delete
    return window.axios.delete(makeQuery('/admin/periods/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodIndex(getParams = {}, postData = null) {
    // see admin.period.index
    return window.axios.get(makeQuery('/admin/periods', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodList(getParams = {}, postData = null) {
    // see admin.period.list
    return window.axios.get(makeQuery('/admin/periods/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminPeriodSave(getParams = {}, postData = null) {
    // see admin.period.save
    return window.axios.post(makeQuery('/admin/periods/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminQrView(uid, getParams = {}, postData = null) {
    // see admin.qr.view
    return window.axios.get(makeQuery('/admin/qr/view/'+uid+'', getParams), prepareRequestData(postData));
}

export function ApiAdminQueue(getParams = {}, postData = null) {
    // see admin.queue
    return window.axios.get(makeQuery('/admin/queue', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueClear(getParams = {}, postData = null) {
    // see admin.queue.clear
    return window.axios.post(makeQuery('/admin/queue/clear', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueStart(getParams = {}, postData = null) {
    // see admin.queue.start
    return window.axios.post(makeQuery('/admin/queue/start', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueStatus(getParams = {}, postData = null) {
    // see admin.queue.status
    return window.axios.get(makeQuery('/admin/queue/status', getParams), prepareRequestData(postData));
}

export function ApiAdminQueueStop(getParams = {}, postData = null) {
    // see admin.queue.stop
    return window.axios.post(makeQuery('/admin/queue/stop', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryConfirm(getParams = {}, postData = null) {
    // see admin.requests.counter-history.confirm
    return window.axios.post(makeQuery('/admin/counter-history/json/confirm', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryConfirmDelete(getParams = {}, postData = null) {
    // see admin.requests.counter-history.confirm-delete
    return window.axios.post(makeQuery('/admin/counter-history/json/confirm-delete', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryCreateClaim(historyId, getParams = {}, postData = null) {
    // see admin.requests.counter-history.create-claim
    return window.axios.post(makeQuery('/admin/counter-history/json/create-claim/'+historyId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryDelete(historyId, getParams = {}, postData = null) {
    // see admin.requests.counter-history.delete
    return window.axios.delete(makeQuery('/admin/counter-history/json/delete/'+historyId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryIndex(getParams = {}, postData = null) {
    // see admin.requests.counter-history.index
    return window.axios.get(makeQuery('/admin/counter-history', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryLink(getParams = {}, postData = null) {
    // see admin.requests.counter-history.link
    return window.axios.post(makeQuery('/admin/counter-history/json/link', getParams), prepareRequestData(postData));
}

export function ApiAdminRequestsCounterHistoryList(getParams = {}, postData = null) {
    // see admin.requests.counter-history.list
    return window.axios.get(makeQuery('/admin/counter-history/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleCreate(getParams = {}, postData = null) {
    // see admin.role.create
    return window.axios.get(makeQuery('/admin/roles/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleDelete(id, getParams = {}, postData = null) {
    // see admin.role.delete
    return window.axios.delete(makeQuery('/admin/roles/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleIndex(getParams = {}, postData = null) {
    // see admin.role.index
    return window.axios.get(makeQuery('/admin/roles', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleList(getParams = {}, postData = null) {
    // see admin.role.list
    return window.axios.get(makeQuery('/admin/roles/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminRoleSave(getParams = {}, postData = null) {
    // see admin.role.save
    return window.axios.post(makeQuery('/admin/roles/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsAccounts(getParams = {}, postData = null) {
    // see admin.selects.accounts
    return window.axios.get(makeQuery('/admin/json/selects/accounts', getParams), prepareRequestData(postData));
}

export function ApiAdminSelectsCounters(accountId, getParams = {}, postData = null) {
    // see admin.selects.counters
    return window.axios.get(makeQuery('/admin/json/selects/counters/'+accountId+'', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceCreate(getParams = {}, postData = null) {
    // see admin.service.create
    return window.axios.get(makeQuery('/admin/services/json/create', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceDelete(id, getParams = {}, postData = null) {
    // see admin.service.delete
    return window.axios.delete(makeQuery('/admin/services/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceIndex(getParams = {}, postData = null) {
    // see admin.service.index
    return window.axios.get(makeQuery('/admin/services', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceList(getParams = {}, postData = null) {
    // see admin.service.list
    return window.axios.get(makeQuery('/admin/services/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminServiceSave(getParams = {}, postData = null) {
    // see admin.service.save
    return window.axios.post(makeQuery('/admin/services/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminTopPanelIndex(getParams = {}, postData = null) {
    // see admin.top-panel.index
    return window.axios.get(makeQuery('/admin/json/top-panel', getParams), prepareRequestData(postData));
}

export function ApiAdminTopPanelSearch(getParams = {}, postData = null) {
    // see admin.top-panel.search
    return window.axios.post(makeQuery('/admin/json/top-panel', getParams), prepareRequestData(postData));
}

export function ApiAdminUserDelete(id, getParams = {}, postData = null) {
    // see admin.user.delete
    return window.axios.delete(makeQuery('/admin/users/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminUserExport(getParams = {}, postData = null) {
    // see admin.user.export
    return window.axios.get(makeQuery('/admin/users/export', getParams), prepareRequestData(postData));
}

export function ApiAdminUserGenerateEmail(getParams = {}, postData = null) {
    // see admin.user.generate-email
    return window.axios.post(makeQuery('/admin/users/json/generate-email', getParams), prepareRequestData(postData));
}

export function ApiAdminUserIndex(getParams = {}, postData = null) {
    // see admin.user.index
    return window.axios.get(makeQuery('/admin/users', getParams), prepareRequestData(postData));
}

export function ApiAdminUserList(getParams = {}, postData = null) {
    // see admin.user.list
    return window.axios.get(makeQuery('/admin/users/json/list', getParams), prepareRequestData(postData));
}

export function ApiAdminUserRestore(id, getParams = {}, postData = null) {
    // see admin.user.restore
    return window.axios.patch(makeQuery('/admin/users/json/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAdminUserSave(getParams = {}, postData = null) {
    // see admin.user.save
    return window.axios.post(makeQuery('/admin/users/json/save', getParams), prepareRequestData(postData));
}

export function ApiAdminUserSendInviteWithPassword(getParams = {}, postData = null) {
    // see admin.user.send.invite-with-password
    return window.axios.post(makeQuery('/admin/users/json/send-invite-password', getParams), prepareRequestData(postData));
}

export function ApiAdminUserSendRestorePassword(getParams = {}, postData = null) {
    // see admin.user.send.restore.password
    return window.axios.post(makeQuery('/admin/users/json/sendRestorePassword', getParams), prepareRequestData(postData));
}

export function ApiAdminUserView(id, getParams = {}, postData = null) {
    // see admin.user.view
    return window.axios.get(makeQuery('/admin/users/view/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiAnnouncementsIndex(getParams = {}, postData = null) {
    // see announcements.index
    return window.axios.get(makeQuery('/announcements', getParams), prepareRequestData(postData));
}

export function ApiAnnouncementsList(getParams = {}, postData = null) {
    // see announcements.list
    return window.axios.get(makeQuery('/announcements/json/list', getParams), prepareRequestData(postData));
}

export function ApiAnnouncementsShow(id, getParams = {}, postData = null) {
    // see announcements.show
    return window.axios.get(makeQuery('/announcements/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiCommonSummary(getParams = {}, postData = null) {
    // see common.summary
    return window.axios.get(makeQuery('/json/summary', getParams), prepareRequestData(postData));
}

export function ApiCommonSummaryDetailing(type, getParams = {}, postData = null) {
    // see common.summary.detailing
    return window.axios.get(makeQuery('/json/summary/'+type+'', getParams), prepareRequestData(postData));
}

export function ApiContacts(getParams = {}, postData = null) {
    // see contacts
    return window.axios.get(makeQuery('/contacts', getParams), prepareRequestData(postData));
}

export function ApiCookieAgreement(getParams = {}, postData = null) {
    // see cookie_agreement
    return window.axios.post(makeQuery('/cookie-agreement', getParams), prepareRequestData(postData));
}

export function ApiCounter(getParams = {}, postData = null) {
    // see counter
    return window.axios.get(makeQuery('/contacts/requests/counter', getParams), prepareRequestData(postData));
}

export function ApiCounterCreate(getParams = {}, postData = null) {
    // see counter.create
    return window.axios.post(makeQuery('/contacts/requests/counter', getParams), prepareRequestData(postData));
}

export function ApiDocumentReceiptBlank(getParams = {}, postData = null) {
    // see document.receipt.blank
    return window.axios.get(makeQuery('/document/invoice-receipt/blank', getParams), prepareRequestData(postData));
}

export function ApiDocumentReceiptInvoice(uid, getParams = {}, postData = null) {
    // see document.receipt.invoice
    return window.axios.get(makeQuery('/document/invoice-receipt/'+uid+'', getParams), prepareRequestData(postData));
}

export function ApiFilesDelete(id, getParams = {}, postData = null) {
    // see files.delete
    return window.axios.delete(makeQuery('/files/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFilesDown(id, getParams = {}, postData = null) {
    // see files.down
    return window.axios.post(makeQuery('/files/json/down/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFilesEdit(id, getParams = {}, postData = null) {
    // see files.edit
    return window.axios.get(makeQuery('/files/json/edit/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFilesIndex(folder, getParams = {}, postData = null) {
    // see files.index
    return window.axios.get(makeQuery('/files/'+folder+'', getParams), prepareRequestData(postData));
}

export function ApiFilesList(getParams = {}, postData = null) {
    // see files.list
    return window.axios.get(makeQuery('/files/json/list', getParams), prepareRequestData(postData));
}

export function ApiFilesMove(getParams = {}, postData = null) {
    // see files.move
    return window.axios.post(makeQuery('/files/json/move', getParams), prepareRequestData(postData));
}

export function ApiFilesReplace(getParams = {}, postData = null) {
    // see files.replace
    return window.axios.post(makeQuery('/files/json/replace', getParams), prepareRequestData(postData));
}

export function ApiFilesSave(getParams = {}, postData = null) {
    // see files.save
    return window.axios.post(makeQuery('/files/json/save', getParams), prepareRequestData(postData));
}

export function ApiFilesStore(getParams = {}, postData = null) {
    // see files.store
    return window.axios.post(makeQuery('/files/json/store', getParams), prepareRequestData(postData));
}

export function ApiFilesUp(id, getParams = {}, postData = null) {
    // see files.up
    return window.axios.post(makeQuery('/files/json/up/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFoldersDelete(id, getParams = {}, postData = null) {
    // see folders.delete
    return window.axios.delete(makeQuery('/folders/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFoldersInfo(id, getParams = {}, postData = null) {
    // see folders.info
    return window.axios.get(makeQuery('/folders/json/info/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiFoldersList(getParams = {}, postData = null) {
    // see folders.list
    return window.axios.get(makeQuery('/folders/json/list', getParams), prepareRequestData(postData));
}

export function ApiFoldersSave(getParams = {}, postData = null) {
    // see folders.save
    return window.axios.post(makeQuery('/folders/json/save', getParams), prepareRequestData(postData));
}

export function ApiFoldersShow(id, getParams = {}, postData = null) {
    // see folders.show
    return window.axios.get(makeQuery('/folders/json/show/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiGarbage(getParams = {}, postData = null) {
    // see garbage
    return window.axios.get(makeQuery('/garbage', getParams), prepareRequestData(postData));
}

export function ApiHome(getParams = {}, postData = null) {
    // see home
    return window.axios.get(makeQuery('/home', getParams), prepareRequestData(postData));
}

export function ApiIndex(getParams = {}, postData = null) {
    // see index
    return window.axios.get(makeQuery('/', getParams), prepareRequestData(postData));
}

export function ApiInfraHistoryChanges(getParams = {}, postData = null) {
    // see infra.history-changes
    return window.axios.get(makeQuery('/admin/history/changes', getParams), prepareRequestData(postData));
}

export function ApiLogin(getParams = {}, postData = null) {
    // see login
    return window.axios.post(makeQuery('/login', getParams), prepareRequestData(postData));
}

export function ApiLoginDo(token, getParams = {}, postData = null) {
    // see login.do
    return window.axios.post(makeQuery('/login/'+token+'', getParams), prepareRequestData(postData));
}

export function ApiLogout(getParams = {}, postData = null) {
    // see logout
    return window.axios.get(makeQuery('/logout', getParams), prepareRequestData(postData));
}

export function ApiNewsCreate(getParams = {}, postData = null) {
    // see news.create
    return window.axios.get(makeQuery('/news/json/create', getParams), prepareRequestData(postData));
}

export function ApiNewsDelete(id, getParams = {}, postData = null) {
    // see news.delete
    return window.axios.delete(makeQuery('/news/json/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsEdit(id, getParams = {}, postData = null) {
    // see news.edit
    return window.axios.get(makeQuery('/news/json/edit/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsFileDelete(id, getParams = {}, postData = null) {
    // see news.file.delete
    return window.axios.delete(makeQuery('/news/json/file/delete/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsFileSave(getParams = {}, postData = null) {
    // see news.file.save
    return window.axios.post(makeQuery('/news/json/file/save', getParams), prepareRequestData(postData));
}

export function ApiNewsFileUpload(id, getParams = {}, postData = null) {
    // see news.file.upload
    return window.axios.post(makeQuery('/news/json/file/upload/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiNewsIndex(getParams = {}, postData = null) {
    // see news.index
    return window.axios.get(makeQuery('/news', getParams), prepareRequestData(postData));
}

export function ApiNewsList(getParams = {}, postData = null) {
    // see news.list
    return window.axios.get(makeQuery('/news/json/list', getParams), prepareRequestData(postData));
}

export function ApiNewsListIndex(getParams = {}, postData = null) {
    // see news.list.index
    return window.axios.get(makeQuery('/news/json/list/index', getParams), prepareRequestData(postData));
}

export function ApiNewsListLocked(getParams = {}, postData = null) {
    // see news.list.locked
    return window.axios.get(makeQuery('/news/json/list/locked', getParams), prepareRequestData(postData));
}

export function ApiNewsSave(getParams = {}, postData = null) {
    // see news.save
    return window.axios.post(makeQuery('/news/json/save', getParams), prepareRequestData(postData));
}

export function ApiNewsShow(id, getParams = {}, postData = null) {
    // see news.show
    return window.axios.get(makeQuery('/news/'+id+'', getParams), prepareRequestData(postData));
}

export function ApiPasswordConfirm(getParams = {}, postData = null) {
    // see password.confirm
    return window.axios.get(makeQuery('/password/confirm', getParams), prepareRequestData(postData));
}

export function ApiPasswordEmail(getParams = {}, postData = null) {
    // see password.email
    return window.axios.post(makeQuery('/password/email', getParams), prepareRequestData(postData));
}

export function ApiPasswordRequest(getParams = {}, postData = null) {
    // see password.request
    return window.axios.get(makeQuery('/password/reset', getParams), prepareRequestData(postData));
}

export function ApiPasswordReset(token, getParams = {}, postData = null) {
    // see password.reset
    return window.axios.get(makeQuery('/password/reset/'+token+'', getParams), prepareRequestData(postData));
}

export function ApiPasswordSave(getParams = {}, postData = null) {
    // see password.save
    return window.axios.post(makeQuery('/password/set', getParams), prepareRequestData(postData));
}

export function ApiPasswordSet(getParams = {}, postData = null) {
    // see password.set
    return window.axios.get(makeQuery('/password/set', getParams), prepareRequestData(postData));
}

export function ApiPasswordUpdate(getParams = {}, postData = null) {
    // see password.update
    return window.axios.post(makeQuery('/password/reset', getParams), prepareRequestData(postData));
}

export function ApiPayment(getParams = {}, postData = null) {
    // see payment
    return window.axios.get(makeQuery('/contacts/requests/payment', getParams), prepareRequestData(postData));
}

export function ApiPaymentCreate(getParams = {}, postData = null) {
    // see payment.create
    return window.axios.post(makeQuery('/contacts/requests/payment', getParams), prepareRequestData(postData));
}

export function ApiPrivacy(getParams = {}, postData = null) {
    // see privacy
    return window.axios.get(makeQuery('/privacy', getParams), prepareRequestData(postData));
}

export function ApiProfileAccountSwitch(getParams = {}, postData = null) {
    // see profile.account.switch
    return window.axios.post(makeQuery('/home/profile/switch-account', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterAddValue(getParams = {}, postData = null) {
    // see profile.counter.add-value
    return window.axios.post(makeQuery('/home/counters/json/add-value', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterCreate(getParams = {}, postData = null) {
    // see profile.counter.create
    return window.axios.post(makeQuery('/home/counters/json/create', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterHistoryList(getParams = {}, postData = null) {
    // see profile.counter.history-list
    return window.axios.post(makeQuery('/home/counters/json/history', getParams), prepareRequestData(postData));
}

export function ApiProfileCounterList(getParams = {}, postData = null) {
    // see profile.counter.list
    return window.axios.get(makeQuery('/home/counters/json/list', getParams), prepareRequestData(postData));
}

export function ApiProfileCountersIncrementSave(getParams = {}, postData = null) {
    // see profile.counters.increment-save
    return window.axios.post(makeQuery('/home/counters/json/increment', getParams), prepareRequestData(postData));
}

export function ApiProfileCountersIndex(getParams = {}, postData = null) {
    // see profile.counters.index
    return window.axios.get(makeQuery('/home/counters', getParams), prepareRequestData(postData));
}

export function ApiProfileCountersView(counter, getParams = {}, postData = null) {
    // see profile.counters.view
    return window.axios.get(makeQuery('/home/counters/'+counter+'', getParams), prepareRequestData(postData));
}

export function ApiProfileInvoicesIndex(getParams = {}, postData = null) {
    // see profile.invoices.index
    return window.axios.get(makeQuery('/home/invoices', getParams), prepareRequestData(postData));
}

export function ApiProfileSavePassword(getParams = {}, postData = null) {
    // see profile.save.password
    return window.axios.post(makeQuery('/home/profile/password', getParams), prepareRequestData(postData));
}

export function ApiProposal(getParams = {}, postData = null) {
    // see proposal
    return window.axios.get(makeQuery('/contacts/requests/proposal', getParams), prepareRequestData(postData));
}

export function ApiProposalCreate(getParams = {}, postData = null) {
    // see proposal.create
    return window.axios.post(makeQuery('/contacts/requests/proposal', getParams), prepareRequestData(postData));
}

export function ApiRegulation(getParams = {}, postData = null) {
    // see regulation
    return window.axios.get(makeQuery('/regulation', getParams), prepareRequestData(postData));
}

export function ApiRequests(getParams = {}, postData = null) {
    // see requests
    return window.axios.get(makeQuery('/contacts/requests', getParams), prepareRequestData(postData));
}

export function ApiSanctumCsrfCookie(getParams = {}, postData = null) {
    // see sanctum.csrf-cookie
    return window.axios.get(makeQuery('/sanctum/csrf-cookie', getParams), prepareRequestData(postData));
}

export function ApiSearch(getParams = {}, postData = null) {
    // see search
    return window.axios.get(makeQuery('/search', getParams), prepareRequestData(postData));
}

export function ApiSearchSite(getParams = {}, postData = null) {
    // see search.site
    return window.axios.post(makeQuery('/search/json/search', getParams), prepareRequestData(postData));
}

export function ApiSessionStore(getParams = {}, postData = null) {
    // see session.store
    return window.axios.post(makeQuery('/session', getParams), prepareRequestData(postData));
}

export function ApiTemplateGet(getParams = {}, postData = null) {
    // see template.get
    return window.axios.post(makeQuery('/pages/json/edit', getParams), prepareRequestData(postData));
}

export function ApiTemplateUpdate(getParams = {}, postData = null) {
    // see template.update
    return window.axios.patch(makeQuery('/pages/json/edit', getParams), prepareRequestData(postData));
}

export function ApiToken(token, getParams = {}, postData = null) {
    // see token
    return window.axios.get(makeQuery('/token/'+token+'', getParams), prepareRequestData(postData));
}

export function ApiVerificationNotice(getParams = {}, postData = null) {
    // see verification.notice
    return window.axios.get(makeQuery('/email/verify', getParams), prepareRequestData(postData));
}

export function ApiVerificationResend(getParams = {}, postData = null) {
    // see verification.resend
    return window.axios.post(makeQuery('/email/resend', getParams), prepareRequestData(postData));
}

export function ApiVerificationVerify(id,hash, getParams = {}, postData = null) {
    // see verification.verify
    return window.axios.get(makeQuery('/email/verify/'+id+'/'+hash+'', getParams), prepareRequestData(postData));
}

export function ApiWebhookAcquringFailed(acquringId,salt, getParams = {}, postData = null) {
    // see webhook.acquring.failed
    return window.axios.delete(makeQuery('/webhook/acquring/failed/'+acquringId+'/'+salt+'', getParams), prepareRequestData(postData));
}

export function ApiWebhookAcquringSubmit(acquringId,salt, getParams = {}, postData = null) {
    // see webhook.acquring.submit
    return window.axios.delete(makeQuery('/webhook/acquring/submit/'+acquringId+'/'+salt+'', getParams), prepareRequestData(postData));
}