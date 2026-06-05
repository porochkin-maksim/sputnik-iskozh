<template>
    <div class="file-item public-file-card">
        <div class="body">
            <template v-if="modeEdit">
                <div class="public-file-card__edit">
                    <div class="public-file-card__edit-actions">
                        <button class="btn btn-success btn-sm" @click="save">
                            <i class="fa fa-save"></i>
                        </button>
                        <button class="btn btn-light border btn-sm" @click="toggleMode">
                            <i class="fa fa-window-close"></i>
                        </button>
                    </div>
                    <custom-input v-model="name"
                                  :errors="errors.name"
                                  :placeholder="'Название'"
                                  :required="false"
                                  @change="clearError('name')"
                    />
                </div>
            </template>
            <template v-else>
                <div class="public-file-card__row">
                    <template v-if="edit">
                        <div class="public-file-card__actions">
                            <button class="btn btn-success btn-sm"
                                    @click="toggleMode"
                            >
                                <i class="fa fa-edit"></i>
                            </button>
                            <template v-if="showUpDownButtons">
                                <button class="btn btn-secondary btn-sm"
                                        :disabled="!useUpSort"
                                        @click="sortUp(index)"
                                >
                                    <i class="fa fa-arrow-up"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm"
                                        :disabled="!useDownSort"
                                        @click="sortDown(index)"
                                >
                                    <i class="fa fa-arrow-down"></i>
                                </button>
                            </template>
                            <button class="btn btn-danger"
                                    @click="deleteFile"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </template>

                    <a class="btn btn-outline-success btn-sm public-file-card__download"
                       :href="file.url"
                       :download="file.name">
                        <i class="fa fa-download"></i>
                    </a>
                    <template v-if="file.isImage">
                        <a :href="file.url"
                           class="name public-file-card__name text-decoration-none"
                           :data-lightbox="file.name"
                           :data-title="file.name"
                           target="_blank">{{ file.name }}</a>
                    </template>
                    <template v-else>
                        <a :href="file.url"
                           class="name public-file-card__name text-decoration-none"
                           target="_blank">{{ file.name }}</a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
    watch,
}                           from 'vue';
import CustomInput          from '@common/form/CustomInput.vue';
import {
    ApiFilesSave,
    ApiFilesDelete,
    ApiFilesUp,
    ApiFilesDown,
}                           from '@api';
import { useResponseError } from '@composables/useResponseError';

const props = defineProps({
    file       : Object,
    edit       : Boolean,
    index      : Number,
    useUpSort  : Boolean,
    useDownSort: Boolean,
});

const emit                    = defineEmits(['updated']);
const { parseResponseErrors } = useResponseError();

const modeEdit = ref(false);
const id       = ref(props.file.id);
const name     = ref(getFileName());

const showUpDownButtons = computed(() => props.useUpSort || props.useDownSort);

function getFileName () {
    return props.file.name.replace('.' + props.file.ext, '');
}

function updatedItem () {
    emit('updated', true);
    modeEdit.value = false;
}

function toggleMode () {
    if (modeEdit.value) {
        save();
    }
    else {
        modeEdit.value = true;
    }
}

function save () {
    ApiFilesSave({}, {
        id  : id.value,
        name: name.value + '.' + props.file.ext,
    }).then(() => {
        updatedItem();
    }).catch(response => {
        parseResponseErrors(response);
    });
}

function deleteFile () {
    if (!confirm('Удалить файл?')) {
        return;
    }

    ApiFilesDelete(id.value).then(() => {
        updatedItem();
    }).catch(response => {
        parseResponseErrors(response);
    });
}

function sortUp (index) {
    ApiFilesUp(id.value, {}, { index }).then(() => {
        updatedItem();
    }).catch(response => {
        parseResponseErrors(response);
    });
}

function sortDown (index) {
    ApiFilesDown(id.value, {}, { index }).then(() => {
        updatedItem();
    }).catch(response => {
        parseResponseErrors(response);
    });
}

watch(() => props.file, () => {
    id.value   = props.file.id;
    name.value = props.file.name.replace('.' + props.file.ext, '');
}, { deep: true });
</script>
