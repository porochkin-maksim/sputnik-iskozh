<template>
    <element-wrapper
        :label="label"
        :required="required"
        :classes="classes"
        :id="id"
    >
        <input
            :value="modelValue"
            :type="computedType"
            :required="required"
            :placeholder="placeholder"
            :disabled="disabled"
            :min="min"
            :step="step"
            :name="name"
            :id="id"
            @change="onChange"
            @keyup.enter="onSubmit"
            @keyup="onKeyup"
            class="form-control"
            :class="[errors ? 'form-error' : '']"
            @focusout="$emit('focusout')"
        />
    </element-wrapper>
    <errors-list :errors="errors" />
</template>

<script>
/**
 * @see https://primevue.org/
 */
import Calendar       from 'primevue/calendar';
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

export default {
    components: {
        ElementWrapper,
        ErrorsList,
        Calendar,
    },
    props     : [
        'classes',
        'modelValue',
        'errors',
        'type',
        'label',
        'name',
        'min',
        'step',
        'placeholder',
        'required',
        'disabled',
    ],
    emits     : [
        'update:modelValue',
        'change',
        'submit',
        'keyup',
        'focusout',
    ],
    mounted () {
        this.id = 'input-' + this.$_uid;
    },
    data () {
        return {
            id        : null,
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
