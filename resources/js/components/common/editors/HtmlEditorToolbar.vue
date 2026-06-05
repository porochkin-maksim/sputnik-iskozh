<template>
    <div class="editor-toolbar border-bottom p-2 bg-light d-flex flex-wrap gap-1">
        <template v-for="(group, groupIndex) in toolbarGroups" :key="groupIndex">
            <button
                v-for="button in group"
                :key="button.title"
                class="btn btn-sm btn-outline-secondary"
                :class="{ active: button.active?.() }"
                type="button"
                :title="button.title"
                @click="button.action()"
            >
                <i v-if="button.icon" :class="button.icon"></i>
                <template v-else>{{ button.label }}</template>
            </button>

            <div v-if="groupIndex < toolbarGroups.length - 1" class="vr mx-1"></div>
        </template>
    </div>
</template>

<script setup>
const props = defineProps({
    editor: {
        type    : Object,
        required: true,
    },
});

const setLink = () => {
    const url = window.prompt('Введите URL ссылки:');
    if (url) {
        props.editor.chain().focus().setLink({ href: url }).run();
    }
    else {
        props.editor.chain().focus().unsetLink().run();
    }
};

const setImage = () => {
    const url = window.prompt('Введите URL изображения:');
    if (url) {
        props.editor.chain().focus().setImage({ src: url }).run();
    }
};

const toolbarGroups = [
    [
        {
            title : 'Жирный',
            icon  : 'fa fa-bold',
            active: () => props.editor.isActive('bold'),
            action: () => props.editor.chain().focus().toggleBold().run(),
        },
        {
            title : 'Курсив',
            icon  : 'fa fa-italic',
            active: () => props.editor.isActive('italic'),
            action: () => props.editor.chain().focus().toggleItalic().run(),
        },
        {
            title : 'Подчёркнутый',
            icon  : 'fa fa-underline',
            active: () => props.editor.isActive('underline'),
            action: () => props.editor.chain().focus().toggleUnderline().run(),
        },
    ],
    [
        {
            title : 'Заголовок 1',
            label : 'H1',
            active: () => props.editor.isActive('heading', { level: 1 }),
            action: () => props.editor.chain().focus().toggleHeading({ level: 1 }).run(),
        },
        {
            title : 'Заголовок 2',
            label : 'H2',
            active: () => props.editor.isActive('heading', { level: 2 }),
            action: () => props.editor.chain().focus().toggleHeading({ level: 2 }).run(),
        },
        {
            title : 'Заголовок 3',
            label : 'H3',
            active: () => props.editor.isActive('heading', { level: 3 }),
            action: () => props.editor.chain().focus().toggleHeading({ level: 3 }).run(),
        },
    ],
    [
        {
            title : 'Маркированный список',
            icon  : 'fa fa-list-ul',
            active: () => props.editor.isActive('bulletList'),
            action: () => props.editor.chain().focus().toggleBulletList().run(),
        },
        {
            title : 'Нумерованный список',
            icon  : 'fa fa-list-ol',
            active: () => props.editor.isActive('orderedList'),
            action: () => props.editor.chain().focus().toggleOrderedList().run(),
        },
    ],
    [
        {
            title : 'Выровнять влево',
            icon  : 'fa fa-align-left',
            active: () => props.editor.isActive({ textAlign: 'left' }),
            action: () => props.editor.chain().focus().setTextAlign('left').run(),
        },
        {
            title : 'По центру',
            icon  : 'fa fa-align-center',
            active: () => props.editor.isActive({ textAlign: 'center' }),
            action: () => props.editor.chain().focus().setTextAlign('center').run(),
        },
        {
            title : 'Выровнять вправо',
            icon  : 'fa fa-align-right',
            active: () => props.editor.isActive({ textAlign: 'right' }),
            action: () => props.editor.chain().focus().setTextAlign('right').run(),
        },
        {
            title : 'По ширине',
            icon  : 'fa fa-align-justify',
            active: () => props.editor.isActive({ textAlign: 'justify' }),
            action: () => props.editor.chain().focus().setTextAlign('justify').run(),
        },
    ],
    [
        {
            title : 'Вставить/редактировать ссылку',
            icon  : 'fa fa-link',
            active: () => props.editor.isActive('link'),
            action: setLink,
        },
        {
            title : 'Вставить изображение',
            icon  : 'fa fa-image',
            action: setImage,
        },
    ],
    [
        {
            title : 'Цитата',
            icon  : 'fa fa-quote-right',
            active: () => props.editor.isActive('blockquote'),
            action: () => props.editor.chain().focus().toggleBlockquote().run(),
        },
        {
            title : 'Инлайн-код',
            icon  : 'fa fa-code',
            active: () => props.editor.isActive('code'),
            action: () => props.editor.chain().focus().toggleCode().run(),
        },
        {
            title : 'Блок кода',
            icon  : 'fa fa-terminal',
            active: () => props.editor.isActive('codeBlock'),
            action: () => props.editor.chain().focus().toggleCodeBlock().run(),
        },
    ],
    [
        {
            title : 'Горизонтальная линия',
            icon  : 'fa fa-minus',
            action: () => props.editor.chain().focus().setHorizontalRule().run(),
        },
        {
            title : 'Очистить форматирование',
            icon  : 'fa fa-eraser',
            action: () => props.editor.chain().focus().unsetAllMarks().clearNodes().run(),
        },
    ],
    [
        {
            title : 'Отменить',
            icon  : 'fa fa-undo',
            action: () => props.editor.chain().focus().undo().run(),
        },
        {
            title : 'Повторить',
            icon  : 'fa fa-repeat',
            action: () => props.editor.chain().focus().redo().run(),
        },
    ],
];
</script>
