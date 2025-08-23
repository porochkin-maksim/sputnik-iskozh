<template>
    <div class="row">
        <div class="col-lg-5 col-md-7 col-12">
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center cursor-pointer"
                     @click="togglePasswordBlock"
                     :class="showPasswordBlock ? '' : 'border-bottom-0'">
                    <div class="fw-bold">
                        Смена пароля
                    </div>
                    <button class="btn btn-light btn-sm">
                        <i class="fa fa-chevron-down"
                           v-if="showPasswordBlock"></i>
                        <i class="fa fa-chevron-left"
                           v-else></i>
                    </button>
                </div>
                <div class="card-body pb-0"
                     v-if="showPasswordBlock">
                    <profile-password :user="user" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Url             from '../../utils/Url.js';
import ResponseError   from '../../mixin/ResponseError.js';
import ProfilePassword from './ProfilePassword.vue';

export default {
    name      : 'PasswordBlock',
    components: {
        ProfilePassword,
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
            showPasswordBlock: false,
        };
    },
    created () {

    },
    methods : {
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