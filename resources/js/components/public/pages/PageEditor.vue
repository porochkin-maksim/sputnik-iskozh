<template>
    <button class="btn btn-danger"
            @click="editMode=true">
        <i class="fa fa-pencil"></i>&nbsp;Редактировать страницу
    </button>
    <wrapper v-if="editMode && template && loaded"
             :container-class="'w-100'"
             @close="editMode=false"
    >
        <div class="container-fluid vh-85">
            <php-editor v-model:value="content" />
            <div class="d-flex justify-content-between bg-white p-2">
                <button class="btn btn-success"
                        @click="editMode=false">
                    Закрыть
                </button>
                <button class="btn btn-danger"
                        @click="saveContent">
                    Сохранить
                </button>
            </div>
        </div>
    </wrapper>
</template>

<script>
import Url        from '../../../utils/Url.js';
import Wrapper    from '../common/Wrapper.vue';
import PhpEditor  from '../common/editors/PhpEditor.vue';
import HtmlEditor from '../common/editors/HtmlEditor.vue';

export default {
    name      : 'PageEditor',
    components: {
        HtmlEditor,
        PhpEditor,
        Wrapper,
    },
    props     : {
        template: String,
    },
    created () {
        this.loadContent();
    },
    data () {
        return {
            Url,
            content : null,
            loaded  : false,
            editMode: false,
        };
    },
    methods: {
        loadContent () {
            window.axios[Url.Routes.templateGet.method](Url.Routes.templateGet.uri, {
                template: this.template,
            }).then(response => {
                this.content = response.data;
                this.loaded  = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveContent () {
            if (!confirm('Точно совершить эту опасную операцию?')) {
                return;
            }
            window.axios[Url.Routes.templateUpdate.method](Url.Routes.templateUpdate.uri, {
                template: this.template,
                content : this.content,
            }).then(response => {

            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>