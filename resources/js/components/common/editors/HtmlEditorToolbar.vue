<template>
    <div class="editor-toolbar border-bottom p-2 bg-light d-flex flex-wrap gap-1">
        <!-- Форматирование текста -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleBold().run()"
                :class="{ active: editor.isActive('bold') }" type="button" title="Жирный">
            <i class="fa fa-bold"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleItalic().run()"
                :class="{ active: editor.isActive('italic') }" type="button" title="Курсив">
            <i class="fa fa-italic"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleUnderline().run()"
                :class="{ active: editor.isActive('underline') }" type="button" title="Подчёркнутый">
            <i class="fa fa-underline"></i>
        </button>

        <div class="vr mx-1"></div>

        <!-- Заголовки -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                :class="{ active: editor.isActive('heading', { level: 1 }) }" type="button" title="Заголовок 1">H1</button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="{ active: editor.isActive('heading', { level: 2 }) }" type="button" title="Заголовок 2">H2</button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="{ active: editor.isActive('heading', { level: 3 }) }" type="button" title="Заголовок 3">H3</button>

        <div class="vr mx-1"></div>

        <!-- Списки -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ active: editor.isActive('bulletList') }" type="button" title="Маркированный список">
            <i class="fa fa-list-ul"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ active: editor.isActive('orderedList') }" type="button" title="Нумерованный список">
            <i class="fa fa-list-ol"></i>
        </button>

        <div class="vr mx-1"></div>

        <!-- Выравнивание -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().setTextAlign('left').run()"
                :class="{ active: editor.isActive({ textAlign: 'left' }) }" type="button" title="Выровнять влево">
            <i class="fa fa-align-left"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().setTextAlign('center').run()"
                :class="{ active: editor.isActive({ textAlign: 'center' }) }" type="button" title="По центру">
            <i class="fa fa-align-center"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().setTextAlign('right').run()"
                :class="{ active: editor.isActive({ textAlign: 'right' }) }" type="button" title="Выровнять вправо">
            <i class="fa fa-align-right"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().setTextAlign('justify').run()"
                :class="{ active: editor.isActive({ textAlign: 'justify' }) }" type="button" title="По ширине">
            <i class="fa fa-align-justify"></i>
        </button>

        <!-- Ссылки и изображения -->
        <button class="btn btn-sm btn-outline-secondary" @click="setLink" type="button"
                :class="{ active: editor.isActive('link') }" title="Вставить/редактировать ссылку">
            <i class="fa fa-link"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="setImage" type="button" title="Вставить изображение">
            <i class="fa fa-image"></i>
        </button>

        <div class="vr mx-1"></div>

        <!-- Цитаты и код -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleBlockquote().run()"
                :class="{ active: editor.isActive('blockquote') }" type="button" title="Цитата">
            <i class="fa fa-quote-right"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleCode().run()"
                :class="{ active: editor.isActive('code') }" type="button" title="Инлайн-код">
            <i class="fa fa-code"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().toggleCodeBlock().run()"
                :class="{ active: editor.isActive('codeBlock') }" type="button" title="Блок кода">
            <i class="fa fa-terminal"></i>
        </button>

    </div>
    <div class="editor-toolbar border-bottom p-2 bg-light d-flex flex-wrap gap-1">

        <!-- Горизонтальная линия, очистка -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().setHorizontalRule().run()"
                type="button" title="Горизонтальная линия">
            <i class="fa fa-minus"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().unsetAllMarks().clearNodes().run()"
                type="button" title="Очистить форматирование">
            <i class="fa fa-eraser"></i>
        </button>

        <div class="vr mx-1"></div>

        <!-- Undo/Redo -->
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().undo().run()" type="button" title="Отменить">
            <i class="fa fa-undo"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" @click="editor.chain().focus().redo().run()" type="button" title="Повторить">
            <i class="fa fa-repeat"></i>
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    editor: {
        type: Object,
        required: true,
    },
});

const setLink = () => {
    const url = window.prompt('Введите URL ссылки:');
    if (url) {
        props.editor.chain().focus().setLink({ href: url }).run();
    } else {
        props.editor.chain().focus().unsetLink().run();
    }
};

const setImage = () => {
    const url = window.prompt('Введите URL изображения:');
    if (url) {
        props.editor.chain().focus().setImage({ src: url }).run();
    }
};
</script>

<style scoped>
.editor-toolbar {
    border-bottom: 1px solid #dee2e6;
}

.btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.btn.active:hover {
    background-color: #0b5ed7;
}

.vr {
    height: 31px;
    align-self: center;
}
</style>