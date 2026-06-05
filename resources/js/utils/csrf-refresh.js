import apiClient from '@api/client';

const CSRF_REFRESH_FLAG = '__csrfRefreshAttempted';

export async function refreshCsrfCookie () {
    return apiClient.get('/sanctum/csrf-cookie');
}

export function installCsrfRefreshInterceptor () {
    apiClient.interceptors.response.use(
        response => response,
        async error => {
            const status = error?.response?.status;
            const config = error?.config;

            if (status !== 419 || !config || config[CSRF_REFRESH_FLAG]) {
                return Promise.reject(error);
            }

            config[CSRF_REFRESH_FLAG] = true;

            try {
                await refreshCsrfCookie();
                return apiClient.request(config);
            }
            catch (refreshError) {
                return Promise.reject(refreshError);
            }
        },
    );
}
