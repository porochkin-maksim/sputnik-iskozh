<template>
    <div class="mb-2 pb-2 border-bottom panel-block d-flex justify-content-between" v-if="canActions">
        <div class="d-flex">
            <div class="search-block" v-if="actions.accounts">
                <i class="fa fa-home prefix"></i>
                <input class="form-control" v-model="account" placeholder="Участок" @keyup="onAccountSearch" @keyup.enter="searchAction">
                <i class="fa fa-search postfix"></i>
            </div>
            <div class="search-block ms-2" v-if="actions.users">
                <i class="fa fa-user prefix"></i>
                <input class="form-control" v-model="user" placeholder="Пользователь" @keyup="onUserSearch" @keyup.enter="searchAction">
                <i class="fa fa-search postfix"></i>
            </div>
        </div>
    </div>
</template>

<script>
import ResponseError from '../../mixin/ResponseError.js';
import Url          from '../../utils/Url.js';
import CustomSelect from '../common/form/CustomSelect.vue';

export default {
    name      : 'TopPanelBlock',
    components: { CustomSelect },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            actions   : null,
            canActions: null,

            account: null,
            user   : null,

            payments: 0,
        };
    },
    created () {
        this.listAction();
    },
    methods: {
        listAction () {
            window.axios[Url.Routes.adminTopPanelIndex.method](Url.Routes.adminTopPanelIndex.uri).then(response => {
                this.actions    = response.data.actions;
                this.canActions = response.data.canActions;
                this.payments   = response.data.payments;

                document.getElementById('new-payments-count').innerHTML = ' (' + this.payments + ')';
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        searchAction () {
            window.axios[Url.Routes.adminTopPanelSearch.method](Url.Routes.adminTopPanelSearch.uri, {
                account: this.account,
                user   : this.user,
            }).then(response => {
                if (response.data) {
                    location.href = response.data;
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        onAccountSearch() {
            this.user = null;
        },
        onUserSearch () {
            this.account = null;
        },
    },
};
</script>

<style scoped>
.panel-block {
    font-size : 12px;
}

.panel-block input {
    padding : 0.2rem 1.5rem;
}

.panel-block .search-block {
    width       : 8.5rem;
    position    : relative;
    display     : flex;
    align-items : center;
}

.panel-block .search-block .prefix {
    position : absolute;
    left     : 0.5rem;
}

.panel-block .search-block .postfix {
    position : absolute;
    right    : 0.5rem;
}
</style>