<template>
    <div>
        <div :class="['form-check', classes, { 'form-switch': switchStyle }]">
            <input
                :id="checkboxId"
                class="form-check-input"
                :class="{ 'is-invalid': resolvedErrors }"
                type="checkbox"
                :checked="modelValue"
                :disabled="disabled"
                :name="name"
                :required="required"
                v-bind="$attrs"
                @change="onChange"
            >
            <label
                v-if="label"
                :for="checkboxId"
                class="form-check-label label-checkbox"
            >
                {{ label }}
            </label>
        </div>
        <errors-list v-if="resolvedErrors" :errors="resolvedErrors" />
    </div>
</template>

<script setup>
import ErrorsList         from '@common/form/partial/ErrorsList.vue';
import { useFormFieldId } from '@common/form/useFormFieldId';
import { useFieldError }  from '@common/form/useFieldError';

const props = defineProps({
    modelValue : Boolean, // для чекбокса значение - булево
    errors     : [String, Array],
    label      : String,
    name       : String,
    disabled   : Boolean,
    required   : Boolean,
    classes    : String,
    switchStyle: Boolean, // добавляем проп для переключения между обычным чекбоксом и switch
});

const emit = defineEmits(['update:modelValue', 'change']);

const checkboxId                                            = useFormFieldId('checkbox');
const { clearResolvedError, resolvedError: resolvedErrors } = useFieldError(props);

const onChange = (event) => {
    emit('update:modelValue', event.target.checked);
    emit('change', event);
    clearResolvedError();
};
</script>
