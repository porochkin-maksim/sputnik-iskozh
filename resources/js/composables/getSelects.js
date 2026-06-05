import { useResponseError } from '@composables/useResponseError';

import {
    ApiAdminSelectsPeriods,
    ApiAdminSelectsAccounts,
    ApiAdminSelectsRoles,
    ApiAdminSelectsCounters,
    ApiAdminSelectsServicesTypes,
}                           from '@api';

export function getSelects () {
    const { parseResponseErrors } = useResponseError();

    const getPeriods = async () => {
        let result = [];
        try {
            const response = await ApiAdminSelectsPeriods();
            result         = response.data;
        }
        catch (error) {
            parseResponseErrors(error);
        }

        return result;
    };

    const getAccounts = async () => {
        let result = [];
        try {
            const response = await ApiAdminSelectsAccounts();
            result         = response.data;
        }
        catch (error) {
            parseResponseErrors(error);
        }

        return result;
    };

    const getRoles = async () => {
        let result = [];
        try {
            const response = await ApiAdminSelectsRoles();
            result         = response.data;
        }
        catch (error) {
            parseResponseErrors(error);
        }

        return result;
    };

    const getServiceTypes = async () => {
        let result = [];
        try {
            const response = await ApiAdminSelectsServicesTypes();
            result         = response.data;
        }
        catch (error) {
            parseResponseErrors(error);
        }

        return result;
    };

    const getAccountCounters = async (accountId) => {
        let result = [];
        try {
            const response = await ApiAdminSelectsCounters(accountId);
            result         = response.data;
        }
        catch (error) {
            parseResponseErrors(error);
        }

        return result;
    };

    return {
        getPeriods,
        getAccounts,
        getRoles,
        getServiceTypes,
        getAccountCounters,
    };
}
