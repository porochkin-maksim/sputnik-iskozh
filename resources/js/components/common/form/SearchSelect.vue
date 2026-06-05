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
                :class="[inputClass, { 'is-invalid': resolvedError }]"
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

        <errors-list v-if="resolvedError"
                     :errors="resolvedError" />

        <search-select-dropdown
            :active-index="activeIndex"
            :is-open="isOpen"
            :is-selected="isSelected"
            :items="filteredItems"
            :list-ref="listRef"
            :multiple="multiple"
            @hover="activeIndex = $event"
            @select="selectItem"
        />
    </div>
</template>

<script setup>
import { ref }                    from 'vue';
import ElementWrapper             from '@common/form/partial/ElementWrapper.vue';
import ErrorsList                 from '@common/form/partial/ErrorsList.vue';
import SearchSelectDropdown       from '@common/form/search-select/SearchSelectDropdown.vue';
import { useSearchSelectOptions } from '@common/form/search-select/useSearchSelectOptions';
import { useSearchSelectState }   from '@common/form/search-select/useSearchSelectState';
import { useFormFieldId }         from '@common/form/useFormFieldId';
import { useFieldError }          from '@common/form/useFieldError';

const props = defineProps({
    items      : {
        type   : Array,
        default: () => [],
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

const inputId     = useFormFieldId('search-select');
const root        = ref(null);
const inputRef    = ref(null);
const listRef     = ref(null);
const isOpen      = ref(false);
const searchQuery = ref('');
const activeIndex = ref(-1);

const options           = useSearchSelectOptions(props, isOpen, searchQuery);
const { resolvedError } = useFieldError(props);

const {
          filteredItems,
          inputDisplay,
          isSelected,
          showClear,
      } = options;

const {
          clearSelection,
          onBlur,
          onFocus,
          onInput,
          onKeydown,
          selectItem,
      } = useSearchSelectState(props, emit, options, {
    inputRef,
    listRef,
    root,
}, {
    activeIndex,
    isOpen,
    searchQuery,
});

defineOptions({ inheritAttrs: false });
</script>
