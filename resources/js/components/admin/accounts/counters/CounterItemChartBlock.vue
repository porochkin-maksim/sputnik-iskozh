<template>
    <counters-chart-block
        :chart-data="chartData"
        :options="chartOptions"
    />
</template>

<script>
import CountersChartBlock from '../../../common/blocks/CountersChartBlock.vue';

export default {
    components: { CountersChartBlock },
    props     : {
        histories: {
            type    : Array,
            required: true,
        },
    },
    data () {
        return {
            chartOptions: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
    },
    computed: {
        chartData() {
            return this.histories
                .filter(item => item.date && item.delta && !isNaN(parseFloat(item.delta)))
                .map(item => ({
                    date : item.date,
                    value: parseFloat(item.delta),
                }))
                .sort((a, b) => new Date(a.date) - new Date(b.date));
        },
    },
};
</script>