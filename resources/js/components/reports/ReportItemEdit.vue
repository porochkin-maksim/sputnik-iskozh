<template>
    <div class="card form"
         :class="alertClass">
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <custom-input v-model="name"
                                  :errors="errors.name"
                                  :placeholder="'Название'"
                                  :required="false"
                                  @change="clearError('name')"
                    />
                </div>
                <div class="col-4">
                    <custom-input v-model="money"
                                  :errors="errors.money"
                                  :placeholder="'Сумма'"
                                  :type="'money'"
                                  :required="false"
                                  @change="clearError('money')"
                    />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3">
                    <custom-select v-model="category"
                                   :errors="errors.category"
                                   :items="categories"
                                   :required="false"
                                   @change="clearError('category')"
                    />
                </div>
                <div class="col-3">
                    <custom-select v-model="type"
                                   :errors="errors.type"
                                   :items="types"
                                   :required="false"
                                   @change="clearError('type')"
                    />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3">
                    <custom-input v-model="start_at"
                                  :errors="errors.start_at"
                                  :type="'date'"
                                  :required="false"
                                  @change="clearError('start_at')"
                    />
                </div>
                <div class="col-3">
                    <custom-input v-model="end_at"
                                  :errors="errors.end_at"
                                  :type="'date'"
                                  :required="false"
                                  @change="clearError('end_at')"
                    />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-5">
                    <button class="btn btn-success"
                            @click="saveAction">
                        {{ id ? 'Сохранить' : 'Создать' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import CustomInput   from '../common/form/CustomInput.vue';
import CustomSelect  from '../common/form/CustomSelect.vue';
import ResponseError from '../../mixin/ResponseError.js';

export default {
    emits     : ['updated'],
    components: {
        CustomSelect,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'modelValue',
    ],
    created () {
        if (this.modelValue) {
            this.getAction();
        }
        else {
            this.makeAction();
        }
    },
    data () {
        return {
            categories: [],
            types     : [],

            id      : this.modelValue,
            name    : null,
            category: null,
            type    : null,
            start_at: null,
            end_at  : null,
            money   : null,
        };
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.reportsCreate.method](Url.Routes.reportsCreate.uri).then(response => {
                this.categories = response.data.categories;
                this.types      = response.data.types;

                this.mapResponse(response.data.report);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.reportsEdit, {
                id: this.modelValue,
            });
            window.axios[Url.Routes.reportsEdit.method](uri).then(response => {
                this.categories = response.data.categories;
                this.types      = response.data.types;

                this.mapResponse(response.data.report);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            let form = new FormData();
            form.append('id', this.id);
            form.append('name', this.name ? this.name : '');
            form.append('category', this.category);
            form.append('type', this.type);
            form.append('start_at', this.start_at);
            form.append('end_at', this.end_at);
            form.append('money', this.money ? this.money : '');
            form.append('file', this.file);

            this.clearResponseErrors();
            window.axios[Url.Routes.reportsSave.method](
                Url.Routes.reportsSave.uri,
                form,
            ).then(response => {
                if (response.data) {
                    this.mapResponse(response.data);
                    this.eventSuccess();
                    this.$emit('updated');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        mapResponse (report) {
            this.id       = report.id;
            this.name     = report.name;
            this.category = report.category;
            this.type     = report.type;
            this.start_at = report.start_at;
            this.end_at   = report.end_at;
            this.money    = report.money;
        },
    },
};
</script>