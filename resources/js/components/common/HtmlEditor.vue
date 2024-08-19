<template>
    <div class="h-100 position-relative overflow-y-hidden border-bottom">
        <div class="html-editor h-100 overflow-y-auto">
            <Editor
                v-model="content"
                height="100%"
                @change="onChange"
                api-key="l8vjolgctraqq0x8nxujokup1bcd9x0u7knkh9cl9o08smty"
                :init="{
        toolbar_mode: 'sliding',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        }"
            />
        </div>
    </div>
</template>

<script>
/**
 * @see https://www.tiny.cloud/my-account/integrate/#more
 */
import Editor from '@tinymce/tinymce-vue';

export default {
    name      : 'HtmlEditor',
    components: {
        Editor,
    },
    props     : {
        value: String,
    },
    data () {
        return {
            content: null,
        };
    },
    mounted () {
        this.content = this.value;
    },
    created () {
        this.content = this.value;
    },
    methods: {
        onChange () {
            console.log('onChange');
            this.$emit('update:value', this.content);
        },
    },
};
</script>