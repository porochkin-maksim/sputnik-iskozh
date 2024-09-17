<template>
    <div class="card form"
         :class="alertClass">
        <div class="card form">
            <div class="card-header">
                {{ this.id ? 'Редактирование опроса' : 'Создание опроса' }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <custom-input v-model="title"
                                              :errors="errors.title"
                                              :label="'Название'"
                                              :required="true"
                                />
                            </div>
                            <div class="col-6">
                                <custom-input v-model="start_at"
                                              :errors="errors.start_at"
                                              :type="'datetime-local'"
                                              :label="'Дата начала'"
                                              :required="true"
                                />
                            </div>
                            <div class="col-6">
                                <custom-input v-model="end_at"
                                              :errors="errors.end_at"
                                              :type="'datetime-local'"
                                              :label="'Дата окончания'"
                                              :required="true"
                                />
                            </div>
                            <div class="col-12 pt-2">
                                <div class="vh-30">
                                    <html-editor v-model:value="description" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-sm btn-success"
                        @click="saveAction">
                    {{ this.id ? 'Сохранить' : 'Создать' }}
                </button>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center pt-3">
        <button class="btn btn-success w-lg-25 w-md-50 w-100"
                @click="saveAction">
            {{ id ? 'Сохранить' : 'Создать' }}
        </button>
    </div>
</template>

<script>
import Url            from '../../utils/Url.js';
import CustomInput    from '../common/form/CustomInput.vue';
import CustomSelect   from '../common/form/CustomSelect.vue';
import ResponseError  from '../../mixin/ResponseError.js';
import HtmlEditor     from '../common/HtmlEditor.vue';
import CustomCheckbox from '../common/form/CustomCheckbox.vue';

export default {
    emits     : ['updated'],
    components: {
        CustomCheckbox,
        CustomSelect,
        CustomInput,
        HtmlEditor,
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
            id           : this.modelValue,
            title        : null,
            description  : null,
            start_at     : null,
            end_at       : null,
            notify_emails: null,
        };
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.pollsCreate.method](Url.Routes.pollsCreate.uri).then(response => {
                this.mapResponse(response.data.poll);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.pollsEdit, {
                id: this.modelValue,
            });
            window.axios[Url.Routes.pollsEdit.method](uri).then(response => {
                this.categories = response.data.categories;
                this.mapResponse(response.data.poll);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            let form = new FormData();
            form.append('id', this.id);
            form.append('title', this.title);
            form.append('description', this.description ? this.description : '');
            form.append('start_at', this.start_at);
            form.append('end_at', this.end_at);
            form.append('notify_emails', this.notify_emails);

            this.clearResponseErrors();
            window.axios[Url.Routes.pollsSave.method](
                Url.Routes.pollsSave.uri,
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
        mapResponse (poll) {
            this.id            = poll.id;
            this.title         = poll.title;
            this.description   = poll.description;
            this.start_at      = poll.startAt;
            this.end_at        = poll.endAt;
            this.notify_emails = poll.notifyEmails;
        },
    },
};
</script>