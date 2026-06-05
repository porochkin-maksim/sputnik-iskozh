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

<script setup>
import {
    onMounted,
    ref,
}                           from 'vue';
import Wrapper              from '@common/Wrapper.vue';
import PhpEditor            from '@common/editors/PhpEditor.vue';
import {
    ApiTemplateGet,
    ApiTemplateUpdate,
}                           from '@api';
import { useResponseError } from '@composables/useResponseError';

const props = defineProps({
    template: String,
});

const { parseResponseErrors } = useResponseError();

const content  = ref(null);
const loaded   = ref(false);
const editMode = ref(false);

function loadContent () {
    ApiTemplateGet({
        template: props.template,
    }).then(response => {
        content.value = response.data;
        loaded.value  = true;
    }).catch(response => {
        parseResponseErrors(response);
    });
}

function saveContent () {
    if (!confirm('Точно совершить эту опасную операцию?')) {
        return;
    }

    ApiTemplateUpdate({
        template: props.template,
        content : content.value,
    }).catch(response => {
        parseResponseErrors(response);
    });
}

onMounted(() => {
    loadContent();
});
</script>
