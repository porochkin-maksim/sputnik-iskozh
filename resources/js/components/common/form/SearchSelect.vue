<template>
    <div ref="root"
         class="dropdown-search">
        <element-wrapper
            :label="label"
            :required="required"
            :classes="classes"
            :id="inputId"
            :floating="true"
        >
            <input
                :id="inputId"
                ref="inputRef"
                type="text"
                :class="inputClass"
                :placeholder="placeholder"
                :disabled="disabled"
                :value="inputDisplay"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                @keydown="onKeydown"
                v-bind="$attrs"
            />
            <span
                v-if="showClear"
                class="clear-icon"
                :class="label ? 'with-label' : ''"
                @click.stop="clearSelection"
            >
                    &times;
                </span>
        </element-wrapper>

        <errors-list v-if="error"
                     :errors="error" />

        <ul
            v-if="isOpen && filteredItems.length"
            ref="listRef"
            class="dropdown-menu show shadow-sm"
            @mousedown.prevent
        >
            <li
                v-for="(item, index) in filteredItems"
                :key="item.key"
                :class="{ active: activeIndex === index }"
                @click.stop="selectItem(item)"
                @mouseenter="activeIndex = index"
            >
                <input
                    v-if="multiple"
                    type="checkbox"
                    class="form-check-input me-1"
                    :checked="isSelected(item)"
                    @click.stop="selectItem(item)"
                />
                {{ item.value }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    nextTick,
    onMounted,
    onBeforeUnmount,
}                     from 'vue';
import { useId }      from 'vue';
import ElementWrapper from './partial/ElementWrapper.vue';
import ErrorsList     from './partial/ErrorsList.vue';

const props = defineProps({
    items      : {
        type    : Array,
        required: true,
    },
    modelValue : {
        type   : [String, Number, Array, null],
        default: null,
    },
    multiple   : {
        type   : Boolean,
        default: false,
    },
    placeholder: {
        type   : String,
        default: 'Поиск...',
    },
    label      : String,
    required   : Boolean,
    disabled   : Boolean,
    inputClass : {
        type   : String,
        default: 'form-control',
    },
    error      : [String, Array],
    classes    : String,
});

const emit = defineEmits(['update:modelValue', 'select']);

const inputId  = `search-select-${useId()}`;
const root     = ref(null);
const inputRef = ref(null);
const listRef  = ref(null);

const isOpen      = ref(false);
const searchQuery = ref('');
const activeIndex = ref(-1);

// Нормализуем items: приводим к формату { key, value }
const normalizedItems = computed(() => {
    return props.items.map(item => {
        // Объект с полями value и label
        if (typeof item === 'object' && item !== null && 'value' in item && 'label' in item) {
            return {
                key  : item.value,
                value: item.label,
            };
        }
        // Объект только с value
        else if (typeof item === 'object' && item !== null && 'value' in item) {
            return {
                key  : item.value,
                value: String(item.value),
            };
        }
        // Примитив
        else {
            return {
                key  : item,
                value: String(item),
            };
        }
    });
});

const compareKeys = (a, b) => String(a) === String(b);

const selectedKeys = computed(() => {
    if (props.multiple) {
        return Array.isArray(props.modelValue) ? props.modelValue : [];
    }
    else {
        return props.modelValue !== null && props.modelValue !== undefined ? [props.modelValue] : [];
    }
});

const isSelected = (item) => {
    return selectedKeys.value.some(key => compareKeys(key, item.key));
};

const filteredItems = computed(() => {
    const query  = searchQuery.value.toLowerCase();
    let filtered = normalizedItems.value.filter(item =>
        item.value.toLowerCase().includes(query),
    );

    if (selectedKeys.value.length) {
        filtered = filtered.slice().sort((a, b) => {
            const aSel = isSelected(a);
            const bSel = isSelected(b);
            if (aSel === bSel) {
                return 0;
            }
            return aSel ? -1 : 1;
        });
    }
    return filtered;
});

const inputDisplay = computed(() => {
    if (isOpen.value) {
        return searchQuery.value;
    }
    else {
        if (!selectedKeys.value.length) {
            return '';
        }
        if (props.multiple) {
            const selectedItems = normalizedItems.value.filter(item => isSelected(item));
            return selectedItems.map(i => i.value).join(', ');
        }
        else {
            const selected = normalizedItems.value.find(item => compareKeys(item.key, selectedKeys.value[0]));
            return selected ? selected.value : '';
        }
    }
});

const showClear = computed(() => {
    if (props.disabled) {
        return false;
    }
    return selectedKeys.value.length > 0 && !(isOpen.value && !props.multiple);
});

const onInput = (e) => {
    searchQuery.value = e.target.value;
    if (!isOpen.value) {
        isOpen.value = true;
    }
    activeIndex.value = -1;
};

const onFocus = () => {
    if (props.disabled) {
        return;
    }
    isOpen.value      = true;
    searchQuery.value = '';
};

const onBlur = () => {
    setTimeout(() => {
        if (!root.value?.contains(document.activeElement)) {
            close();
        }
    }, 200);
};

const onKeydown = (e) => {
    if (!isOpen.value) {
        return;
    }
    const items = filteredItems.value;
    if (!items.length) {
        return;
    }

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            activeIndex.value = (activeIndex.value + 1) % items.length;
            scrollToActive();
            break;
        case 'ArrowUp':
            e.preventDefault();
            activeIndex.value = (activeIndex.value - 1 + items.length) % items.length;
            scrollToActive();
            break;
        case 'Enter':
            e.preventDefault();
            if (activeIndex.value >= 0) {
                selectItem(items[activeIndex.value]);
            }
            break;
        case 'Escape':
            e.preventDefault();
            close();
            break;
    }
};

const scrollToActive = () => {
    nextTick(() => {
        if (activeIndex.value >= 0 && listRef.value) {
            const activeLi = listRef.value.children[activeIndex.value];
            activeLi?.scrollIntoView({ block: 'nearest' });
        }
    });
};

const selectItem = (item) => {
    if (props.multiple) {
        const newValue = [...selectedKeys.value];
        const idx      = newValue.findIndex(key => compareKeys(key, item.key));
        if (idx > -1) {
            newValue.splice(idx, 1);
        }
        else {
            newValue.push(item.key);
        }
        emit('update:modelValue', newValue);
        emit('select', item);
        inputRef.value?.focus();
    }
    else {
        emit('update:modelValue', item.key);
        emit('select', item);
        close();
    }
    searchQuery.value = '';
    activeIndex.value = -1;
};

const clearSelection = () => {
    if (props.multiple) {
        emit('update:modelValue', []);
    }
    else {
        emit('update:modelValue', null);
    }
    searchQuery.value = '';
    activeIndex.value = -1;
    inputRef.value?.focus();
};

const close = () => {
    isOpen.value      = false;
    searchQuery.value = '';
    activeIndex.value = -1;
};

const handleClickOutside = (event) => {
    if (root.value && !root.value.contains(event.target)) {
        close();
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});

defineOptions({ inheritAttrs: false });
</script>

<style scoped>
.dropdown-search {
    position : relative;
    width    : 100%;
}

.clear-icon {
    position  : absolute;
    top       : 50%;
    transform : translateY(-50%);
    right     : 10px;
    font-size : 20px;
    color     : #aaa;
    cursor    : pointer;
    z-index   : 5;
}

.clear-icon.with-label {
    top : calc(50% + 6px);
}

.dropdown-menu {
    position      : absolute;
    top           : 100%;
    left          : 0;
    right         : 0;
    z-index       : 1000;
    padding       : 0;
    margin-top    : 2px;
    max-height    : 10rem;
    overflow-y    : auto;
    border        : 1px solid rgba(0, 0, 0, .15);
    border-radius : 0.25rem;
    background    : #fff;
    list-style    : none;
}

.dropdown-menu li {
    padding       : 0.25rem 0.75rem;
    cursor        : pointer;
    border-bottom : 1px solid #e0e0e0;
    white-space   : nowrap;
    overflow      : hidden;
    text-overflow : ellipsis;
}

.dropdown-menu li:last-child {
    border-bottom : none;
}

.dropdown-menu li:hover,
.dropdown-menu li.active {
    background-color : #f0f0f0;
}
</style>