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
        <button class="btn btn-sm btn-outline-success"
                @click="apply">OK
        </button>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
} from 'vue';

const props = defineProps({
    year : Number,
    month: Number,
});

const emit = defineEmits(['apply']);

const yearsRange = ref([]);
const months     = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

// Генерируем диапазон лет
const currentYear = new Date().getFullYear();
yearsRange.value  = Array.from({ length: 111 }, (_, i) => currentYear - 100 + i);

const localYear  = ref(props.year);
const localMonth = ref(props.month);

watch(() => [props.year, props.month], ([y, m]) => {
    localYear.value  = y;
    localMonth.value = m;
});

const apply = () => {
    emit('apply', { year: localYear.value, month: localMonth.value });
};
</script>