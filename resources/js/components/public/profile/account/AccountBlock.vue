<template>
    <div class="border">
        <table class="table table-responsive align-middle m-0">
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

<script>
import ResponseError  from '../../../../mixin/ResponseError.js';

export default {
    mixins    : [
        ResponseError,
    ],
    props: [
        'account',
    ],
    data () {
        return {

        };
    },
};
</script>

<style scoped>
</style>