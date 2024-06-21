<template>
    <div class="row">
        <div class="col-lg-6 col-md-9 col-12">
            <div v-if="!account"
                 class="alert alert-warning text-center mb-2">
                <i class="fa fa-warning"></i> <a :href="Url.Routes.accountRegister.uri">Зарегистрировать участок</a>
            </div>
            <div class="card">
                <profile-show :user="user" />
            </div>
            <div class="card mt-2">
                <profile-email :user="user" />
            </div>
            <div class="card mt-2">
                <profile-password :user="user" />
            </div>
        </div>
        <div class="col-lg-6 col-md-3 col-12" v-if="account">
            <counter-block />
        </div>
    </div>
</template>

<script>
import Url             from '../../utils/Url.js';
import ResponseError   from '../../mixin/ResponseError.js';
import PageTemplate    from '../pages/SingleColumnPage.vue';
import CustomInput     from '../common/form/CustomInput.vue';
import ProfileShow     from './ProfileShow.vue';
import ProfileEmail    from './ProfileEmail.vue';
import ProfilePassword from './ProfilePassword.vue';
import CounterBlock    from './counters/CounterBlock.vue';

export default {
    name      : 'ProfileBlock',
    components: {
        CounterBlock,
        ProfilePassword,
        ProfileEmail,
        ProfileShow,
        CustomInput,
        PageTemplate,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'account',
        'user',
    ],
    data () {
        return {};
    },
    created () {

    },
    methods : {},
    computed: {
        Url () {
            return Url;
        },
    },
};
</script>

<style scoped>
.table {
    tr {
        th {
            text-align : right;
            width      : 50px;
        }
    }
}
</style>