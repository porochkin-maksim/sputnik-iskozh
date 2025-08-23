<template>
    <custom-select v-model="accountId"
                   :items="accounts"
                   :classes="'form-control-sm'"
                   :required="false"
                   @change="switchAccount"
    />
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import CustomSelect  from '../../common/form/CustomSelect.vue';
import Url           from '../../../utils/Url.js';

export default {
    name      : 'AccountSwitcher',
    components: { CustomSelect },
    mixins    : [
        ResponseError,
    ],
    props     : {
        accounts: {
            default: [],
        },
        selected: {
            default: null,
        },
    },
    data () {
        return {
            accountId: null,
        };
    },
    created () {
        this.accountId = this.selected;
    },
    methods: {
        switchAccount(data) {
            Url.RouteFunctions.profileAccountSwitch({}, {
                accountId: data
            }).then(response => {
                console.log(response);
                if (response.data) {
                    this.showInfo('Сейчас страница будет перезагружена');
                    location.reload();
                }
                else {
                    this.showDanger('Что-то пошло не так');
                }
            })
        }
    }
};
</script>

<style scoped>
</style>