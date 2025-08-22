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

export function adminAccountCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts/json/create', getParams));
}

export function adminAccountDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/accounts/json/'+id+'', getParams));
}

export function adminAccountGet(accountId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts/json/view/'+accountId+'', getParams));
}

export function adminAccountIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts', getParams));
}

export function adminAccountList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts/json/list', getParams));
}

export function adminAccountSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/accounts/json/save', getParams));
}

export function adminAccountView(accountId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'', getParams));
}

export function adminClaimCreate(invoiceId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/create', getParams));
}

export function adminClaimDelete(invoiceId,id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/delete/'+id+'', getParams));
}

export function adminClaimList(invoiceId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/list', getParams));
}

export function adminClaimSave(invoiceId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/save', getParams));
}

export function adminClaimView(invoiceId,claimId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/claims/get/'+claimId+'', getParams));
}

export function adminCounterAddValue(accountId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/json/counters/add-value', getParams));
}

export function adminCounterCreate(accountId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/json/counters/create', getParams));
}

export function adminCounterDelete(accountId,counterId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/json/counters/delete/'+counterId+'', getParams));
}

export function adminCounterHistoryList(counterId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/counters/json/'+counterId+'/history/list', getParams));
}

export function adminCounterList(accountId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/json/counters/list', getParams));
}

export function adminCounterSave(accountId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/accounts/view/'+accountId+'/json/counters/save', getParams));
}

export function adminCounterView(accountId,counterId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/accounts/view/'+accountId+'/json/counters/view/'+counterId+'', getParams));
}

export function adminErrorLogsDetails(filename,index, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/error-logs/'+filename+'/details/'+index+'', getParams));
}

export function adminErrorLogsIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/error-logs', getParams));
}

export function adminErrorLogsShow(filename, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/error-logs/'+filename+'', getParams));
}

export function adminIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin', getParams));
}

export function adminInvoiceCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/create', getParams));
}

export function adminInvoiceCreateRegularInvoices(periodId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/invoices/json/create-regular-invoices/'+periodId+'', getParams));
}

export function adminInvoiceDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/invoices/json/delete/'+id+'', getParams));
}

export function adminInvoiceExport(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/export', getParams));
}

export function adminInvoiceGet(id, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/get/'+id+'', getParams));
}

export function adminInvoiceGetAccountsCountWithoutRegular(periodId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/get-without-regular/'+periodId+'', getParams));
}

export function adminInvoiceIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices', getParams));
}

export function adminInvoiceList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/list', getParams));
}

export function adminInvoiceSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/invoices/json/save', getParams));
}

export function adminInvoiceView(id, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/view/'+id+'', getParams));
}

export function adminNewPaymentDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/invoices/payments/json/delete/'+id+'', getParams));
}

export function adminNewPaymentGetInvoices(accountId,periodId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/payments/json/get-invoices/'+accountId+'/'+periodId+'', getParams));
}

export function adminNewPaymentIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/payments', getParams));
}

export function adminNewPaymentList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/payments/json/list', getParams));
}

export function adminNewPaymentSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/invoices/payments/json/save', getParams));
}

export function adminNewPaymentView(paymentId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/payments/json/get/'+paymentId+'', getParams));
}

export function adminOptionsIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/options', getParams));
}

export function adminOptionsList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/options/json/list', getParams));
}

export function adminOptionsSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/options/json/save', getParams));
}

export function adminPaymentAutoCreate(invoiceId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/auto-create', getParams));
}

export function adminPaymentCreate(invoiceId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/create', getParams));
}

export function adminPaymentDelete(invoiceId,id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/delete/'+id+'', getParams));
}

export function adminPaymentList(invoiceId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/list', getParams));
}

export function adminPaymentSave(invoiceId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/save', getParams));
}

export function adminPaymentView(invoiceId,paymentId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/invoices/json/'+invoiceId+'/payments/get/'+paymentId+'', getParams));
}

export function adminPeriodCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/periods/json/create', getParams));
}

export function adminPeriodDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/periods/json/'+id+'', getParams));
}

export function adminPeriodIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/periods', getParams));
}

export function adminPeriodList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/periods/json/list', getParams));
}

export function adminPeriodSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/periods/json/save', getParams));
}

export function adminQueue(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/queue', getParams));
}

export function adminQueueClear(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/queue/clear', getParams));
}

export function adminQueueStart(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/queue/start', getParams));
}

export function adminQueueStatus(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/queue/status', getParams));
}

export function adminQueueStop(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/queue/stop', getParams));
}

export function adminRequestsCounterHistoryConfirm(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/counter-history/json/confirm', getParams));
}

export function adminRequestsCounterHistoryConfirmDelete(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/counter-history/json/confirm-delete', getParams));
}

export function adminRequestsCounterHistoryCreateClaim(historyId, getParams = {}) {            
    return window.axios.post(makeQuery('/admin/counter-history/json/create-claim/'+historyId+'', getParams));
}

export function adminRequestsCounterHistoryDelete(historyId, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/counter-history/json/delete/'+historyId+'', getParams));
}

export function adminRequestsCounterHistoryIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/counter-history', getParams));
}

export function adminRequestsCounterHistoryLink(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/counter-history/json/link', getParams));
}

export function adminRequestsCounterHistoryList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/counter-history/json/list', getParams));
}

export function adminRoleCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/roles/json/create', getParams));
}

export function adminRoleDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/roles/json/'+id+'', getParams));
}

export function adminRoleIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/roles', getParams));
}

export function adminRoleList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/roles/json/list', getParams));
}

export function adminRoleSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/roles/json/save', getParams));
}

export function adminSelectsAccounts(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/json/selects/accounts', getParams));
}

export function adminSelectsCounters(accountId, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/json/selects/counters/'+accountId+'', getParams));
}

export function adminServiceCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/services/json/create', getParams));
}

export function adminServiceDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/services/json/'+id+'', getParams));
}

export function adminServiceIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/services', getParams));
}

export function adminServiceList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/services/json/list', getParams));
}

export function adminServiceSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/services/json/save', getParams));
}

export function adminTopPanelIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/json/top-panel', getParams));
}

export function adminTopPanelSearch(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/json/top-panel', getParams));
}

export function adminUserDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/admin/users/json/'+id+'', getParams));
}

export function adminUserExport(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/users/export', getParams));
}

export function adminUserGenerateEmail(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/users/json/generate-email', getParams));
}

export function adminUserIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/users', getParams));
}

export function adminUserList(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/users/json/list', getParams));
}

export function adminUserSave(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/users/json/save', getParams));
}

export function adminUserSendInviteWithPassword(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/users/json/send-invite-password', getParams));
}

export function adminUserSendRestorePassword(getParams = {}) {            
    return window.axios.post(makeQuery('/admin/users/json/sendRestorePassword', getParams));
}

export function adminUserView(id, getParams = {}) {            
    return window.axios.get(makeQuery('/admin/users/view/'+id+'', getParams));
}

export function announcementsIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/announcements', getParams));
}

export function announcementsList(getParams = {}) {            
    return window.axios.get(makeQuery('/announcements/json/list', getParams));
}

export function announcementsShow(id, getParams = {}) {            
    return window.axios.get(makeQuery('/announcements/'+id+'', getParams));
}

export function commonSummary(getParams = {}) {            
    return window.axios.get(makeQuery('/json/summary', getParams));
}

export function commonSummaryDetailing(type, getParams = {}) {            
    return window.axios.get(makeQuery('/json/summary/'+type+'', getParams));
}

export function contacts(getParams = {}) {            
    return window.axios.get(makeQuery('/contacts', getParams));
}

export function cookieAgreement(getParams = {}) {            
    return window.axios.post(makeQuery('/cookie-agreement', getParams));
}

export function counter(getParams = {}) {            
    return window.axios.get(makeQuery('/contacts/requests/counter', getParams));
}

export function counterCreate(getParams = {}) {            
    return window.axios.post(makeQuery('/contacts/requests/counter', getParams));
}

export function filesDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/files/json/delete/'+id+'', getParams));
}

export function filesDown(id, getParams = {}) {            
    return window.axios.post(makeQuery('/files/json/down/'+id+'', getParams));
}

export function filesEdit(id, getParams = {}) {            
    return window.axios.get(makeQuery('/files/json/edit/'+id+'', getParams));
}

export function filesIndex(folder, getParams = {}) {            
    return window.axios.get(makeQuery('/files/'+folder+'', getParams));
}

export function filesList(getParams = {}) {            
    return window.axios.get(makeQuery('/files/json/list', getParams));
}

export function filesMove(getParams = {}) {            
    return window.axios.post(makeQuery('/files/json/move', getParams));
}

export function filesReplace(getParams = {}) {            
    return window.axios.post(makeQuery('/files/json/replace', getParams));
}

export function filesSave(getParams = {}) {            
    return window.axios.post(makeQuery('/files/json/save', getParams));
}

export function filesStore(getParams = {}) {            
    return window.axios.post(makeQuery('/files/json/store', getParams));
}

export function filesUp(id, getParams = {}) {            
    return window.axios.post(makeQuery('/files/json/up/'+id+'', getParams));
}

export function foldersDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/folders/json/delete/'+id+'', getParams));
}

export function foldersList(getParams = {}) {            
    return window.axios.get(makeQuery('/folders/json/list', getParams));
}

export function foldersSave(getParams = {}) {            
    return window.axios.post(makeQuery('/folders/json/save', getParams));
}

export function foldersShow(id, getParams = {}) {            
    return window.axios.get(makeQuery('/folders/json/show/'+id+'', getParams));
}

export function garbage(getParams = {}) {            
    return window.axios.get(makeQuery('/garbage', getParams));
}

export function home(getParams = {}) {            
    return window.axios.get(makeQuery('/home', getParams));
}

export function index(getParams = {}) {            
    return window.axios.get(makeQuery('/', getParams));
}

export function infraHistoryChanges(getParams = {}) {            
    return window.axios.get(makeQuery('/admin/history/changes', getParams));
}

export function login(getParams = {}) {            
    return window.axios.post(makeQuery('/login', getParams));
}

export function logout(getParams = {}) {            
    return window.axios.get(makeQuery('/logout', getParams));
}

export function newsCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/news/json/create', getParams));
}

export function newsDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/news/json/delete/'+id+'', getParams));
}

export function newsEdit(id, getParams = {}) {            
    return window.axios.get(makeQuery('/news/json/edit/'+id+'', getParams));
}

export function newsFileDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/news/json/file/delete/'+id+'', getParams));
}

export function newsFileSave(getParams = {}) {            
    return window.axios.post(makeQuery('/news/json/file/save', getParams));
}

export function newsFileUpload(id, getParams = {}) {            
    return window.axios.post(makeQuery('/news/json/file/upload/'+id+'', getParams));
}

export function newsIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/news', getParams));
}

export function newsList(getParams = {}) {            
    return window.axios.get(makeQuery('/news/json/list', getParams));
}

export function newsListAll(getParams = {}) {            
    return window.axios.get(makeQuery('/news/json/list/all', getParams));
}

export function newsListLocked(getParams = {}) {            
    return window.axios.get(makeQuery('/news/json/list/locked', getParams));
}

export function newsSave(getParams = {}) {            
    return window.axios.post(makeQuery('/news/json/save', getParams));
}

export function newsShow(id, getParams = {}) {            
    return window.axios.get(makeQuery('/news/'+id+'', getParams));
}

export function passwordConfirm(getParams = {}) {            
    return window.axios.get(makeQuery('/password/confirm', getParams));
}

export function passwordEmail(getParams = {}) {            
    return window.axios.post(makeQuery('/password/email', getParams));
}

export function passwordRequest(getParams = {}) {            
    return window.axios.get(makeQuery('/password/reset', getParams));
}

export function passwordReset(token, getParams = {}) {            
    return window.axios.get(makeQuery('/password/reset/'+token+'', getParams));
}

export function passwordSave(getParams = {}) {            
    return window.axios.post(makeQuery('/password/set', getParams));
}

export function passwordSet(getParams = {}) {            
    return window.axios.get(makeQuery('/password/set', getParams));
}

export function passwordUpdate(getParams = {}) {            
    return window.axios.post(makeQuery('/password/reset', getParams));
}

export function payment(getParams = {}) {            
    return window.axios.get(makeQuery('/contacts/requests/payment', getParams));
}

export function paymentCreate(getParams = {}) {            
    return window.axios.post(makeQuery('/contacts/requests/payment', getParams));
}

export function privacy(getParams = {}) {            
    return window.axios.get(makeQuery('/privacy', getParams));
}

export function profileCounterAddValue(getParams = {}) {            
    return window.axios.post(makeQuery('/home/counters/json/add-value', getParams));
}

export function profileCounterCreate(getParams = {}) {            
    return window.axios.post(makeQuery('/home/counters/json/create', getParams));
}

export function profileCounterHistoryList(getParams = {}) {            
    return window.axios.post(makeQuery('/home/counters/json/history', getParams));
}

export function profileCounterList(getParams = {}) {            
    return window.axios.get(makeQuery('/home/counters/json/list', getParams));
}

export function profileCountersIncrementSave(getParams = {}) {            
    return window.axios.post(makeQuery('/home/counters/json/increment', getParams));
}

export function profileCountersIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/home/counters', getParams));
}

export function profileCountersView(counter, getParams = {}) {            
    return window.axios.get(makeQuery('/home/counters/'+counter+'', getParams));
}

export function profileSavePassword(getParams = {}) {            
    return window.axios.post(makeQuery('/home/profile/password', getParams));
}

export function proposal(getParams = {}) {            
    return window.axios.get(makeQuery('/contacts/requests/proposal', getParams));
}

export function proposalCreate(getParams = {}) {            
    return window.axios.post(makeQuery('/contacts/requests/proposal', getParams));
}

export function regulation(getParams = {}) {            
    return window.axios.get(makeQuery('/regulation', getParams));
}

export function reportsCreate(getParams = {}) {            
    return window.axios.get(makeQuery('/reports/json/create', getParams));
}

export function reportsDelete(id, getParams = {}) {            
    return window.axios.delete(makeQuery('/reports/json/delete/'+id+'', getParams));
}

export function reportsEdit(id, getParams = {}) {            
    return window.axios.get(makeQuery('/reports/json/edit/'+id+'', getParams));
}

export function reportsFileDelete(id, getParams = {}) {            
    return window.axios.post(makeQuery('/reports/json/file/delete/'+id+'', getParams));
}

export function reportsFileUpload(id, getParams = {}) {            
    return window.axios.post(makeQuery('/reports/json/file/upload/'+id+'', getParams));
}

export function reportsIndex(getParams = {}) {            
    return window.axios.get(makeQuery('/reports', getParams));
}

export function reportsList(getParams = {}) {            
    return window.axios.get(makeQuery('/reports/json/list', getParams));
}

export function reportsSave(getParams = {}) {            
    return window.axios.post(makeQuery('/reports/json/save', getParams));
}

export function requests(getParams = {}) {            
    return window.axios.get(makeQuery('/contacts/requests', getParams));
}

export function sanctumCsrfCookie(getParams = {}) {            
    return window.axios.get(makeQuery('/sanctum/csrf-cookie', getParams));
}

export function search(getParams = {}) {            
    return window.axios.get(makeQuery('/search', getParams));
}

export function searchSite(getParams = {}) {            
    return window.axios.post(makeQuery('/search/json/search', getParams));
}

export function sessionStore(getParams = {}) {            
    return window.axios.post(makeQuery('/session', getParams));
}

export function templateGet(getParams = {}) {            
    return window.axios.post(makeQuery('/pages/json/edit', getParams));
}

export function templateUpdate(getParams = {}) {            
    return window.axios.patch(makeQuery('/pages/json/edit', getParams));
}

export function verificationNotice(getParams = {}) {            
    return window.axios.get(makeQuery('/email/verify', getParams));
}

export function verificationResend(getParams = {}) {            
    return window.axios.post(makeQuery('/email/resend', getParams));
}

export function verificationVerify(id,hash, getParams = {}) {            
    return window.axios.get(makeQuery('/email/verify/'+id+'/'+hash+'', getParams));
}