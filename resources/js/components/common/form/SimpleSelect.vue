<template>
    <select
        :value="modelValue"
        @change="onChange"
        class="form-select"
        :disabled="disabled"
        v-bind="$attrs"
    >
        <option v-if="placeholder"
                :value="null"
                disabled>
            {{ placeholder }}
        </option>
        <option
            v-for="option in normalizedOptions"
            :key="option.value"
            :value="option.value"
        >
            {{ option.label }}
        </option>
    </select>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue : [String, Number, Boolean, null],
    disabled   : Boolean,
    options    : {
        type    : Array,
        required: true,
    },
    placeholder: String,
});

const emit = defineEmits(['update:modelValue', 'change']);

// Нормализуем опции: сохраняем исходное значение и его тип
const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object' && opt !== null) {
            return {
                value   : opt.value ?? opt,
                label   : opt.label ?? String(opt.value ?? opt),
                original: opt.value ?? opt,
            };
        }
        return {
            value   : opt,
            label   : String(opt),
            original: opt,
        };
    });
});

// Функция приведения строки из select к нужному типу
const coerceValue = (stringValue) => {
    // Если значение пустое (placeholder) — возвращаем null
    if (stringValue === '' || stringValue === 'null') {
        return null;
    }

    // Пытаемся определить ожидаемый тип по первому элементу options
    const firstOption = normalizedOptions.value[0]?.original;
    if (firstOption !== undefined) {
        if (typeof firstOption === 'number') {
            const num = Number(stringValue);
            return isNaN(num) ? stringValue : num;
        }
        if (typeof firstOption === 'boolean') {
            return stringValue === 'true';
        }
    }
    // Если не удалось определить, пробуем по исходному modelValue
    if (typeof props.modelValue === 'number') {
        const num = Number(stringValue);
        return isNaN(num) ? stringValue : num;
    }
    if (typeof props.modelValue === 'boolean') {
        return stringValue === 'true';
    }
    // По умолчанию возвращаем строку
    return stringValue;
};

const onChange = (event) => {
    const rawValue   = event.target.value;
    const typedValue = coerceValue(rawValue);
    emit('update:modelValue', typedValue);
    emit('change', typedValue);
};
</script>