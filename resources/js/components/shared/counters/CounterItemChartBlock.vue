<template>
    <counters-chart-block :chart-data="chartData" />
</template>

<script setup>
import { computed }       from 'vue';
import CountersChartBlock from './CountersChartBlock.vue';

const props = defineProps({
    histories: {
        type    : Array,
        required: true,
    },
});

// Вычисляемое свойство для данных графика
const chartData = computed(() => {
    if (!props.histories || !props.histories.length) {
        return [];
    }

    // Фильтруем записи, у которых есть delta (включая 0)
    return props.histories
        .filter(item => {
            // Проверяем, что delta существует и не null/undefined
            // Для построения графика нужны все точки, даже с delta = 0
            return item.date && item.delta !== undefined && item.delta !== null;
        })
        .map(item => ({
            date : item.date,
            value: parseFloat(item.delta),
        }))
        .sort((a, b) => new Date(a.date) - new Date(b.date));
});
</script>