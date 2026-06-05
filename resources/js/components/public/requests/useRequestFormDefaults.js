import { computed } from 'vue';

export function useRequestFormDefaults (props) {
    const storedRequestValue = (key) => {
        const value = localStorage.getItem(key);
        return value === 'null' ? '' : value ?? '';
    };

    const propUserName = computed(() => {
        if (!props.propUser?.email) {
            return null;
        }

        return (
            props.propUser?.lastName
            + ' '
            + props.propUser?.firstName
            + ' '
            + props.propUser?.middleName
        ).replace('null', '');
    });

    const resolveContactValue = (propValue, storageKey) => propValue ?? storedRequestValue(storageKey);

    return {
        propUserName,
        storedRequestValue,
        resolveContactValue,
    };
}
