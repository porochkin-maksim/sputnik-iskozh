<template>
    <div class="row">
        <div class="col-lg-5 col-md-7 col-12">
            <div class="alert alert-info text-center">
                <i class="fa fa-warning"></i> Личный кабинет находится в разработке <i class="fa fa-warning"></i>
            </div>
            <div v-if="!account"
                 class="alert alert-warning text-center mb-2">
                <i class="fa fa-warning"></i> <a :href="Url.Routes.accountRegister.uri">Зарегистрировать участок</a>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        ФИО
                    </div>
                    <button class="btn btn-light btn-sm border" @click="toggleProfileBlock">
                        <i class="fa fa-chevron-up" v-if="showProfileBlock"></i>
                        <i class="fa fa-chevron-down" v-else></i>
                    </button>
                </div>
                <div class="card-body" v-if="showProfileBlock">
                    <profile-show :user="user"/>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        Смена эл.почты
                    </div>
                    <button class="btn btn-light btn-sm border" @click="toggleEmailBlock">
                        <i class="fa fa-chevron-up" v-if="showEmailBlock"></i>
                        <i class="fa fa-chevron-down" v-else></i>
                    </button>
                </div>
                <div class="card-body" v-if="showEmailBlock">
                    <profile-email :user="user" />
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        Смена пароля
                    </div>
                    <button class="btn btn-light btn-sm border" @click="togglePasswordBlock">
                        <i class="fa fa-chevron-up" v-if="showPasswordBlock"></i>
                        <i class="fa fa-chevron-down" v-else></i>
                    </button>
                </div>
                <div class="card-body" v-if="showPasswordBlock">
                    <profile-password :user="user" />
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-md-5 col-12" v-if="account">
            <div class="row mt-lg-0 mt-2">
                <div class="col-lg-6 col-md-12">
                    <account-block :account="account" />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-6 col-md-12">
                    <counter-block :account="account" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Url             from '../../../utils/Url.js';
import ResponseError   from '../../../mixin/ResponseError.js';
import PageTemplate    from '../pages/SingleColumnPage.vue';
import CustomInput     from '../../common/form/CustomInput.vue';
import ProfileShow     from './ProfileShow.vue';
import ProfileEmail    from './ProfileEmail.vue';
import ProfilePassword from './ProfilePassword.vue';
import CounterBlock    from './counters/CounterBlock.vue';
import AccountBlock    from './account/AccountBlock.vue';

export default {
    name      : 'ProfileBlock',
    components: {
        AccountBlock,
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
        return {
            showProfileBlock: false,
            showEmailBlock: false,
            showPasswordBlock: false,
        };
    },
    created () {

    },
    methods : {
        toggleProfileBlock() {
            this.showProfileBlock = !this.showProfileBlock;
        },
        toggleEmailBlock() {
            this.showEmailBlock = !this.showEmailBlock;
        },
        togglePasswordBlock() {
            this.showPasswordBlock = !this.showPasswordBlock;
        },
    },
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