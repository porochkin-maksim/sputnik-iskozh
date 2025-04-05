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
                      :required="true"
                      :errors="errors.account"
                      :label="'Номер дачи и номер участка (например: 999/1 )'"
                      :disabled="propAccount?.number"
                      @submit="sendForm"
        />
        <custom-input v-model="value"
                      :classes="'my-3'"
                      @change="clearError('value')"
                      :required="true"
                      :errors="errors.value"
                      :label="'Показания счётчика'"
                      @submit="sendForm"
        />
        <template v-if="propCounters && propCounters.length">
            <label class="small text-secondary">Счётчик</label>
            <simple-select v-model="counter"
                           class="period"
                           :items="computedCounters"
                           @change="onCounterChange"
            />
        </template>
        <template v-else>
            <custom-input v-model="counter"
                          :classes="'my-3'"
                          @change="clearError('counter')"
                          :errors="errors.counter"
                          :label="'Номер счётчика (по возможности)'"
                          @submit="sendForm"
            />
        </template>
        <custom-input v-model="name"
                      :classes="'my-3'"
                      @change="clearError('name')"
                      :errors="errors.name"
                      :label="'Ваше имя (по желанию)'"
                      :disabled="propUserName"
                      @submit="sendForm"
        />
        <custom-input v-model="email"
                      :classes="'mb-3'"
                      @change="clearError('email')"
                      :errors="errors.email"
                      :label="'Эл.почта (по желанию)'"
                      :disabled="propUser?.email"
                      @submit="sendForm"
        />
        <custom-input v-model="phone"
                      :classes="'mb-3'"
                      @change="clearError('phone')"
                      :errors="errors.phone"
                      :label="'Телефон (по желанию)'"
                      :disabled="propUser?.phone"
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
                    v-if="!pending"
                    @click="sendForm"
                    class="btn btn-success">Отправить
            </button>
            <button class="btn border-0" disabled v-else>
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
import SimpleSelect   from '../../common/form/SimpleSelect.vue';

export default {
    name      : 'CounterForm',
    components: {
        SimpleSelect,
        CustomTextarea,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : {
        propAccount : {
            type   : Object,
            default: {},
        },
        propUser    : {
            type   : Object,
            default: {},
        },
        propCounters: {
            type   : Array,
            default: [],
        },
    },
    created () {
        if (this.propAccount?.number) {
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

        if (this.propCounters && this.propCounters.length) {
            this.counter = this.propCounters[0].id;
            this.value   = this.propCounters[0].value;
        }
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
            if (this.propCounters && this.propCounters.length) {
                form.append('counter_id', this.counter ? this.counter : null);
            }

            form.append('file', this.file);

            window.axios[Url.Routes.counterCreate.method](Url.Routes.counterCreate.uri, form).then(response => {
                this.success = true;
                this.showSuccess('Показания приняты');

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
        onCounterChange () {
            this.propCounters.forEach(item => {
                if (parseInt(item.id) === parseInt(this.counter)) {
                    this.value = item.value;
                }
            });
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
        propUserName () {
            if (!this.propUser?.email) {
                return null;
            }
            return (this.propUser?.lastName + ' ' + this.propUser?.firstName + ' ' + this.propUser?.middleName).replace('null', '');
        },
        disableSubmit () {
            return !this.account || !this.value || !this.file || this.pending;
        },
        computedCounters () {
            let result = [];

            if (!this.propCounters && !this.propCounters.length) {
                return result;
            }

            return this.propCounters.map(item => {
                return {
                    key  : item.id,
                    value: item.number,
                };
            });
        },
    },
};
</script>