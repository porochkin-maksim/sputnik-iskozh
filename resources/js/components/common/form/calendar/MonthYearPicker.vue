<template>
    <div class="custom-calendar__month-year-picker d-flex align-items-center gap-2 mb-2">
        <select v-model="localYear"
                class="form-select form-select-sm w-auto">
            <option v-for="y in yearsRange"
                    :key="y"
                    :value="y">{{ y }}
            </option>
        </select>
        <select v-model="localMonth"
                class="form-select form-select-sm w-auto">
            <option v-for="(m, idx) in months"
                    :key="idx"
                    :value="idx">{{ m }}
            </option>
        </select>
        <button type="button"
                class="btn btn-sm btn-outline-success"
                @click="apply">OK
        </button>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
    watch,
} from 'vue';

const props = defineProps({
    year : {
        type    : Number,
        required: true,
    },
    month: {
        type    : Number,
        required: true,
    },
});

const emit = defineEmits(['apply']);

const months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

const yearsRange = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 201 }, (_, index) => currentYear - 100 + index);
});

const localYear  = ref(props.year);
const localMonth = ref(props.month);

watch(() => [props.year, props.month], ([y, m]) => {
    localYear.value  = y;
    localMonth.value = m;
}, { immediate: true });

const apply = () => {
    emit('apply', { year: localYear.value, month: localMonth.value });
};
</script>
