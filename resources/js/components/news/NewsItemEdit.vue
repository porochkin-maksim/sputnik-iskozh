<template>
    <div class="card form"
         :class="alertClass">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8 col-12 pe-lg-0">
                    <custom-input v-model="title"
                                  :errors="errors.title"
                                  :placeholder="'Заголовок'"
                                  :required="true"
                                  @change="clearError('title')"
                    />
                </div>
                <div class="col-lg-4 col-12 ps-lg-0">
                    <custom-input v-model="published_at"
                                  :errors="errors.published_at"
                                  :type="'datetime-local'"
                                  :placeholder="'Время публикации'"
                                  :required="true"
                                  @change="clearError('published_at')"
                    />
                </div>
            </div>
            <div class="vh-50">
                <html-editor v-model:value="article" />
            </div>

            <div class="d-flex justify-content-center pt-3">
                <button class="btn btn-primary w-lg-25 w-md-50 w-100"
                        @click="saveAction">
                    {{ id ? 'Сохранить' : 'Создать' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import CustomInput   from '../common/form/CustomInput.vue';
import CustomSelect  from '../common/form/CustomSelect.vue';
import ResponseError from '../../mixin/ResponseError.js';
import HtmlEditor    from '../common/HtmlEditor.vue';

export default {
    emits     : ['updated'],
    components: {
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
            id          : this.modelValue,
            title       : null,
            article     : null,
            published_at: null,
        };
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.newsCreate.method](Url.Routes.newsCreate.uri).then(response => {
                this.mapResponse(response.data.news);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.newsEdit, {
                id: this.modelValue,
            });
            window.axios[Url.Routes.newsEdit.method](uri).then(response => {
                this.mapResponse(response.data.news);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            let form = new FormData();
            form.append('id', this.id);
            form.append('title', this.title);
            form.append('article', this.article);
            form.append('published_at', this.published_at);

            this.clearResponseErrors();
            window.axios[Url.Routes.newsSave.method](
                Url.Routes.newsSave.uri,
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
        mapResponse (news) {
            this.id           = news.id;
            this.title        = news.title;
            this.article      = news.article;
            this.published_at = news.publishedAt;
        },
    },
};
</script>