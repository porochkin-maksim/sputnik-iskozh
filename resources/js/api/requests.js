import {
    ApiAdminSelectsAccounts,
    ApiAdminSelectsCounters,
    ApiAdminSelectsPeriods,
} from '@api';

export function fetchPeriods (activeOnly = false) {
    return ApiAdminSelectsPeriods({
        active: activeOnly,
    });
}

export function fetchAccounts () {
    return ApiAdminSelectsAccounts();
}

export function fetchCounters (accountId = null) {
    return ApiAdminSelectsCounters(accountId);
}