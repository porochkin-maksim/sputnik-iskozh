import { computed } from 'vue';

export function useSearchSelectOptions (props, isOpen, searchQuery) {
    const compareKeys = (firstKey, secondKey) => String(firstKey) === String(secondKey);

    const normalizedItems = computed(() => {
        const items = Array.isArray(props.items) ? props.items : [];

        return items.map(item => {
            if (typeof item === 'object' && item !== null && 'value' in item && 'label' in item) {
                return {
                    key  : item.value,
                    value: item.label,
                };
            }

            if (typeof item === 'object' && item !== null && 'value' in item) {
                return {
                    key  : item.value,
                    value: String(item.value),
                };
            }

            return {
                key  : item,
                value: String(item),
            };
        });
    });

    const selectedKeys = computed(() => {
        if (props.multiple) {
            return Array.isArray(props.modelValue) ? props.modelValue : [];
        }

        return props.modelValue !== null && props.modelValue !== undefined ? [props.modelValue] : [];
    });

    const isSelected = (item) => {
        return selectedKeys.value.some(key => compareKeys(key, item.key));
    };

    const filteredItems = computed(() => {
        const query  = searchQuery.value.toLowerCase();
        let filtered = normalizedItems.value.filter(item => (item.value ?? '').toString().toLowerCase().includes(query));

        if (selectedKeys.value.length) {
            filtered = filtered.slice().sort((firstItem, secondItem) => {
                const firstSelected  = isSelected(firstItem);
                const secondSelected = isSelected(secondItem);

                if (firstSelected === secondSelected) {
                    return 0;
                }

                return firstSelected ? -1 : 1;
            });
        }

        return filtered;
    });

    const inputDisplay = computed(() => {
        if (isOpen.value) {
            return searchQuery.value;
        }

        if (!selectedKeys.value.length) {
            return '';
        }

        if (props.multiple) {
            const selectedItems = normalizedItems.value.filter(item => isSelected(item));

            return selectedItems.map(item => item.value).join(', ');
        }

        const selectedItem = normalizedItems.value.find(item => compareKeys(item.key, selectedKeys.value[0]));

        return selectedItem ? selectedItem.value : '';
    });

    const showClear = computed(() => {
        if (props.disabled) {
            return false;
        }

        return selectedKeys.value.length > 0 && !(isOpen.value && !props.multiple);
    });

    return {
        compareKeys,
        filteredItems,
        inputDisplay,
        isSelected,
        selectedKeys,
        showClear,
    };
}
