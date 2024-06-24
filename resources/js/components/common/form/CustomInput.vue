<template>
    <label class="form-control label-input" :class="classes">
        <span class="label-text">
            {{ label }}
        </span>
        <input
               :value="modelValue"
               :type="computedType"
               :required="required"
               :placeholder="placeholder"
               :disabled="disabled"
               :name="name"
               @change="onChange"
               @keyup.enter="onSubmit"
               @keyup="onKeyup"
               class="form-control"
               :class="[errors ? 'form-error' : '']"
        />
    </label>
    <ul v-if="errors"
        class="form-error"
    >
        <li v-for="error in errors">
            {{ error }}
        </li>
    </ul>
</template>

<script>
/**
 * @see https://primevue.org/
 */
import Calendar from 'primevue/calendar';

export default {
    components: {
        Calendar,
    },
    props     : [
        'classes',
        'modelValue',
        'errors',
        'type',
        'label',
        'name',
        'placeholder',
        'required',
        'disabled',
    ],
    emits     : [
        'update:modelValue',
        'change',
        'submit',
        'keyup',
    ],
    data () {
        return {
            localValue: null,
        };
    },
    methods : {
        onChange (event) {
            if (this.type === 'file') {
                this.$emit('update:modelValue', event.target.files[0]);
            }
            else {
                this.$emit('update:modelValue', event.target.value);
            }
            this.$emit('change', event);
        },
        onSubmit (event) {
            this.onChange(event);
            this.$emit('submit', event);
        },
        onKeyup (event) {
            this.onChange(event);
            this.$emit('keyup', event);
        },
        usePrimeVue () {
            switch (this.computedType) {
                case 'datetime-local':
                    return true;
                default:
                    return false;
            }
        },
    },
    computed: {
        computedType () {
            if (this.type === 'money') {
                return 'number';
            }

            return this.type;
        },
    },
};
</script>
