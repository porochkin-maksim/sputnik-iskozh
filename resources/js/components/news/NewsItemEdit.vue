<template>
    <div class="card form"
         :class="alertClass">
        <div class="card-body" v-if="loaded">
            <div class="row">
                <div class="col-lg-9">
                    <div class="">
                        <custom-input v-model="title"
                                      :errors="errors.title"
                                      :placeholder="'Заголовок'"
                                      :required="true"
                                      @change="clearError('title')"
                        />
                    </div>
                    <div class="vh-75">
                        <html-editor v-model:value="article" />
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="">
                        <custom-input v-model="published_at"
                                      :errors="errors.published_at"
                                      :type="'datetime-local'"
                                      :placeholder="'Время публикации'"
                                      :required="true"
                                      @change="clearError('published_at')"
                        />
                    </div>
                    <div class="">
                        <custom-select v-model="category"
                                       :errors="errors.category"
                                       :items="categories"
                                       :required="false"
                                       @change="clearError('category')"
                        />
                    </div>
                    <div class="">
                        <custom-checkbox v-model="lock"
                                         :errors="errors.lock"
                                         :label="'Закрепить на главной'"
                                         :required="true"
                                         @change="clearError('lock')"
                        />
                    </div>
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
            id          : this.modelValue,
            title       : null,
            article     : null,
            published_at: null,
            lock        : null,
            category    : null,
            categories  : [],
            loaded      : false,
        };
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.newsCreate.method](Url.Routes.newsCreate.uri).then(response => {
                this.categories = response.data.categories;
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
                this.categories = response.data.categories;
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
            form.append('is_lock', this.lock);
            form.append('category', parseInt(this.category));

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
            this.lock         = news.isLock;
            this.category     = news.category;

            this.loaded = true;
        },
    },
};
</script>