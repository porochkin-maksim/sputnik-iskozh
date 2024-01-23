<template>
    <button class="btn btn-primary">Добавить отчёт</button>

    <div class="new-report">
        <div class="card form">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <custom-select v-model="category"
                                       :errors="errors.category"
                                       :items="categories"
                                       :label="'Категория'"
                                       :required="false"
                        />
                    </div>
                    <div class="col-4">
                        <custom-select v-model="type"
                                       :errors="errors.type"
                                       :label="'Тип'"
                                       :items="types"
                                       :required="false"
                        />
                    </div>
                    <div class="col-4">
                        <custom-input v-model="type"
                                      :errors="errors.type"
                                      :label="'Тип'"
                                      :items="types"
                                      :required="false"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import CustomInput   from '../common/CustomInput.vue';
import CustomSelect  from '../common/CustomSelect.vue';
import ResponseError from '../../mixin/ResponseError.js';

export default {
    name      : 'ReportList',
    components: {
        CustomSelect,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'categories',
        'types',
    ],
    data () {
        return {
            category: null,
            type    : null,
        };
    },
    methods: {},
};
</script>