// resources/js/utils/api/helpers.js

export function hasFiles(obj) {
    if (!obj || typeof obj !== 'object') return false;
    return Object.values(obj).some(value => {
        if (value instanceof File || value instanceof Blob) return true;
        if (Array.isArray(value)) return value.some(v => v instanceof File || v instanceof Blob);
        if (value && typeof value === 'object') return hasFiles(value);
        return false;
    });
}

export function objectToFormData(obj, formData = new FormData(), parentKey = null) {
    for (const key in obj) {
        if (!Object.prototype.hasOwnProperty.call(obj, key)) continue;
        const value = obj[key];
        const propName = parentKey ? `${parentKey}[${key}]` : key;
        if (value && typeof value === 'object' && !(value instanceof File) && !(value instanceof Blob) && !Array.isArray(value)) {
            objectToFormData(value, formData, propName);
        } else if (Array.isArray(value)) {
            value.forEach((item, index) => {
                const arrayKey = `${propName}[${index}]`;
                if (item && typeof item === 'object' && !(item instanceof File) && !(item instanceof Blob)) {
                    objectToFormData(item, formData, arrayKey);
                } else {
                    formData.append(arrayKey, item);
                }
            });
        } else {
            formData.append(propName, value);
        }
    }
    return formData;
}

export function prepareRequestData(data) {
    if (!data) return data;
    if (data instanceof FormData) return data;
    if (hasFiles(data)) return objectToFormData(data);
    return data;
}

export function makeQuery(uri, getParams = {}) {
    let getQuery = [];
    Object.keys(getParams).forEach(key => {
        if (getParams[key]) {
            if (Array.isArray(getParams[key])) {
                getParams[key].forEach((value, index) => {
                    getQuery = getQuery.concat([key + '[]=' + value]);
                })
            } else if (String(getParams[key]) !== '0') {
                getQuery = getQuery.concat([key + '=' + getParams[key]]);
            }
        }
    });
    if (getQuery.length) {
        uri = uri + '?' + getQuery.join('&');
    }
    return uri;
}