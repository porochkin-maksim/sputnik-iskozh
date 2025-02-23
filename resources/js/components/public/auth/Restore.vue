<template>
    <div v-if="registerSuccessMessage"
         class="alert alert-success"
         v-html="registerSuccessMessage" />
    <form v-else @submit.prevent="restoreAction">
        <custom-input v-model="login"
                      :errors="errors.email"
                      @change="clearError('email')"
                      :type="'email'"
                      :placeholder="'Эл.почта'"
                      :required="true"
        />
        <div class="d-grid mt-3">
            <button type="submit"
                    class="btn btn-success btn-block text-uppercase mb-2 rounded-pill shadow-sm">Восстановить
            </button>
        </div>
    </form>
</template>

<script>
import CustomInput   from '../common/form/CustomInput.vue';
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';

export default {
    name      : 'Restore',
    components: {
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            registerSuccessMessage: null,

            login: null,
        };
    },
    methods: {
        restoreAction () {
            window.axios[Url.Routes.passwordEmail.method](Url.Routes.passwordEmail.uri, {
                email: this.login,
            }).then(response => {
                this.registerSuccessMessage = response.data.message;
                // setTimeout(() => location.reload(), 3000);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>
