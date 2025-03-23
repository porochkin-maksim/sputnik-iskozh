<template>
    <select
        :required="required"
        @change="onChange"
        class="form-select"
        :disabled="disabled"
        v-model="selectedOption"
    >
        <option v-if="label"
                :value="null"
                disabled
                class="disabled">{{ label }}:
        </option>
        <option
            v-for="item in items"
            :key="item.key !== undefined ? item.key : item"
            :value="item.key !== undefined ? item.key : item"
        >{{ item.value !== undefined ? item.value : item }}
        </option>
    </select>
</template>

<script>
export default {
    props: ['modelValue', 'required', 'label', 'disabled', 'items'],
    emits: ['change', 'update:modelValue'],
    data () {
        return {
            selectedOption: this.modelValue,
        };
    },
    watch  : {
        modelValue (newVal) {
            this.selectedOption = newVal;
        },
    },
    methods: {
        onChange (event) {
            const value = event.target.value;
            this.$emit('update:modelValue', value);
            this.$emit('change', value);
        },
    },
};
</script>
