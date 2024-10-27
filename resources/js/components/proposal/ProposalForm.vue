<template>
    <div class="alert alert-success" v-if="success">
        Спасибо большое! Ваше предложение отправлено!
    </div>
    <div class="form" v-if="!success">
        <custom-textarea v-model="text"
                         @change="clearError('text')"
                         :errors="errors.text"
                         :label="'Ваше предложение'"
                         :required="true"
                         :height="'300'"
                         @submit="sendForm"
        />
        <div class="d-flex justify-content-end small"
             v-if="text && text.length">
            <span class="text-secondary">Символов: {{ text.length }}</span>
        </div>
        <custom-input v-model="name"
                      :classes="'my-3'"
                      @change="clearError('name')"
                      :errors="errors.name"
                      :label="'Имя (по желанию)'"
                      :required="false"
                      @submit="sendForm"
        />
        <custom-input v-model="email"
                      :classes="'mb-3'"
                      @change="clearError('email')"
                      :errors="errors.email"
                      :label="'Эл.почта (по желанию)'"
                      :required="false"
                      @submit="sendForm"
        />
        <custom-input v-model="phone"
                      :classes="'mb-3'"
                      @change="clearError('phone')"
                      :errors="errors.phone"
                      :label="'Телефон (по желанию)'"
                      :required="false"
                      @submit="sendForm"
        />
        <div class="d-flex justify-content-end">
            <button type="submit"
                    :disabled="disableSubmit"
                    @click="sendForm"
                    class="btn btn-success">Отправить
            </button>
        </div>
    </div>
</template>

<script>
import Url            from '../../utils/Url.js';
import ResponseError  from '../../mixin/ResponseError.js';
import CustomInput    from '../common/form/CustomInput.vue';
import CustomTextarea from '../common/form/CustomTextarea.vue';

export default {
    name      : 'ProposalForm',
    components: {
        CustomTextarea,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    created () {
        this.email = localStorage.getItem('proposalEmail') === 'null' ? '' : localStorage.getItem('proposalEmail');
        this.phone = localStorage.getItem('proposalPhone') === 'null' ? '' : localStorage.getItem('proposalPhone');
        this.name  = localStorage.getItem('proposalName') === 'null' ? '' : localStorage.getItem('proposalName');
        this.text  = localStorage.getItem('proposalText') === 'null' ? '' : localStorage.getItem('proposalText');
    },
    data () {
        return {
            Url,
            email: '',
            phone: '',
            name : '',
            text : '',

            success : null,
        };
    },
    methods : {
        sendForm () {
            this.clearResponseErrors();
            window.axios[Url.Routes.proposalCreate.method](Url.Routes.proposalCreate.uri, {
                email: this.email,
                phone: this.phone,
                name : this.name,
                text : this.text,
            }).then(response => {
                localStorage.removeItem('proposalEmail')
                localStorage.removeItem('proposalPhone')
                localStorage.removeItem('proposalName')
                localStorage.removeItem('proposalText')

                this.email = '';
                this.phone = '';
                this.name  = '';
                this.text  = '';

                this.success = true;

                setTimeout(() => {
                    this.success = null;
                }, 10000)
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch   : {
        email () {
            localStorage.setItem('proposalEmail', this.email);
        },
        phone () {
            localStorage.setItem('proposalPhone', this.phone);
        },
        name () {
            localStorage.setItem('proposalName', this.name);
        },
        text () {
            localStorage.setItem('proposalText', this.text);
        },
    },
    computed: {
        disableSubmit () {
            return !this.text;
        },
    },
};
</script>