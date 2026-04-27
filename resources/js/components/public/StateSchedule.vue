<template>
    <table class="table table-bordered mb-0 text-center table-striped">
        <tbody>
        <tr>
            <th colspan="4">График работы</th>
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

<script>
export default {
    props: ['schedule'],
};
</script>