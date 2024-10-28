<template>
    <div class="form-control">
        <div class="form-check form-switch"
             :class="classes">
            <input class="form-check-input"
                   type="checkbox"
                   :id="id"
                   v-model="localValue"
                   :checked="localValue"
                   :disabled="disabled"
                   :name="name"
                   @change="onChange"
            >
            <label class="form-check-label label-checkbox"
                   :for="id">{{ label }}</label>
        </div>
    </div>
    <errors-list :errors="errors" />
</template>

<script>
import ErrorsList from './partial/ErrorsList.vue';

export default {
    components: {
        ErrorsList,
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
            id        : null,
        };
    },
    mounted () {
        this.id         = 'checkbox-' + this.$_uid;
        this.localValue = this.modelValue;
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
    watch: {
        modelValue: function () {
            this.localValue = this.modelValue;
        },
    },
    computed: {
        isInvalid () {
            return this.errors && this.errors.length;
        },
    },
};
</script>
