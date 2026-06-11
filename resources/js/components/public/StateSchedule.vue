<template>
    <table class="table table-bordered mb-0 text-center table-striped">
        <caption class="visually-hidden">График работы</caption>
        <tbody>
        <tr>
            <th colspan="4" scope="colgroup">График работы</th>
        </tr>
        <tr v-for="s in schedule">
            <td>{{ s.weekDaylable }}</td>
            <td>{{ s.day }}</td>
            <td>{{ s.monthGenitive }}</td>
            <td>{{ s.startTime }} - {{ s.endTime }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
defineProps({
    schedule: {
        type   : Array,
        default: () => [],
    },
});
</script>
