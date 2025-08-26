function makeQuery(uri, getParams = {}) {
    let getQuery = [];
    Object.keys(getParams).forEach(key => {
        if (getParams[key] && String(getParams[key]) !== '0') {
            getQuery = getQuery.concat([key + '=' + getParams[key]]);
        }
    });
    if (getQuery.length) {
        uri = uri + '?' + getQuery.join('&');
    }

    return uri;
}

export function adminAccountCreate(getParams = {}, postData = null) {
    // see admin.account.create
    return window.axios.get(makeQuery('/admin/accounts/json/create', getParams), postData);
}

export function adminAccountDelete(id, getParams = {}, postData = null) {
    // see admin.account.delete
    return window.axios.delete(makeQuery('/admin/accounts/json/'+id+'', getParams), postData);
}

export function adminAccountIndex(getParams = {}, postData = null) {
    // see admin.account.index
    return window.axios.get(makeQuery('/admin/accounts', getParams), postData);
}

export function adminAccountInvoiceList(accountId, getParams = {}, postData = null) {
    // see admin.account.invoice.list
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/invoices/json/list', getParams), postData);
}

export function adminAccountList(getParams = {}, postData = null) {
    // see admin.account.list
    return window.axios.get(makeQuery('/admin/accounts/json/list', getParams), postData);
}

export function adminAccountSave(getParams = {}, postData = null) {
    // see admin.account.save
    return window.axios.post(makeQuery('/admin/accounts/json/save', getParams), postData);
}

export function adminAccountView(accountId, getParams = {}, postData = null) {
    // see admin.account.view
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'', getParams), postData);
}

export function adminClaimCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.create
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/create', getParams), postData);
}

export function adminClaimDelete(invoiceId,id, getParams = {}, postData = null) {
    // see admin.claim.delete
    return window.axios.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/delete/'+id+'', getParams), postData);
}

export function adminClaimList(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.list
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/list', getParams), postData);
}

export function adminClaimSave(invoiceId, getParams = {}, postData = null) {
    // see admin.claim.save
    return window.axios.post(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/save', getParams), postData);
}

export function adminClaimView(invoiceId,claimId, getParams = {}, postData = null) {
    // see admin.claim.view
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/get/'+claimId+'', getParams), postData);
}

export function adminCounterAddValue(accountId, getParams = {}, postData = null) {
    // see admin.counter.add-value
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/add-value', getParams), postData);
}

export function adminCounterCreate(accountId, getParams = {}, postData = null) {
    // see admin.counter.create
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/create', getParams), postData);
}

export function adminCounterDelete(accountId,counterId, getParams = {}, postData = null) {
    // see admin.counter.delete
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/delete/'+counterId+'', getParams), postData);
}

export function adminCounterHistoryList(counterId, getParams = {}, postData = null) {
    // see admin.counter.history.list
    return window.axios.get(makeQuery('/admin/counters/json/'+counterId+'/history/list', getParams), postData);
}

export function adminCounterList(accountId, getParams = {}, postData = null) {
    // see admin.counter.list
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/list', getParams), postData);
}

export function adminCounterSave(accountId, getParams = {}, postData = null) {
    // see admin.counter.save
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/counters/json/save', getParams), postData);
}

export function adminCounterView(accountId,counterId, getParams = {}, postData = null) {
    // see admin.counter.view
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/counters/view/'+counterId+'', getParams), postData);
}

export function adminErrorLogsDetails(filename,index, getParams = {}, postData = null) {
    // see admin.error-logs.details
    return window.axios.get(makeQuery('/admin/error-logs/'+filename+'/details/'+index+'', getParams), postData);
}

export function adminErrorLogsIndex(getParams = {}, postData = null) {
    // see admin.error-logs.index
    return window.axios.get(makeQuery('/admin/error-logs', getParams), postData);
}

export function adminErrorLogsShow(filename, getParams = {}, postData = null) {
    // see admin.error-logs.show
    return window.axios.get(makeQuery('/admin/error-logs/'+filename+'', getParams), postData);
}

export function adminIndex(getParams = {}, postData = null) {
    // see admin.index
    return window.axios.get(makeQuery('/admin', getParams), postData);
}

export function adminInvoiceCreate(getParams = {}, postData = null) {
    // see admin.invoice.create
    return window.axios.get(makeQuery('/admin/invoices/json/create', getParams), postData);
}

export function adminInvoiceCreateRegularInvoices(periodId, getParams = {}, postData = null) {
    // see admin.invoice.create-regular-invoices
    return window.axios.post(makeQuery('/admin/invoices/json/create-regular-invoices/'+periodId+'', getParams), postData);
}

export function adminInvoiceDelete(id, getParams = {}, postData = null) {
    // see admin.invoice.delete
    return window.axios.delete(makeQuery('/admin/invoices/json/delete/'+id+'', getParams), postData);
}

export function adminInvoiceExport(getParams = {}, postData = null) {
    // see admin.invoice.export
    return window.axios.get(makeQuery('/admin/invoices/export', getParams), postData);
}

export function adminInvoiceGet(id, getParams = {}, postData = null) {
    // see admin.invoice.get
    return window.axios.get(makeQuery('/admin/invoices/json/get/'+id+'', getParams), postData);
}

export function adminInvoiceGetAccountsCountWithoutRegular(periodId, getParams = {}, postData = null) {
    // see admin.invoice.get-accounts-count-without-regular
    return window.axios.get(makeQuery('/admin/invoices/json/get-without-regular/'+periodId+'', getParams), postData);
}

export function adminInvoiceIndex(getParams = {}, postData = null) {
    // see admin.invoice.index
    return window.axios.get(makeQuery('/admin/invoices', getParams), postData);
}

export function adminInvoiceList(getParams = {}, postData = null) {
    // see admin.invoice.list
    return window.axios.get(makeQuery('/admin/invoices/json/list', getParams), postData);
}

export function adminInvoiceSave(getParams = {}, postData = null) {
    // see admin.invoice.save
    return window.axios.post(makeQuery('/admin/invoices/json/save', getParams), postData);
}

export function adminInvoiceView(id, getParams = {}, postData = null) {
    // see admin.invoice.view
    return window.axios.get(makeQuery('/admin/invoices/view/'+id+'', getParams), postData);
}

export function adminNewPaymentDelete(id, getParams = {}, postData = null) {
    // see admin.new-payment.delete
    return window.axios.delete(makeQuery('/admin/invoices/payments/json/delete/'+id+'', getParams), postData);
}

export function adminNewPaymentGetInvoices(accountId,periodId, getParams = {}, postData = null) {
    // see admin.new-payment.get-invoices
    return window.axios.get(makeQuery('/admin/invoices/payments/json/get-invoices/'+accountId+'/'+periodId+'', getParams), postData);
}

export function adminNewPaymentIndex(getParams = {}, postData = null) {
    // see admin.new-payment.index
    return window.axios.get(makeQuery('/admin/invoices/payments', getParams), postData);
}

export function adminNewPaymentList(getParams = {}, postData = null) {
    // see admin.new-payment.list
    return window.axios.get(makeQuery('/admin/invoices/payments/json/list', getParams), postData);
}

export function adminNewPaymentSave(getParams = {}, postData = null) {
    // see admin.new-payment.save
    return window.axios.post(makeQuery('/admin/invoices/payments/json/save', getParams), postData);
}

export function adminNewPaymentView(paymentId, getParams = {}, postData = null) {
    // see admin.new-payment.view
    return window.axios.get(makeQuery('/admin/invoices/payments/json/get/'+paymentId+'', getParams), postData);
}

export function adminOptionsIndex(getParams = {}, postData = null) {
    // see admin.options.index
    return window.axios.get(makeQuery('/admin/options', getParams), postData);
}

export function adminOptionsList(getParams = {}, postData = null) {
    // see admin.options.list
    return window.axios.get(makeQuery('/admin/options/json/list', getParams), postData);
}

export function adminOptionsSave(getParams = {}, postData = null) {
    // see admin.options.save
    return window.axios.post(makeQuery('/admin/options/json/save', getParams), postData);
}

export function adminPaymentAutoCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.auto-create
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/auto-create', getParams), postData);
}

export function adminPaymentCreate(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.create
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/create', getParams), postData);
}

export function adminPaymentDelete(invoiceId,id, getParams = {}, postData = null) {
    // see admin.payment.delete
    return window.axios.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/delete/'+id+'', getParams), postData);
}

export function adminPaymentList(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.list
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/list', getParams), postData);
}

export function adminPaymentSave(invoiceId, getParams = {}, postData = null) {
    // see admin.payment.save
    return window.axios.post(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/save', getParams), postData);
}

export function adminPaymentView(invoiceId,paymentId, getParams = {}, postData = null) {
    // see admin.payment.view
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/get/'+paymentId+'', getParams), postData);
}

export function adminPeriodCreate(getParams = {}, postData = null) {
    // see admin.period.create
    return window.axios.get(makeQuery('/admin/periods/json/create', getParams), postData);
}

export function adminPeriodDelete(id, getParams = {}, postData = null) {
    // see admin.period.delete
    return window.axios.delete(makeQuery('/admin/periods/json/'+id+'', getParams), postData);
}

export function adminPeriodIndex(getParams = {}, postData = null) {
    // see admin.period.index
    return window.axios.get(makeQuery('/admin/periods', getParams), postData);
}

export function adminPeriodList(getParams = {}, postData = null) {
    // see admin.period.list
    return window.axios.get(makeQuery('/admin/periods/json/list', getParams), postData);
}

export function adminPeriodSave(getParams = {}, postData = null) {
    // see admin.period.save
    return window.axios.post(makeQuery('/admin/periods/json/save', getParams), postData);
}

export function adminQueue(getParams = {}, postData = null) {
    // see admin.queue
    return window.axios.get(makeQuery('/admin/queue', getParams), postData);
}

export function adminQueueClear(getParams = {}, postData = null) {
    // see admin.queue.clear
    return window.axios.post(makeQuery('/admin/queue/clear', getParams), postData);
}

export function adminQueueStart(getParams = {}, postData = null) {
    // see admin.queue.start
    return window.axios.post(makeQuery('/admin/queue/start', getParams), postData);
}

export function adminQueueStatus(getParams = {}, postData = null) {
    // see admin.queue.status
    return window.axios.get(makeQuery('/admin/queue/status', getParams), postData);
}

export function adminQueueStop(getParams = {}, postData = null) {
    // see admin.queue.stop
    return window.axios.post(makeQuery('/admin/queue/stop', getParams), postData);
}

export function adminRequestsCounterHistoryConfirm(getParams = {}, postData = null) {
    // see admin.requests.counter-history.confirm
    return window.axios.post(makeQuery('/admin/counter-history/json/confirm', getParams), postData);
}

export function adminRequestsCounterHistoryConfirmDelete(getParams = {}, postData = null) {
    // see admin.requests.counter-history.confirm-delete
    return window.axios.post(makeQuery('/admin/counter-history/json/confirm-delete', getParams), postData);
}

export function adminRequestsCounterHistoryCreateClaim(historyId, getParams = {}, postData = null) {
    // see admin.requests.counter-history.create-claim
    return window.axios.post(makeQuery('/admin/counter-history/json/create-claim/'+historyId+'', getParams), postData);
}

export function adminRequestsCounterHistoryDelete(historyId, getParams = {}, postData = null) {
    // see admin.requests.counter-history.delete
    return window.axios.delete(makeQuery('/admin/counter-history/json/delete/'+historyId+'', getParams), postData);
}

export function adminRequestsCounterHistoryIndex(getParams = {}, postData = null) {
    // see admin.requests.counter-history.index
    return window.axios.get(makeQuery('/admin/counter-history', getParams), postData);
}

export function adminRequestsCounterHistoryLink(getParams = {}, postData = null) {
    // see admin.requests.counter-history.link
    return window.axios.post(makeQuery('/admin/counter-history/json/link', getParams), postData);
}

export function adminRequestsCounterHistoryList(getParams = {}, postData = null) {
    // see admin.requests.counter-history.list
    return window.axios.get(makeQuery('/admin/counter-history/json/list', getParams), postData);
}

export function adminRoleCreate(getParams = {}, postData = null) {
    // see admin.role.create
    return window.axios.get(makeQuery('/admin/roles/json/create', getParams), postData);
}

export function adminRoleDelete(id, getParams = {}, postData = null) {
    // see admin.role.delete
    return window.axios.delete(makeQuery('/admin/roles/json/'+id+'', getParams), postData);
}

export function adminRoleIndex(getParams = {}, postData = null) {
    // see admin.role.index
    return window.axios.get(makeQuery('/admin/roles', getParams), postData);
}

export function adminRoleList(getParams = {}, postData = null) {
    // see admin.role.list
    return window.axios.get(makeQuery('/admin/roles/json/list', getParams), postData);
}

export function adminRoleSave(getParams = {}, postData = null) {
    // see admin.role.save
    return window.axios.post(makeQuery('/admin/roles/json/save', getParams), postData);
}

export function adminSelectsAccounts(getParams = {}, postData = null) {
    // see admin.selects.accounts
    return window.axios.get(makeQuery('/admin/json/selects/accounts', getParams), postData);
}

export function adminSelectsCounters(accountId, getParams = {}, postData = null) {
    // see admin.selects.counters
    return window.axios.get(makeQuery('/admin/json/selects/counters/'+accountId+'', getParams), postData);
}

export function adminServiceCreate(getParams = {}, postData = null) {
    // see admin.service.create
    return window.axios.get(makeQuery('/admin/services/json/create', getParams), postData);
}

export function adminServiceDelete(id, getParams = {}, postData = null) {
    // see admin.service.delete
    return window.axios.delete(makeQuery('/admin/services/json/'+id+'', getParams), postData);
}

export function adminServiceIndex(getParams = {}, postData = null) {
    // see admin.service.index
    return window.axios.get(makeQuery('/admin/services', getParams), postData);
}

export function adminServiceList(getParams = {}, postData = null) {
    // see admin.service.list
    return window.axios.get(makeQuery('/admin/services/json/list', getParams), postData);
}

export function adminServiceSave(getParams = {}, postData = null) {
    // see admin.service.save
    return window.axios.post(makeQuery('/admin/services/json/save', getParams), postData);
}

export function adminTopPanelIndex(getParams = {}, postData = null) {
    // see admin.top-panel.index
    return window.axios.get(makeQuery('/admin/json/top-panel', getParams), postData);
}

export function adminTopPanelSearch(getParams = {}, postData = null) {
    // see admin.top-panel.search
    return window.axios.post(makeQuery('/admin/json/top-panel', getParams), postData);
}

export function adminUserDelete(id, getParams = {}, postData = null) {
    // see admin.user.delete
    return window.axios.delete(makeQuery('/admin/users/json/'+id+'', getParams), postData);
}

export function adminUserExport(getParams = {}, postData = null) {
    // see admin.user.export
    return window.axios.get(makeQuery('/admin/users/export', getParams), postData);
}

export function adminUserGenerateEmail(getParams = {}, postData = null) {
    // see admin.user.generate-email
    return window.axios.post(makeQuery('/admin/users/json/generate-email', getParams), postData);
}

export function adminUserIndex(getParams = {}, postData = null) {
    // see admin.user.index
    return window.axios.get(makeQuery('/admin/users', getParams), postData);
}

export function adminUserList(getParams = {}, postData = null) {
    // see admin.user.list
    return window.axios.get(makeQuery('/admin/users/json/list', getParams), postData);
}

export function adminUserRestore(id, getParams = {}, postData = null) {
    // see admin.user.restore
    return window.axios.patch(makeQuery('/admin/users/json/'+id+'', getParams), postData);
}

export function adminUserSave(getParams = {}, postData = null) {
    // see admin.user.save
    return window.axios.post(makeQuery('/admin/users/json/save', getParams), postData);
}

export function adminUserSendInviteWithPassword(getParams = {}, postData = null) {
    // see admin.user.send.invite-with-password
    return window.axios.post(makeQuery('/admin/users/json/send-invite-password', getParams), postData);
}

export function adminUserSendRestorePassword(getParams = {}, postData = null) {
    // see admin.user.send.restore.password
    return window.axios.post(makeQuery('/admin/users/json/sendRestorePassword', getParams), postData);
}

export function adminUserView(id, getParams = {}, postData = null) {
    // see admin.user.view
    return window.axios.get(makeQuery('/admin/users/view/'+id+'', getParams), postData);
}

export function announcementsIndex(getParams = {}, postData = null) {
    // see announcements.index
    return window.axios.get(makeQuery('/announcements', getParams), postData);
}

export function announcementsList(getParams = {}, postData = null) {
    // see announcements.list
    return window.axios.get(makeQuery('/announcements/json/list', getParams), postData);
}

export function announcementsShow(id, getParams = {}, postData = null) {
    // see announcements.show
    return window.axios.get(makeQuery('/announcements/'+id+'', getParams), postData);
}

export function commonSummary(getParams = {}, postData = null) {
    // see common.summary
    return window.axios.get(makeQuery('/json/summary', getParams), postData);
}

export function commonSummaryDetailing(type, getParams = {}, postData = null) {
    // see common.summary.detailing
    return window.axios.get(makeQuery('/json/summary/'+type+'', getParams), postData);
}

export function contacts(getParams = {}, postData = null) {
    // see contacts
    return window.axios.get(makeQuery('/contacts', getParams), postData);
}

export function cookieAgreement(getParams = {}, postData = null) {
    // see cookie_agreement
    return window.axios.post(makeQuery('/cookie-agreement', getParams), postData);
}

export function counter(getParams = {}, postData = null) {
    // see counter
    return window.axios.get(makeQuery('/contacts/requests/counter', getParams), postData);
}

export function counterCreate(getParams = {}, postData = null) {
    // see counter.create
    return window.axios.post(makeQuery('/contacts/requests/counter', getParams), postData);
}

export function filesDelete(id, getParams = {}, postData = null) {
    // see files.delete
    return window.axios.delete(makeQuery('/files/json/delete/'+id+'', getParams), postData);
}

export function filesDown(id, getParams = {}, postData = null) {
    // see files.down
    return window.axios.post(makeQuery('/files/json/down/'+id+'', getParams), postData);
}

export function filesEdit(id, getParams = {}, postData = null) {
    // see files.edit
    return window.axios.get(makeQuery('/files/json/edit/'+id+'', getParams), postData);
}

export function filesIndex(folder, getParams = {}, postData = null) {
    // see files.index
    return window.axios.get(makeQuery('/files/'+folder+'', getParams), postData);
}

export function filesList(getParams = {}, postData = null) {
    // see files.list
    return window.axios.get(makeQuery('/files/json/list', getParams), postData);
}

export function filesMove(getParams = {}, postData = null) {
    // see files.move
    return window.axios.post(makeQuery('/files/json/move', getParams), postData);
}

export function filesReplace(getParams = {}, postData = null) {
    // see files.replace
    return window.axios.post(makeQuery('/files/json/replace', getParams), postData);
}

export function filesSave(getParams = {}, postData = null) {
    // see files.save
    return window.axios.post(makeQuery('/files/json/save', getParams), postData);
}

export function filesStore(getParams = {}, postData = null) {
    // see files.store
    return window.axios.post(makeQuery('/files/json/store', getParams), postData);
}

export function filesUp(id, getParams = {}, postData = null) {
    // see files.up
    return window.axios.post(makeQuery('/files/json/up/'+id+'', getParams), postData);
}

export function foldersDelete(id, getParams = {}, postData = null) {
    // see folders.delete
    return window.axios.delete(makeQuery('/folders/json/delete/'+id+'', getParams), postData);
}

export function foldersList(getParams = {}, postData = null) {
    // see folders.list
    return window.axios.get(makeQuery('/folders/json/list', getParams), postData);
}

export function foldersSave(getParams = {}, postData = null) {
    // see folders.save
    return window.axios.post(makeQuery('/folders/json/save', getParams), postData);
}

export function foldersShow(id, getParams = {}, postData = null) {
    // see folders.show
    return window.axios.get(makeQuery('/folders/json/show/'+id+'', getParams), postData);
}

export function garbage(getParams = {}, postData = null) {
    // see garbage
    return window.axios.get(makeQuery('/garbage', getParams), postData);
}

export function home(getParams = {}, postData = null) {
    // see home
    return window.axios.get(makeQuery('/home', getParams), postData);
}

export function index(getParams = {}, postData = null) {
    // see index
    return window.axios.get(makeQuery('/', getParams), postData);
}

export function infraHistoryChanges(getParams = {}, postData = null) {
    // see infra.history-changes
    return window.axios.get(makeQuery('/admin/history/changes', getParams), postData);
}

export function login(getParams = {}, postData = null) {
    // see login
    return window.axios.post(makeQuery('/login', getParams), postData);
}

export function logout(getParams = {}, postData = null) {
    // see logout
    return window.axios.get(makeQuery('/logout', getParams), postData);
}

export function newsCreate(getParams = {}, postData = null) {
    // see news.create
    return window.axios.get(makeQuery('/news/json/create', getParams), postData);
}

export function newsDelete(id, getParams = {}, postData = null) {
    // see news.delete
    return window.axios.delete(makeQuery('/news/json/delete/'+id+'', getParams), postData);
}

export function newsEdit(id, getParams = {}, postData = null) {
    // see news.edit
    return window.axios.get(makeQuery('/news/json/edit/'+id+'', getParams), postData);
}

export function newsFileDelete(id, getParams = {}, postData = null) {
    // see news.file.delete
    return window.axios.delete(makeQuery('/news/json/file/delete/'+id+'', getParams), postData);
}

export function newsFileSave(getParams = {}, postData = null) {
    // see news.file.save
    return window.axios.post(makeQuery('/news/json/file/save', getParams), postData);
}

export function newsFileUpload(id, getParams = {}, postData = null) {
    // see news.file.upload
    return window.axios.post(makeQuery('/news/json/file/upload/'+id+'', getParams), postData);
}

export function newsIndex(getParams = {}, postData = null) {
    // see news.index
    return window.axios.get(makeQuery('/news', getParams), postData);
}

export function newsList(getParams = {}, postData = null) {
    // see news.list
    return window.axios.get(makeQuery('/news/json/list', getParams), postData);
}

export function newsListAll(getParams = {}, postData = null) {
    // see news.list.all
    return window.axios.get(makeQuery('/news/json/list/all', getParams), postData);
}

export function newsListLocked(getParams = {}, postData = null) {
    // see news.list.locked
    return window.axios.get(makeQuery('/news/json/list/locked', getParams), postData);
}

export function newsSave(getParams = {}, postData = null) {
    // see news.save
    return window.axios.post(makeQuery('/news/json/save', getParams), postData);
}

export function newsShow(id, getParams = {}, postData = null) {
    // see news.show
    return window.axios.get(makeQuery('/news/'+id+'', getParams), postData);
}

export function passwordConfirm(getParams = {}, postData = null) {
    // see password.confirm
    return window.axios.get(makeQuery('/password/confirm', getParams), postData);
}

export function passwordEmail(getParams = {}, postData = null) {
    // see password.email
    return window.axios.post(makeQuery('/password/email', getParams), postData);
}

export function passwordRequest(getParams = {}, postData = null) {
    // see password.request
    return window.axios.get(makeQuery('/password/reset', getParams), postData);
}

export function passwordReset(token, getParams = {}, postData = null) {
    // see password.reset
    return window.axios.get(makeQuery('/password/reset/'+token+'', getParams), postData);
}

export function passwordSave(getParams = {}, postData = null) {
    // see password.save
    return window.axios.post(makeQuery('/password/set', getParams), postData);
}

export function passwordSet(getParams = {}, postData = null) {
    // see password.set
    return window.axios.get(makeQuery('/password/set', getParams), postData);
}

export function passwordUpdate(getParams = {}, postData = null) {
    // see password.update
    return window.axios.post(makeQuery('/password/reset', getParams), postData);
}

export function payment(getParams = {}, postData = null) {
    // see payment
    return window.axios.get(makeQuery('/contacts/requests/payment', getParams), postData);
}

export function paymentCreate(getParams = {}, postData = null) {
    // see payment.create
    return window.axios.post(makeQuery('/contacts/requests/payment', getParams), postData);
}

export function privacy(getParams = {}, postData = null) {
    // see privacy
    return window.axios.get(makeQuery('/privacy', getParams), postData);
}

export function profileAccountSwitch(getParams = {}, postData = null) {
    // see profile.account.switch
    return window.axios.post(makeQuery('/home/profile/switch-account', getParams), postData);
}

export function profileCounterAddValue(getParams = {}, postData = null) {
    // see profile.counter.add-value
    return window.axios.post(makeQuery('/home/counters/json/add-value', getParams), postData);
}

export function profileCounterCreate(getParams = {}, postData = null) {
    // see profile.counter.create
    return window.axios.post(makeQuery('/home/counters/json/create', getParams), postData);
}

export function profileCounterHistoryList(getParams = {}, postData = null) {
    // see profile.counter.history-list
    return window.axios.post(makeQuery('/home/counters/json/history', getParams), postData);
}

export function profileCounterList(getParams = {}, postData = null) {
    // see profile.counter.list
    return window.axios.get(makeQuery('/home/counters/json/list', getParams), postData);
}

export function profileCountersIncrementSave(getParams = {}, postData = null) {
    // see profile.counters.increment-save
    return window.axios.post(makeQuery('/home/counters/json/increment', getParams), postData);
}

export function profileCountersIndex(getParams = {}, postData = null) {
    // see profile.counters.index
    return window.axios.get(makeQuery('/home/counters', getParams), postData);
}

export function profileCountersView(counter, getParams = {}, postData = null) {
    // see profile.counters.view
    return window.axios.get(makeQuery('/home/counters/'+counter+'', getParams), postData);
}

export function profileInvoicesIndex(getParams = {}, postData = null) {
    // see profile.invoices.index
    return window.axios.get(makeQuery('/home/invoices', getParams), postData);
}

export function profileSavePassword(getParams = {}, postData = null) {
    // see profile.save.password
    return window.axios.post(makeQuery('/home/profile/password', getParams), postData);
}

export function proposal(getParams = {}, postData = null) {
    // see proposal
    return window.axios.get(makeQuery('/contacts/requests/proposal', getParams), postData);
}

export function proposalCreate(getParams = {}, postData = null) {
    // see proposal.create
    return window.axios.post(makeQuery('/contacts/requests/proposal', getParams), postData);
}

export function regulation(getParams = {}, postData = null) {
    // see regulation
    return window.axios.get(makeQuery('/regulation', getParams), postData);
}

export function reportsCreate(getParams = {}, postData = null) {
    // see reports.create
    return window.axios.get(makeQuery('/reports/json/create', getParams), postData);
}

export function reportsDelete(id, getParams = {}, postData = null) {
    // see reports.delete
    return window.axios.delete(makeQuery('/reports/json/delete/'+id+'', getParams), postData);
}

export function reportsEdit(id, getParams = {}, postData = null) {
    // see reports.edit
    return window.axios.get(makeQuery('/reports/json/edit/'+id+'', getParams), postData);
}

export function reportsFileDelete(id, getParams = {}, postData = null) {
    // see reports.file.delete
    return window.axios.post(makeQuery('/reports/json/file/delete/'+id+'', getParams), postData);
}

export function reportsFileUpload(id, getParams = {}, postData = null) {
    // see reports.file.upload
    return window.axios.post(makeQuery('/reports/json/file/upload/'+id+'', getParams), postData);
}

export function reportsIndex(getParams = {}, postData = null) {
    // see reports.index
    return window.axios.get(makeQuery('/reports', getParams), postData);
}

export function reportsList(getParams = {}, postData = null) {
    // see reports.list
    return window.axios.get(makeQuery('/reports/json/list', getParams), postData);
}

export function reportsSave(getParams = {}, postData = null) {
    // see reports.save
    return window.axios.post(makeQuery('/reports/json/save', getParams), postData);
}

export function requests(getParams = {}, postData = null) {
    // see requests
    return window.axios.get(makeQuery('/contacts/requests', getParams), postData);
}

export function sanctumCsrfCookie(getParams = {}, postData = null) {
    // see sanctum.csrf-cookie
    return window.axios.get(makeQuery('/sanctum/csrf-cookie', getParams), postData);
}

export function search(getParams = {}, postData = null) {
    // see search
    return window.axios.get(makeQuery('/search', getParams), postData);
}

export function searchSite(getParams = {}, postData = null) {
    // see search.site
    return window.axios.post(makeQuery('/search/json/search', getParams), postData);
}

export function sessionStore(getParams = {}, postData = null) {
    // see session.store
    return window.axios.post(makeQuery('/session', getParams), postData);
}

export function templateGet(getParams = {}, postData = null) {
    // see template.get
    return window.axios.post(makeQuery('/pages/json/edit', getParams), postData);
}

export function templateUpdate(getParams = {}, postData = null) {
    // see template.update
    return window.axios.patch(makeQuery('/pages/json/edit', getParams), postData);
}

export function verificationNotice(getParams = {}, postData = null) {
    // see verification.notice
    return window.axios.get(makeQuery('/email/verify', getParams), postData);
}

export function verificationResend(getParams = {}, postData = null) {
    // see verification.resend
    return window.axios.post(makeQuery('/email/resend', getParams), postData);
}

export function verificationVerify(id,hash, getParams = {}, postData = null) {
    // see verification.verify
    return window.axios.get(makeQuery('/email/verify/'+id+'/'+hash+'', getParams), postData);
}