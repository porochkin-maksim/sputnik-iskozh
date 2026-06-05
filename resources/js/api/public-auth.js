import apiClient from './client.js';

export function ApiRegister (postData = null, getParams = {}) {
    return apiClient.post('/register', postData, { params: getParams });
}
