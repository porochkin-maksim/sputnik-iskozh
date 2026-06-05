<template>
    <div class="h-100 position-relative overflow-y-hidden border-bottom">
        <div class="html-editor h-100 overflow-y-hidden">
            <Codemirror
                v-model:value="content"
                :options="cmOptions"
                @change="onChange"
            />
        </div>
    </div>
</template>

<script setup>
/**
 * @see https://www.npmjs.com/package/codemirror-editor-vue3
 */
import {
    ref,
    watch,
}                 from 'vue';
import Codemirror from 'codemirror-editor-vue3';

import 'codemirror/addon/display/placeholder.js';
import 'codemirror/mode/php/php.js';

const props = defineProps({
    value: String,
});

const emit = defineEmits(['update:value']);

const content   = ref(props.value ?? '');
const cmOptions = {
    mode: 'text/html',
};

watch(() => props.value, (value) => {
    content.value = value ?? '';
});

const onChange = () => {
    emit('update:value', content.value);
};
</script>
