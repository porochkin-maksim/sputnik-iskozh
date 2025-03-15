<template>
    <div class="row">
        <div class="col-lg-5 col-md-7 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"
                     :class="showProfileBlock ? '' : 'border-bottom-0'">
                    <div class="fw-bold">
                        ФИО
                    </div>
                    <button class="btn btn-light btn-sm "
                            @click="toggleProfileBlock">
                        <i class="fa fa-chevron-up"
                           v-if="showProfileBlock"></i>
                        <i class="fa fa-chevron-down"
                           v-else></i>
                    </button>
                </div>
                <div class="card-body"
                     v-if="showProfileBlock">
                    <profile-show :user="user" />
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center"
                     :class="showEmailBlock ? '' : 'border-bottom-0'">
                    <div class="fw-bold">
                        Смена эл.почты
                    </div>
                    <button class="btn btn-light btn-sm"
                            @click="toggleEmailBlock">
                        <i class="fa fa-chevron-up"
                           v-if="showEmailBlock"></i>
                        <i class="fa fa-chevron-down"
                           v-else></i>
                    </button>
                </div>
                <div class="card-body"
                     v-if="showEmailBlock">
                    <profile-email :user="user" />
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center"
                     :class="showPasswordBlock ? '' : 'border-bottom-0'">
                    <div class="fw-bold">
                        Смена пароля
                    </div>
                    <button class="btn btn-light btn-sm"
                            @click="togglePasswordBlock">
                        <i class="fa fa-chevron-up"
                           v-if="showPasswordBlock"></i>
                        <i class="fa fa-chevron-down"
                           v-else></i>
                    </button>
                </div>
                <div class="card-body"
                     v-if="showPasswordBlock">
                    <profile-password :user="user" />
                </div>
            </div>
            <div v-if="!account||!account.id"
                 class="alert alert-info text-center mt-2">
                <a :href="Url.Routes.accountRegister.uri"
                   class="text-decoration-none">Зарегистрировать участок</a>
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
import CountersBlock   from './counters/CountersBlock.vue';
import AccountBlock    from './account/AccountBlock.vue';

export default {
    name      : 'ProfileBlock',
    components: {
        AccountBlock,
        CounterBlock: CountersBlock,
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
            showProfileBlock : false,
            showEmailBlock   : false,
            showPasswordBlock: false,
        };
    },
    created () {

    },
    methods : {
        toggleProfileBlock () {
            this.showProfileBlock = !this.showProfileBlock;
        },
        toggleEmailBlock () {
            this.showEmailBlock = !this.showEmailBlock;
        },
        togglePasswordBlock () {
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