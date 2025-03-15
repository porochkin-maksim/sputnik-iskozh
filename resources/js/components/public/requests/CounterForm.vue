<template>
    <div class="alert alert-success"
         v-if="success">
        Спасибо большое! Сведения о показаниях приняты и будут обработаны.
    </div>
    <div class="form"
         v-if="!success">
        <custom-input v-model="account"
                      :classes="'my-3'"
                      @change="clearError('account')"
                      :errors="errors.account"
                      :label="'Номер дачи и номер участка (например: 999/1 )'"
                      @submit="sendForm"
        />
        <custom-input v-model="counter"
                      :classes="'my-3'"
                      @change="clearError('counter')"
                      :errors="errors.counter"
                      :label="'Номер счётчика'"
                      @submit="sendForm"
        />
        <custom-input v-model="value"
                      :classes="'my-3'"
                      @change="clearError('value')"
                      :errors="errors.value"
                      :label="'Показания счётчика'"
                      @submit="sendForm"
        />
        <custom-input v-model="name"
                      :classes="'my-3'"
                      @change="clearError('name')"
                      :errors="errors.name"
                      :label="'Ваше имя (по желанию)'"
                      @submit="sendForm"
        />
        <custom-input v-model="email"
                      :classes="'mb-3'"
                      @change="clearError('email')"
                      :errors="errors.email"
                      :label="'Эл.почта (по желанию)'"
                      @submit="sendForm"
        />
        <custom-input v-model="phone"
                      :classes="'mb-3'"
                      @change="clearError('phone')"
                      :errors="errors.phone"
                      :label="'Телефон (по желанию)'"
                      @submit="sendForm"
        />
        <div class="mt-2">
            <div v-if="file">
                <button class="btn btn-sm btn-danger"
                        @click="removeFile">
                    <i class="fa fa-trash"></i>
                </button>
                &nbsp;
                {{ file.name }}
            </div>
            <template v-else>
                <button class="btn btn-outline-secondary"
                        @click="chooseFile"
                        v-if="!file">
                    <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                </button>
                <input class="d-none"
                       type="file"
                       ref="fileElem"
                       accept="image/*"
                       @change="appendFile"
                />
            </template>
        </div>
        <div class="d-flex justify-content-end mt-2">
            <button type="submit"
                    :disabled="disableSubmit"
                    @click="sendForm"
                    class="btn btn-success">Отправить
            </button>
        </div>
    </div>
</template>

<script>
import Url            from '../../../utils/Url.js';
import ResponseError  from '../../../mixin/ResponseError.js';
import CustomInput    from '../../common/form/CustomInput.vue';
import CustomTextarea from '../../common/form/CustomTextarea.vue';

export default {
    name      : 'CounterForm',
    components: {
        CustomTextarea,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    created () {
        this.account = localStorage.getItem('requestAccount') === 'null' ? '' : localStorage.getItem('requestAccount');
        this.email   = localStorage.getItem('requestEmail') === 'null' ? '' : localStorage.getItem('requestEmail');
        this.phone   = localStorage.getItem('requestPhone') === 'null' ? '' : localStorage.getItem('requestPhone');
        this.name    = localStorage.getItem('requestName') === 'null' ? '' : localStorage.getItem('requestName');
    },
    data () {
        return {
            Url,
            email  : '',
            phone  : '',
            name   : '',
            account: '',
            counter: '',
            value  : '',

            file: null,

            success: null,
            pending: false,
        };
    },
    methods : {
        sendForm () {
            this.pending = true;
            this.clearResponseErrors();
            let form = new FormData();
            form.append('email', this.email ? this.email : null);
            form.append('phone', this.phone ? this.phone : null);
            form.append('name', this.name ? this.name : null);
            form.append('account', this.account ? this.account : null);
            form.append('counter', this.counter ? this.counter : null);
            form.append('value', this.value ? this.value : null);

            form.append('file', this.file);

            window.axios[Url.Routes.counterCreate.method](Url.Routes.counterCreate.uri, form).then(response => {
                this.success = true;
                this.showSuccess('Платёж принят');

                setTimeout(() => {
                    location.reload();
                }, 10000);
            }).catch(response => {
                this.parseResponseErrors(response);
            }).finally(() => {
                this.pending = false;
            });
        },
        chooseFile () {
            this.$refs.fileElem.click();
        },
        appendFile (event) {
            this.file = event.target.files[0];
        },
        removeFile () {
            this.file = null;
        },
    },
    watch   : {
        account () {
            localStorage.setItem('requestAccount', this.account);
        },
        email () {
            localStorage.setItem('requestEmail', this.email);
        },
        phone () {
            localStorage.setItem('requestPhone', this.phone);
        },
        name () {
            localStorage.setItem('requestName', this.name);
        },
    },
    computed: {
        disableSubmit () {
            return !this.account || !this.counter || !this.value || !this.file || this.pending;
        },
        filesSize () {
            let result = 0;
            this.files.forEach(file => {
                result += file.size;
            });
            return (result / (1024 * 1024)).toFixed(2);
        },
    },
};
</script>