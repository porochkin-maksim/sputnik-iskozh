<template>
    <page-template class="d-flex justify-content-center">
        <template v-slot:main>
            <div class="w-100 d-flex justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-8 col-12">
                    <div class="alert alert-info text-center">
                        Для пользования личным кабинетом необходимо зарегистрировать свой участок
                    </div>
                    <div class="form d-inline">
                        <custom-input v-model="number"
                                      :errors="errors.number"
                                      :placeholder="'Номер участка'"
                                      :required="true"
                                      @change="clearError('number')"
                        />
                    </div>
                    <div class="d-flex justify-content-center my-3">
                        <button class="btn btn-primary" @click="register">
                            Зарегистрировать
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </page-template>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import PageTemplate  from '../pages/SingleColumnPage.vue';
import CustomInput   from '../common/form/CustomInput.vue';

export default {
    name      : 'RegisterAccount',
    components: {
        CustomInput,
        PageTemplate,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            number: null,
        };
    },
    methods: {
        register () {
            window.axios[Url.Routes.accountRegister.method](Url.Routes.accountRegister.uri, {
                number  : this.number,
            }).then(response => {
                location.reload();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>