<template>
    <div class="form-control">
        <div class="form-check form-switch" :class="classes">
            <input class="form-check-input"
                   type="checkbox"
                   :id="id"
                   v-model="localValue"
                   :checked="localValue"
                   :disabled="disabled"
                   :name="name"
                   @change="onChange"
            >
            <label class="form-check-label label-checkbox" :for="id">{{ label }}</label>
        </div>
    </div>
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

export default {
    components: {
    },
    props     : [
        'classes',
        'modelValue',
        'errors',
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

            id: null,
        };
    },
    mounted () {
        this.id = 'checkbox-' + this.$_uid;
    },
    created () {
        this.localValue = this.modelValue;
    },
    methods : {
        onChange (event) {
            this.$emit('update:modelValue', this.localValue);
            this.$emit('change', event);
        },
    },
    computed: {
        isInvalid () {
            return this.errors && this.errors.length;
        },
    },
};
</script>
