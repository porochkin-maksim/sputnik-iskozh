<template>
    <div class="h-100 position-relative">
        <div class="html-editor"
             :class="[showCode ? 'd-none' : '']">
            <quill-editor :toolbar="toolbar"
                          v-model:content="content"
                          contentType="html"
                          @update:content="update"
                          @ready="readyEvent" />
        </div>
        <textarea class="html-editor form-control"
                  :class="[!showCode ? 'd-none' : '']"
                  @change="update"
                  v-model="content"></textarea>
        <button class="badge bg-secondary d-flex justify-content-center mb-3 position-absolute right-0 bottom-0 z-1"
                @click="switchMode">{{ switchModeText }}
        </button>
    </div>
</template>

<script>
/**
 * @see https://stackoverflow.com/questions/71468563/quill-editor-wont-display-v-model-in-input-field-vue-3
 * @see https://stackforgeeks.com/blog/how-to-configure-vuequilleditor-rich-editor-using-html-css-in-vue-js-3-framework
 * @see https://www.vuescript.com/quill-editor-3/
 */
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

export default {
    name      : 'HtmlEditor',
    components: {
        QuillEditor,
    },
    props     : {
        value: { type: String, default: '' },
    },
    data () {
        return {
            editor     : null,
            content    : '',
            toolbar    : [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ align: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ color: [] }, { background: [] }],
                ['link', 'image', 'video'],
                ['blockquote', 'code-block'],
                ['clean'],
            ],
            quill      : null,
            readyToSync: false,

            showCode: false,
        };
    },
    mounted () {
        this.readyToSync = true;
        this.content     = this.value;
    },
    created () {
        this.content = this.value;
    },
    methods : {
        update () {
            this.$emit('update:value', this.content);
        },
        readyEvent (quill) {
            if (!this.readyToSync) {
                setTimeout(() => {
                    this.readyEvent(quill);
                }, 100);
            }
            else {
                this.quill = quill;
                setTimeout(() => {
                    this.quill.root.innerHTML = this.value;
                }, 100);
            }
        },
        switchMode () {
            this.showCode = !this.showCode;
            this.quill.root.innerHTML = this.content;
        },
    },
    computed: {
        switchModeText () {
            return this.showCode ? 'Скрыть код' : 'Показать код';
        },
    },
};
</script>