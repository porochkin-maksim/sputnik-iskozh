<template>
    <div>
        <div class="chart-container" v-if="chartData.length !== 0">
            <canvas ref="chartCanvas"></canvas>
        </div>
        <div class="text-end mt-2" v-if="totalSum > 0">
            <strong>Итого за период: {{ formatNumber(totalSum) }}</strong>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
    nextTick,
} from 'vue';
import {
    Chart,
    LineElement,
    PointElement,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Filler, // Добавляем плагин Filler
} from 'chart.js';

// Регистрируем компоненты
Chart.register(
    LineElement,
    PointElement,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Filler, // Регистрируем Filler
);

const props = defineProps({
    chartData: {
        type    : Array,
        required: true,
        default : () => [],
    },
});

const chartCanvas = ref(null);
let chartInstance = null;
const totalSum    = ref(0);

// Форматирование чисел
const formatNumber = (value) => {
    return Math.round(value).toLocaleString('ru-RU');
};

// Форматирование даты
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        month: 'long',
        year : 'numeric',
    });
};

// Инициализация графика
const initChart = () => {
    if (!chartCanvas.value) {
        return;
    }

    if (!props.chartData || !props.chartData.length) {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
        totalSum.value = 0;
        return;
    }

    const ctx = chartCanvas.value.getContext('2d');
    if (!ctx) {
        return;
    }

    // Подготавливаем данные
    const sortedData = [...props.chartData].sort((a, b) => new Date(a.date) - new Date(b.date));

    // Считаем сумму (только положительные значения для итога)
    totalSum.value = sortedData.reduce((sum, item) => sum + (item.value > 0 ? item.value : 0), 0);

    const labels = sortedData.map(item => formatDate(item.date));
    const values = sortedData.map(item => item.value);

    // Находим границы для оси Y
    const minValue = Math.min(...values, 0);
    const maxValue = Math.max(...values, 1);
    const padding  = (maxValue - minValue) * 0.1 || 10;
    const yMin     = Math.max(0, minValue - padding);
    const yMax     = maxValue + padding;

    // Уничтожаем старый график
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Создаём график
    chartInstance = new Chart(ctx, {
        type   : 'line',
        data   : {
            labels  : labels,
            datasets: [
                {
                    label               : 'Потребление (кВт)',
                    data                : values,
                    borderColor         : 'rgb(75, 192, 192)',
                    backgroundColor     : 'rgba(75, 192, 192, 0.1)',
                    borderWidth         : 2,
                    pointRadius         : 4,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor    : '#fff',
                    pointBorderWidth    : 1,
                    tension             : 0.1,
                    fill                : true, // fill теперь работает, так как Filler зарегистрирован
                },
            ],
        },
        options: {
            responsive         : true,
            maintainAspectRatio: false,
            plugins            : {
                legend : {
                    display : true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `Потребление: ${formatNumber(context.raw)} кВт`,
                    },
                },
            },
            scales             : {
                y: {
                    min  : yMin,
                    max  : yMax,
                    title: {
                        display: true,
                        text   : 'кВт',
                    },
                    ticks: {
                        callback: (value) => formatNumber(value),
                    },
                },
                x: {
                    title: {
                        display: true,
                        text   : 'Дата',
                    },
                },
            },
        },
    });
};

// Обновление графика
const updateChart = () => {
    nextTick(() => {
        initChart();
    });
};

// Следим за изменением данных
watch(() => props.chartData, () => {
    updateChart();
}, { deep: true });

// Монтирование
onMounted(() => {
    nextTick(() => {
        initChart();
    });
});

// Размонтирование
onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
});
</script>

<style scoped>
.chart-container {
    position : relative;
    height   : 300px;
    width    : 100%;
}
</style>