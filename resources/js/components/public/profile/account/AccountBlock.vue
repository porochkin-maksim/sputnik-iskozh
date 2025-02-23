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
            <tr>
                <th>Членский взнос</th>
                <td>{{ totalMembershipFee?.formatted }}</td>
            </tr>
            <tr>
                <th>Вывоз мусора</th>
                <td>{{ garbageCollectionFee?.formatted }}</td>
            </tr>
            <tr>
                <th>Ремонт дороги</th>
                <td>{{ roadCollectionFee?.formatted }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import Url            from '../../../../utils/Url.js';
import ResponseError  from '../../../../mixin/ResponseError.js';
import Wrapper        from '../../common/Wrapper.vue';
import CustomInput    from '../../common/form/CustomInput.vue';
import CustomCheckbox from '../../common/form/CustomCheckbox.vue';

export default {
    components: {
        CustomCheckbox,
        CustomInput,
        Wrapper,

    },
    mixins    : [
        ResponseError,
    ],
    props: [
        'account',
    ],
    data () {
        return {
            showForm: false,

            totalMembershipFee  : null,
            membershipFee       : null,
            electricTariff      : null,
            garbageCollectionFee: null,
            roadCollectionFee   : null,
        };
    },
    created () {
        this.getAccountInfo();
    },
    methods: {
        getAccountInfo () {
            window.axios[Url.Routes.accountInfo.method](Url.Routes.accountInfo.uri).then(response => {
                this.totalMembershipFee   = response.data.totalMembershipFee;
                this.membershipFee        = response.data.membershipFee;
                this.electricTariff       = response.data.electricTariff;
                this.garbageCollectionFee = response.data.garbageCollectionFee;
                this.roadCollectionFee    = response.data.roadCollectionFee;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
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
</style>