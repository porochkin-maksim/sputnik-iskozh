<template>
    <div>
        <div class="chart-container">
            <canvas ref="chart"></canvas>
        </div>
        <div class="text-end mt-2">
            <strong>Итого за период: {{ formatNumber(totalSum) }}</strong>
        </div>
    </div>
</template>

<script>
import {
    Chart,
    LineElement,
    PointElement,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
} from 'chart.js';

// Регистрируем компоненты только один раз
Chart.register(
    LineElement,
    PointElement,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
);

export default {
    name : 'CountersChartBlock',
    props: {
        chartData: {
            type    : Array,
            required: true,
        },
    },
    data () {
        return {
            chart           : null,
            totalSum        : 0,
            chartInitialized: false,
        };
    },
    methods: {
        formatNumber (value) {
            return Math.round(value).toLocaleString('ru-RU');
        },
        formatDate (date) {
            return new Date(date).toLocaleDateString('ru-RU', {
                month: 'long',
                year : 'numeric',
            });
        },
        initChart () {
            if (!this.chartData?.length) {
                return;
            }

            const canvas = this.$refs.chart;
            if (!canvas) {
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                return;
            }

            // Подготавливаем данные
            const data = [...this.chartData]
                .filter(item => item.date && !isNaN(parseFloat(item.value)))
                .sort((a, b) => new Date(a.date) - new Date(b.date));

            if (!data.length) {
                return;
            }

            // Считаем сумму
            this.totalSum = data.reduce((sum, item) => sum + parseFloat(item.value), 0);

            // Находим границы
            const values = data.map(item => parseFloat(item.value));
            const min    = Math.floor(Math.min(...values) / 100) * 100;
            const max    = Math.ceil(Math.max(...values) / 100) * 100;

            // Уничтожаем старый график если есть
            if (this.chart) {
                this.chart.destroy();
            }

            // Создаем график
            this.chart = new Chart(ctx, {
                type   : 'line',
                data   : {
                    labels  : data.map(item => this.formatDate(item.date)),
                    datasets: [
                        {
                            data           : data.map(item => parseFloat(item.value)),
                            borderColor    : 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension        : 0.1,
                            pointRadius    : 4,
                            fill           : true,
                        }],
                },
                options: {
                    responsive         : true,
                    maintainAspectRatio: false,
                    animation          : {
                        duration: 0,
                    },
                    scales             : {
                        y: {
                            min,
                            max,
                            ticks: {
                                stepSize: 100,
                                callback: value => this.formatNumber(value),
                            },
                        },
                    },
                    plugins            : {
                        legend : {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: context => `Значение: ${this.formatNumber(context.raw)}`,
                            },
                        },
                    },
                },
            });

            this.chartInitialized = true;
        },
        updateChart () {
            this.$nextTick(() => {
                this.initChart();
            });
        },
    },
    watch  : {
        chartData: {
            handler (newData) {
                this.updateChart();
            },
            deep: true,
        },
    },
    mounted () {
        this.$nextTick(() => {
            this.initChart();
        });
    },
    beforeUnmount () {
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
    },
};
</script>

<style scoped>
.chart-container {
    position : relative;
    height   : 300px;
    width    : 100%;
}
</style>