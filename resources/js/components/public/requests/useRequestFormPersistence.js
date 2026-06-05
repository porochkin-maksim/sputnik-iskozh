import { watch } from 'vue';

export function useRequestFormPersistence (fieldMap) {
    Object.entries(fieldMap).forEach(([storageKey, source]) => {
        watch(source, value => {
            localStorage.setItem(storageKey, value);
        });
    });
}
