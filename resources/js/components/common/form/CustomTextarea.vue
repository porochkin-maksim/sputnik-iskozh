<template>
    <element-wrapper
        :label="label"
        :required="required"
        :classes="classes"
        :id="id"
    >
        <textarea
            :value="modelValue"
            :type="computedType"
            :required="required"
            :placeholder="placeholder"
            :disabled="disabled"
            :name="name"
            @change="onChange"
            @keyup="onKeyup"
            class="form-control"
            :style="{minHeight: (height ? height : 200) + 'px'}"
            :class="[errors ? 'form-error' : '']"
        ></textarea>
    </element-wrapper>
    <errors-list :errors="errors" />
</template>

<script>
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

export default {
    components: {
        ElementWrapper,
        ErrorsList,
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
        'height',
    ],
    emits     : [
        'update:modelValue',
        'change',
        'submit',
        'keyup',
    ],
    mounted () {
        this.id = 'textarea-' + this.$_uid;
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
