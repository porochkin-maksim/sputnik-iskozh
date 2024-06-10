<template>
    <label class="form-control label-input">
        <span class="label-text">
            {{ label }}
        </span>
<!--        <template v-if="usePrimeVue">-->
<!--            <template v-if="type==='datetime-local'">-->
<!--                <Calendar v-model="localValue"-->
<!--                          showTime-->
<!--                          hourFormat="24"-->
<!--                          :placeholder="placeholder"-->
<!--                />-->
<!--            </template>-->
<!--        </template>-->
        <input
               :value="modelValue"
               :type="computedType"
               :required="required"
               :placeholder="placeholder"
               :name="name"
               @change="onChange"
               @keyup.enter="onSubmit"
               @keyup="onChange"
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
        'modelValue',
        'errors',
        'type',
        'label',
        'name',
        'placeholder',
        'required',
    ],
    emits     : [
        'update:modelValue',
        'change',
        'submit',
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
        },
        onSubmit (event) {
            this.onChange(event);
            this.$emit('submit');
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
