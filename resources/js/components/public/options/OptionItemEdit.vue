<template>
    <div class="card form"
         :class="alertClass">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <custom-input v-model="data"
                                  :errors="errors.data"
                                  :type="type"
                                  :required="true"
                                  @change="clearError('data')"
                    />
                </div>
            </div>

            <div class="d-flex justify-content-center pt-3">
                <button class="btn btn-success w-lg-25 w-md-50 w-100"
                        @click="saveAction">
                    {{ id ? 'Сохранить' : 'Создать' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Url           from '../../../utils/Url.js';
import CustomInput   from '../common/form/CustomInput.vue';
import CustomSelect  from '../common/form/CustomSelect.vue';
import ResponseError from '../../../mixin/ResponseError.js';

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
    },
    data () {
        return {
            id  : this.modelValue,
            data: null,
            type: null,
        };
    },
    methods: {
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.optionsEdit, {
                id: this.modelValue,
            });
            window.axios[Url.Routes.optionsEdit.method](uri).then(response => {
                this.mapResponse(response.data.option);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            let form = new FormData();
            form.append('id', this.id);
            form.append('data', this.data);

            this.clearResponseErrors();
            window.axios[Url.Routes.optionsSave.method](
                Url.Routes.optionsSave.uri,
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
        mapResponse (option) {
            this.id   = option.id;
            this.data = option.data;
            this.type = option.type;
        },
    },
};
</script>