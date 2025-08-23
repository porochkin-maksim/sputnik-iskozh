<template>
    <element-wrapper
        :label="label"
        :required="required"
        :classes="classes"
        :id="id"
    >
        <select :required="required"
                @change="onChange"
                @keyup="onChange"
                class="form-control"
                :class="[errors ? 'form-error' : '']"
        >
            <option v-for="item in items"
                    :key="item.key !== undefined ? item.key : item"
                    :value="item.key !== undefined ? item.key : item"
                    :selected="(item.key !== undefined ? String(item.key) : String(item)) === String(modelValue)"
            >
                {{ item.value !== undefined ? item.value : item }}
            </option>
        </select>
    </element-wrapper>
    <errors-list :errors="errors" />
</template>

<script>
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

export default {
    components: { ElementWrapper, ErrorsList },
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
        'items',
    ],
    emits     : [
        'update:modelValue',
        'change',
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
    methods   : {
        onChange (event) {
            this.$emit('update:modelValue', event.target.value);
            this.$emit('change', event.target.value);
        },
    },
};
</script>
