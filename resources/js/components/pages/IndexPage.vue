<template>
    <default-page>
        <template v-slot:left>
        </template>
        <template v-slot:right>
        </template>
    </default-page>
</template>

<script>
import ResponseError from '../../mixin/ResponseError.js';
import DefaultPage   from '../pages/DefaultPage.vue';
import Url           from '../../utils/Url.js';

export default {
    name      : 'IndexPage',
    components: {
        DefaultPage,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
        };
    },
    methods: {
    },
};
</script>