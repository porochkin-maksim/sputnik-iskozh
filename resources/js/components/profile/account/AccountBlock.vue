<template>
    <div class="info-table">
        <table class="table table-sm align-middle m-0">
            <tbody>
            <tr>
                <th>Участок</th>
                <td>{{ account.number }}</td>
            </tr>
            <tr>
                <th>Площадь</th>
                <td>{{ account.size }}м<sup>2</sup></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import {
    defineProps,
} from 'vue';

const props = defineProps({
    account: {
        type   : Object,
        default: {
            number: '',
            size  : 0,
        },
    },
});
</script>
