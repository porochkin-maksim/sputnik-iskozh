import {
    nextTick,
    onBeforeUnmount,
    onMounted,
} from 'vue';

export function useSearchSelectState (props, emit, options, refs, state) {
    const close = () => {
        state.isOpen.value      = false;
        state.searchQuery.value = '';
        state.activeIndex.value = -1;
    };

    const scrollToActive = () => {
        nextTick(() => {
            if (state.activeIndex.value < 0 || !refs.listRef.value) {
                return;
            }

            const activeItem = refs.listRef.value.children[state.activeIndex.value];
            activeItem?.scrollIntoView({ block: 'nearest' });
        });
    };

    const selectItem = (item) => {
        if (props.multiple) {
            const newValue  = [...options.selectedKeys.value];
            const itemIndex = newValue.findIndex(key => options.compareKeys(key, item.key));

            if (itemIndex > -1) {
                newValue.splice(itemIndex, 1);
            }
            else {
                newValue.push(item.key);
            }

            emit('update:modelValue', newValue);
            emit('select', item);
            refs.inputRef.value?.focus();
        }
        else {
            emit('update:modelValue', item.key);
            emit('select', item);
            close();
        }

        state.searchQuery.value = '';
        state.activeIndex.value = -1;
    };

    const clearSelection = () => {
        emit('update:modelValue', props.multiple ? [] : null);
        state.searchQuery.value = '';
        state.activeIndex.value = -1;
        refs.inputRef.value?.focus();
    };

    const onInput = (event) => {
        state.searchQuery.value = event.target.value;

        if (!state.isOpen.value) {
            state.isOpen.value = true;
        }

        state.activeIndex.value = -1;
    };

    const onFocus = () => {
        if (props.disabled) {
            return;
        }

        state.isOpen.value      = true;
        state.searchQuery.value = '';
    };

    const onBlur = () => {
        setTimeout(() => {
            if (!refs.root.value?.contains(document.activeElement)) {
                close();
            }
        }, 200);
    };

    const onKeydown = (event) => {
        if (!state.isOpen.value) {
            return;
        }

        const items = options.filteredItems.value;

        if (!items.length) {
            return;
        }

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                state.activeIndex.value = (state.activeIndex.value + 1) % items.length;
                scrollToActive();
                break;
            case 'ArrowUp':
                event.preventDefault();
                state.activeIndex.value = (state.activeIndex.value - 1 + items.length) % items.length;
                scrollToActive();
                break;
            case 'Enter':
                event.preventDefault();

                if (state.activeIndex.value >= 0) {
                    selectItem(items[state.activeIndex.value]);
                }
                break;
            case 'Escape':
                event.preventDefault();
                close();
                break;
        }
    };

    const handleClickOutside = (event) => {
        if (refs.root.value && !refs.root.value.contains(event.target)) {
            close();
        }
    };

    onMounted(() => {
        document.addEventListener('mousedown', handleClickOutside);
    });

    onBeforeUnmount(() => {
        document.removeEventListener('mousedown', handleClickOutside);
    });

    return {
        clearSelection,
        onBlur,
        onFocus,
        onInput,
        onKeydown,
        selectItem,
    };
}
