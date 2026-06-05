import { computed } from 'vue';

export function useRequestContactDefaults (user) {
    const storedRequestValue = (key) => {
        const value = localStorage.getItem(key);
        return value === 'null' ? '' : value ?? '';
    };

    const userName = computed(() => {
        if (!user?.email) {
            return null;
        }

        return (
            user?.lastName
            + ' '
            + user?.firstName
            + ' '
            + user?.middleName
        ).replace('null', '').trim();
    });

    return {
        userName,
        storedRequestValue,
    };
}
