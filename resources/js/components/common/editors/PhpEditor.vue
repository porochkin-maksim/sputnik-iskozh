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

<script>
/**
 * @see https://www.npmjs.com/package/codemirror-editor-vue3
 */
import Codemirror from "codemirror-editor-vue3";

// placeholder
import "codemirror/addon/display/placeholder.js";
// language
import "codemirror/mode/php/php.js";

export default {
    components: {
        Codemirror,
    },
    props     : {
        value: String,
    },
    data () {
        return {
            content: null,
            cmOptions: {
                mode: "text/javascript",
            },
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
            this.$emit('update:value', this.content);
        },
    },
};
</script>