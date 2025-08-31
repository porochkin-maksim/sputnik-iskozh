<template>
    <div>
        <h5>Пользователи</h5>
        <table class="table align-middle m-0 text-center"
               v-if="users && users.length">
            <thead>
            <tr>
                <th>ФИО</th>
                <th>Почта</th>
                <th>Доля</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="user in users">
                <tr>
                    <td class="text-start"><a :href="user.viewUrl">{{ user.fullName }}</a></td>
                    <td class="text-start">
                        <span :data-copy="user.email" class="text-primary cursor-pointer">{{ user.email }}</span>
                     </td>
                    <td><i class="fa fa-user" :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>&nbsp;{{ user.fractionPercent }}</td>
                    <td>{{ $formatDate(user.ownerDate) }}</td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between mt-2">
        <div class="d-flex">
            <a class="btn btn-success me-2"
                    :href="createUserPageLink">Добавить пользователя
            </a>
        </div>
    </div>
</template>

<script>
import Url           from '../../../../utils/Url.js';
import ResponseError from '../../../../mixin/ResponseError.js';
import HistoryBtn    from '../../../common/HistoryBtn.vue';

export default {
    components: {
        HistoryBtn,
    },
    props     : {
        account: Object,
        users  : Array,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            vueId: null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
    },
    computed: {
        createUserPageLink () {
            return Url.Generator.makeUri(
                Url.Routes.adminUserView,
                {
                    id: null,
                },
                {
                    accountId: this.account.id,
                },
            );
        },
    },
};
</script>