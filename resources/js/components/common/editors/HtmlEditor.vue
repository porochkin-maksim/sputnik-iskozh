<template>
    <div class="html-editor h-100 d-flex flex-column border rounded">
        <html-editor-toolbar v-if="editor"
                             :editor="editor" />

        <div class="editor-container flex-grow-1 overflow-y-auto p-2"
             v-if="editor">
            <editor-content :editor="editor" />
        </div>
    </div>
</template>

<script setup>
import {
    useEditor,
    EditorContent,
}                        from '@tiptap/vue-3';
import StarterKit        from '@tiptap/starter-kit';
import Underline         from '@tiptap/extension-underline';
import Link              from '@tiptap/extension-link';
import Image             from '@tiptap/extension-image';
import TextAlign         from '@tiptap/extension-text-align';
import Blockquote        from '@tiptap/extension-blockquote';
import Code              from '@tiptap/extension-code';
import CodeBlock         from '@tiptap/extension-code-block';
import HorizontalRule    from '@tiptap/extension-horizontal-rule';
import {
    watch,
    onBeforeUnmount,
}                        from 'vue';
import HtmlEditorToolbar from './HtmlEditorToolbar.vue';

const props = defineProps({
    value: {
        type   : String,
        default: '',
    },
});

const emit = defineEmits(['update:value']);

const editor = useEditor({
    content    : props.value,
    extensions : [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3],
            },
            // Отключаем дублирующие расширения, которые будут добавлены отдельно
            blockquote    : false,
            code          : false,
            codeBlock     : false,
            horizontalRule: false,
        }),
        Underline,
        Link.configure({
            openOnClick   : false,
            HTMLAttributes: {
                rel   : 'noopener noreferrer',
                target: '_blank',
            },
        }),
        Image.configure({
            inline        : true,
            HTMLAttributes: {
                class: 'img-fluid',
            },
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        Blockquote,
        Code,
        CodeBlock,
        HorizontalRule,
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none p-3 focus:outline-none',
        },
    },
    onUpdate   : ({ editor }) => {
        emit('update:value', editor.getHTML());
    },
});

// Синхронизация с пропсом
watch(() => props.value, (newValue) => {
    if (editor.value && newValue !== editor.value.getHTML()) {
        editor.value.commands.setContent(newValue, false);
    }
});

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>

<style scoped>
.html-editor {
    background : #fff;
}

.editor-container {
    min-height : 200px;
}

:deep(.tiptap) {
    outline : none;
}

:deep(.tiptap p) {
    margin : 0.5em 0;
}

:deep(.tiptap h1) {
    font-size : 2em;
    margin    : 0.5em 0;
}

:deep(.tiptap h2) {
    font-size : 1.5em;
    margin    : 0.5em 0;
}

:deep(.tiptap h3) {
    font-size : 1.17em;
    margin    : 0.5em 0;
}

:deep(.tiptap ul),
:deep(.tiptap ol) {
    padding-left : 1.5em;
    margin       : 0.5em 0;
}

:deep(.tiptap a) {
    color           : #0d6efd;
    text-decoration : underline;
}

:deep(.tiptap img) {
    max-width : 100%;
    height    : auto;
}

:deep(.tiptap blockquote) {
    border-left  : 4px solid #dee2e6;
    padding-left : 1rem;
    margin-left  : 0;
    color        : #6c757d;
}

:deep(.tiptap code) {
    background-color : #f8f9fa;
    padding          : 0.2em 0.4em;
    border-radius    : 3px;
    font-family      : monospace;
}

:deep(.tiptap pre) {
    background-color : #f8f9fa;
    padding          : 0.75rem;
    border-radius    : 4px;
    overflow-x       : auto;
}
</style>