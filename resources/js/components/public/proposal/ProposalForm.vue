<template>
    <div class="alert alert-success"
         v-if="success">
        Спасибо большое! Ваше предложение отправлено!
    </div>
    <div class="form"
         v-if="!success">
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
        <template v-if="files && files.length">
            <ul class="list-unstyled">
                <li v-for="(file, index) in files"
                    class="mb-2 d-flex justify-content-between">
                    <div>
                        <button class="btn btn-sm btn-danger"
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
                v-if="!fileCountExceed">
            <i class="fa fa-paperclip "></i>&nbsp;Файлы
        </button>
        <input class="d-none"
               type="file"
               ref="fileElem"
               @change="appendFiles"
               multiple>
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

            files: [],

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
            form.append('text', this.text ? this.text : null);

            this.files.forEach((file, index) => {
                form.append('file' + index, file);
            });

            window.axios[Url.Routes.proposalCreate.method](Url.Routes.proposalCreate.uri, form).then(response => {
                localStorage.removeItem('proposalEmail');
                localStorage.removeItem('proposalPhone');
                localStorage.removeItem('proposalName');
                localStorage.removeItem('proposalText');

                this.email = '';
                this.phone = '';
                this.name  = '';
                this.text  = '';
                this.files = [];

                this.success = true;

                setTimeout(() => {
                    location.reload();
                }, 20000);
            }).catch(response => {
                this.parseResponseErrors(response);
            }).finally(() => {
                this.pending = false;
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
            return !this.text || this.pending || this.fileSizeExceed;
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