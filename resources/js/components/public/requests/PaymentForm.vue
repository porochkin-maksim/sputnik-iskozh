<template>
    <div class="alert alert-success"
         v-if="success">
        Спасибо большое! Сведения о платеже приняты и будут обработаны.
    </div>
    <div class="form"
         v-if="!success">
        <custom-input v-model="account"
                      :classes="'my-3'"
                      @change="clearError('account')"
                      :required="true"
                      :errors="errors.account"
                      :label="'Номер дачи и номер участка (например: 999/1 )'"
                      :disabled="loading || propAccount?.number || propInvoice?.id"
                      @submit="sendForm"
        />
        <custom-input v-model="cost"
                      :classes="'my-3'"
                      @change="clearError('cost')"
                      :required="true"
                      :errors="errors.cost"
                      :label="'Сумма платежа'"
                      :disabled="loading"
                      @submit="sendForm"
        />
        <custom-textarea v-model="text"
                         @change="clearError('text')"
                         :required="true"
                         :errors="errors.text"
                         :label="'Комментарий о платеже - когда и за что платили'"
                         :height="'100'"
                         :disabled="loading || propInvoice?.id"
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
                      :label="'Ваше имя (по желанию)'"
                      :disabled="loading || propUser?.email"
                      @submit="sendForm"
        />
        <custom-input v-model="email"
                      :classes="'mb-3'"
                      @change="clearError('email')"
                      :errors="errors.email"
                      :label="'Эл.почта (по желанию)'"
                      :disabled="loading || propUser?.email"
                      @submit="sendForm"
        />
        <custom-input v-model="phone"
                      :classes="'mb-3'"
                      @change="clearError('phone')"
                      :errors="errors.phone"
                      :label="'Телефон (по желанию)'"
                      :disabled="loading || propUser?.phone"
                      @submit="sendForm"
        />
        <template v-if="files && files.length">
            <ul class="list-unstyled">
                <li v-for="(file, index) in files"
                    class="mb-2 d-flex justify-content-between">
                    <div>
                        <button class="btn btn-sm btn-danger"
                                :disabled="loading"
                                @click="removeFile(index)">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ index + 1 }}. {{ file.name }}
                    </div>
                    <span class="text-secondary">
                        {{ (file.size / (1024 * 1024)).toFixed(2) }}MB
                    </span>
                </li>
            </ul>
            <div class="d-flex justify-content-end small">
                <span :class="[fileSizeExceed ? 'text-danger' : 'text-secondary']">Размер файлов: {{ filesSize }}MB</span>
            </div>
        </template>
        <button class="btn btn-outline-secondary"
                @click="chooseFiles"
                :disabled="loading"
                v-if="!fileCountExceed">
            <i class="fa fa-paperclip "></i>&nbsp;Файлы подтверждающие оплату
        </button>
        <input class="d-none"
               type="file"
               ref="fileElem"
               @change="appendFiles"
               multiple>
        <div class="d-flex justify-content-end mt-2">
            <button type="submit"
                    :disabled="isSubmitDisable"
                    v-if="!loading"
                    @click="sendForm"
                    class="btn btn-success">Отправить
            </button>
            <button class="btn border-0"
                    disabled
                    v-else>
                <i class="fa fa-spinner fa-spin"></i> Отправка
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
    name      : 'PaymentForm',
    components: {
        CustomTextarea,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : {
        propAccount: {
            type   : Object,
            default: null,
        },
        propUser   : {
            type   : Object,
            default: null,
        },
        propInvoice   : {
            type   : Object,
            default: null,
        },
    },
    created () {
        if (this.propInvoice?.id) {
            this.account = this.propInvoice?.account.number;
        }
        else if (this.propAccount?.number) {
            this.account = this.propAccount?.number;
        }
        else {
            this.account = localStorage.getItem('requestAccount') === 'null' ? '' : localStorage.getItem('requestAccount');
        }

        if (this.propUser?.email) {
            this.email = this.propUser?.email;
        }
        else {
            this.email = localStorage.getItem('requestEmail') === 'null' ? '' : localStorage.getItem('requestEmail');
        }

        if (this.propUser?.phone) {
            this.phone = this.propUser?.phone;
        }
        else {
            this.phone = localStorage.getItem('requestPhone') === 'null' ? '' : localStorage.getItem('requestPhone');
        }

        if (this.propUserName) {
            this.name = this.propUserName;
        }
        else {
            this.name = localStorage.getItem('requestName') === 'null' ? '' : localStorage.getItem('requestName');
        }

        if (this.propInvoice?.delta) {
            this.cost = this.propInvoice?.delta;
        }
        else {
            this.cost = localStorage.getItem('requestCost') === 'null' ? '' : localStorage.getItem('requestCost');
        }

        if (this.propInvoice?.id) {
            this.text = 'Оплата по счёту №' + this.propInvoice?.id + ' за период "' + this.propInvoice.period.name + '" за участок ' + this.propInvoice.account.number;
        }
        else {
            this.text = localStorage.getItem('requestText') === 'null' ? '' : localStorage.getItem('requestText');
        }
    },
    data () {
        return {
            loading: false,

            Url,
            account: '',
            email  : '',
            phone  : '',
            name   : '',
            text   : '',
            cost   : '',

            files: [],

            success: null,
        };
    },
    methods : {
        sendForm () {
            this.loading = true;
            this.clearResponseErrors();
            let form = new FormData();
            form.append('email', this.email ? this.email : null);
            form.append('phone', this.phone ? this.phone : null);
            form.append('name', this.name ? this.name : null);
            form.append('account', this.account ? this.account : null);
            form.append('text', this.text ? this.text : null);
            form.append('cost', this.cost ? this.cost : null);
            form.append('invoice', this.propInvoice?.id ? this.propInvoice?.id : null);

            this.files.forEach((file, index) => {
                form.append('file' + index, file);
            });

            Url.RouteFunctions.paymentCreate({}, form).then(response => {
                localStorage.removeItem('requestText');
                this.success = true;
                this.showSuccess('Платёж принят');
                setTimeout(() => {
                    location.reload();
                }, 10000);
            }).catch(response => {
                this.parseResponseErrors(response);
            }).finally(() => {
                this.loading = false;
            });
        },
        chooseFiles () {
            this.$refs.fileElem.click();
        },
        appendFiles (event) {
            for (let i = 0; i < event.target.files.length; i++) {
                if (!this.fileCountExceed) {
                    this.files.push(event.target.files[i]);
                }
            }
        },
        removeFile (index) {
            let result = [];
            for (let i = 0; i < this.files.length; i++) {
                if (i !== index) {
                    result.push(this.files[i]);
                }
            }
            this.files = result;
        },
    },
    watch   : {
        email () {
            localStorage.setItem('requestEmail', this.email);
        },
        phone () {
            localStorage.setItem('requestPhone', this.phone);
        },
        name () {
            localStorage.setItem('requestName', this.name);
        },
        text () {
            localStorage.setItem('requestText', this.text);
        },
        cost () {
            localStorage.setItem('requestCost', this.cost);
        },
        account () {
            localStorage.setItem('requestAccount', this.account);
        },
    },
    computed: {
        propUserName () {
            if (!this.propUser?.email) {
                return null;
            }
            return (this.propUser?.lastName + ' ' + this.propUser?.firstName + ' ' + this.propUser?.middleName).replace('null', '');
        },
        isSubmitDisable () {
            return !this.files.length || !this.account || !this.text || this.loading || this.fileSizeExceed;
        },
        filesSize () {
            let result = 0;
            this.files.forEach(file => {
                result += file.size;
            });
            return (result / (1024 * 1024)).toFixed(2);
        },
        fileSizeExceed () {
            return this.filesSize > 20;
        },
        fileCountExceed () {
            return this.files.length > 4;
        },
    },
};
</script>