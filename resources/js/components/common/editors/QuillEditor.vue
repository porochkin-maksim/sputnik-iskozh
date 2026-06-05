<template>
    <div class="h-100 position-relative overflow-y-hidden border-bottom">
        <div class="html-editor quill-editor"
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
    </div>
</template>

<script setup>
/**
 * @see https://stackoverflow.com/questions/71468563/quill-editor-wont-display-v-model-in-input-field-vue-3
 * @see https://stackforgeeks.com/blog/how-to-configure-vuequilleditor-rich-editor-using-html-css-in-vue-js-3-framework
 * @see https://www.vuescript.com/quill-editor-3/
 */
import {
    ref,
    watch,
}                      from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    value: {
        type   : String,
        default: '',
    },
});

const emit = defineEmits(['update:value']);

const content     = ref(props.value);
const quill       = ref(null);
const readyToSync = ref(false);
const toolbar     = [
    [{ header: [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ align: [] }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ script: 'sub' }, { script: 'super' }],
    [{ indent: '-1' }, { indent: '+1' }],
    [{ color: [] }, { background: [] }],
    ['link', 'image', 'video'],
    ['blockquote', 'code-block'],
    ['clean'],
];
const showCode    = ref(false);

watch(() => props.value, (value) => {
    content.value = value;
});

const update = () => {
    emit('update:value', content.value);
};

const readyEvent = (instance) => {
    if (!readyToSync.value) {
        setTimeout(() => {
            readyEvent(instance);
        }, 100);

        return;
    }

    quill.value = instance;
    setTimeout(() => {
        quill.value.root.innerHTML = props.value;
    }, 100);
};

readyToSync.value = true;
</script>
