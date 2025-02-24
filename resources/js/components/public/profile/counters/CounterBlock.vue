<template>
    <div v-if="showForm">
        <wrapper @close="showForm=false" :container-class="'w-lg-25 w-md-50 w-100'">
            <div class="container-fluid">
                <div class="card form">
                    <div class="card-header text-center">
                        Добавление основного счётчика
                    </div>
                    <div class="card-body">
                        <custom-input v-model="number"
                                      :errors="errors.number"
                                      :type="'text'"
                                      :placeholder="'Номер'"
                                      :required="true"
                        />
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <button class="btn btn-success" @click="saveAction" :disabled="!number">
                            Добавить
                        </button>
                    </div>
                </div>
            </div>
        </wrapper>
    </div>
    <div class="border">
        <table class="table table-responsive align-middle m-0">
            <tbody>
            <tr>
                <th class="d-flex justify-content-between align-items-center bg-light">
                    <span>Cчётчики</span>
                    <button class="btn btn-sm btn-light border"
                            v-if="showAddButton"
                            @click="showForm=true">
                        <i class="fa fa-plus"></i>
                    </button>
                </th>
            </tr>
            <tr v-if="primaryCounter">
                <th>{{ primaryCounter.number }}</th>
            </tr>
            <template v-if="counters && counters.length">
                <template v-for="item in counters">
                    <tr v-if="item.id !== primaryCounter?.id">
                        <td>{{ item.number }}</td>
                    </tr>
                </template>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import Url            from '../../../../utils/Url.js';
import ResponseError  from '../../../../mixin/ResponseError.js';
import Wrapper        from '../../../common/Wrapper.vue';
import CustomInput    from '../../../common/form/CustomInput.vue';
import CustomCheckbox from '../../../common/form/CustomCheckbox.vue';

export default {
    components: {
        CustomCheckbox,
        CustomInput,
        Wrapper,

    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'account',
    ],
    data () {
        return {
            showForm: false,

            id     : null,
            number : null,

            primaryCounter: null,
            counters      : null,
        };
    },
    created () {
        this.getActiveCounter();
    },
    methods : {
        getActiveCounter () {
            window.axios[Url.Routes.profileCounterList.method](Url.Routes.profileCounterList.uri).then(response => {
                this.primaryCounter = response.data.counter;
                this.counters       = response.data.counters;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            if (!confirm('Номер счётчика невозможно будет изменить. Продолжить?')) {
                return;
            }

            window.axios[Url.Routes.profileCounterSave.method](Url.Routes.profileCounterSave.uri, {
                number: this.number,
            }).then(response => {
                this.showForm = false;
                this.getActiveCounter();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        Url () {
            return Url;
        },
        showAddButton() {
            return !this.counters || !(this.counters.length > 2)
        }
    },
};
</script>

<style scoped>
</style>