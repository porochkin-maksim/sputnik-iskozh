<template>
    <div class="file">
        <!-- Режим редактирования -->
        <template v-if="modeEdit">
            <div class="form input-group input-group-sm">
                <button class="btn btn-success"
                        @click="save"
                        :disabled="loading"
                        title="Сохранить">
                    <i class="fa fa-save"></i>
                </button>
                <button class="btn btn-success"
                        @click="triggerFileUpload"
                        :disabled="loading"
                        title="Заменить файл">
                    <i class="fa fa-upload"></i>
                </button>
                <button class="btn btn-light border"
                        @click="cancelEdit"
                        :disabled="loading"
                        title="Отмена">
                    <i class="fa fa-window-close"></i>
                </button>
                <custom-input
                    v-model="baseName"
                    :errors="errors.name"
                    placeholder="Название"
                    :disabled="loading"
                    @change="clearError('name')"
                />
            </div>
            <input
                type="file"
                ref="fileInput"
                class="d-none"
                accept="*/*"
                @change="uploadReplacedFile"
            >
        </template>

        <!-- Режим просмотра -->
        <template v-else>
            <div class="d-inline-flex align-items-center">
                <!-- Кнопки управления (если есть права на редактирование) -->
                <template v-if="edit">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-success"
                                @click="enterEdit"
                                title="Редактировать">
                            <i class="fa fa-edit"></i>
                        </button>
                        <template v-if="showUpDownButtons">
                            <button
                                class="btn btn-light border"
                                :disabled="!useUpSort"
                                @click="sortUp(index)"
                                title="Переместить вверх"
                            >
                                <i class="fa fa-arrow-up"></i>
                            </button>
                            <button
                                class="btn btn-light border"
                                :disabled="!useDownSort"
                                @click="sortDown(index)"
                                title="Переместить вниз"
                            >
                                <i class="fa fa-arrow-down"></i>
                            </button>
                        </template>
                        <button class="btn btn-danger"
                                @click="deleteFile"
                                title="Удалить">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </template>

                <!-- Кнопка скачивания -->
                <a
                    class="btn btn-success btn-sm me-2"
                    :href="file.url"
                    :download="file.name"
                    title="Скачать"
                >
                    <i class="fa fa-download"></i>
                </a>

                <!-- Ссылка на файл с иконкой типа -->
                <a
                    :href="file.url"
                    class="name text-nowrap"
                    :data-lightbox="file.isImage ? file.name : null"
                    :data-title="file.isImage ? file.name : null"
                    target="_blank"
                    :title="file.name"
                >
                    <i :class="['fa', fileIcon]"></i>
                    {{ file.name }}
                </a>
            </div>
        </template>
    </div>
</template>

<script>
import Url           from '../../../utils/Url.js';
import CustomInput   from '../../common/form/CustomInput.vue';
import ResponseError from '../../../mixin/ResponseError.js';

export default {
    name      : 'FileItem',
    components: {
        CustomInput,
    },
    mixins    : [ResponseError],
    props     : {
        file       : {
            type    : Object,
            required: true,
        },
        edit       : {
            type   : Boolean,
            default: false,
        },
        index      : {
            type   : Number,
            default: null,
        },
        useUpSort  : {
            type   : Boolean,
            default: false,
        },
        useDownSort: {
            type   : Boolean,
            default: false,
        },
    },
    emits     : ['updated'],
    data () {
        return {
            modeEdit: false,
            loading : false,
            baseName: '',
        };
    },
    computed: {
        // Иконка в зависимости от типа файла
        fileIcon () {
            if (this.file.isImage) {
                return 'fa-file-image-o';
            }
            const ext = this.file.ext?.toLowerCase();
            if (ext === 'pdf') {
                return 'fa-file-pdf-o';
            }
            if (['doc', 'docx'].includes(ext)) {
                return 'fa-file-word-o';
            }
            if (['xls', 'xlsx'].includes(ext)) {
                return 'fa-file-excel-o';
            }
            if (['zip', 'rar', '7z'].includes(ext)) {
                return 'fa-file-archive-o';
            }
            return 'fa-file-o';
        },
        // Показывать ли кнопки сортировки
        showUpDownButtons () {
            return this.useUpSort || this.useDownSort;
        },
    },
    watch   : {
        // При изменении файла (например, после перезагрузки списка) обновляем базовое имя
        file: {
            handler (newFile) {
                this.baseName = this.getBaseName(newFile.name);
            },
            deep     : true,
            immediate: true,
        },
    },
    methods : {
        // Получить имя файла без расширения
        getBaseName (fullName) {
            return fullName.replace(`.${this.file.ext}`, '');
        },
        enterEdit () {
            this.modeEdit = true;
        },
        cancelEdit () {
            this.modeEdit = false;
            this.baseName = this.getBaseName(this.file.name); // сброс к исходному
            this.clearError('name');
        },
        async save () {
            this.loading = true;
            try {
                await window.axios[Url.Routes.newsFileSave.method](Url.Routes.newsFileSave.uri, {
                    id  : this.file.id,
                    name: this.baseName + '.' + this.file.ext,
                });
                this.showSuccess('Файл сохранён');
                this.$emit('updated', true);
                this.modeEdit = false;
            }
            catch (err) {
                this.parseResponseErrors(err);
            }
            finally {
                this.loading = false;
            }
        },
        triggerFileUpload () {
            this.$refs.fileInput?.click();
        },
        async uploadReplacedFile (event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('id', this.file.id);

            this.loading = true;
            try {
                await window.axios[Url.Routes.filesReplace.method](
                    Url.Routes.filesReplace.uri,
                    formData,
                );
                this.showSuccess('Файл заменён');
                this.$emit('updated', true);
                // Очищаем input, чтобы можно было выбрать тот же файл повторно
                this.$refs.fileInput.value = '';
            }
            catch (err) {
                this.parseResponseErrors(err);
            }
            finally {
                this.loading = false;
            }
        },
        async deleteFile () {
            if (!confirm('Удалить файл?')) {
                return;
            }

            const uri = Url.Generator.makeUri(Url.Routes.newsFileDelete, {
                id: this.file.id,
            });

            this.loading = true;
            try {
                await window.axios[Url.Routes.newsFileDelete.method](uri);
                this.showSuccess('Файл удалён');
                this.$emit('updated', true);
            }
            catch (err) {
                this.parseResponseErrors(err);
            }
            finally {
                this.loading = false;
            }
        },
        async sortUp (index) {
            const uri    = Url.Generator.makeUri(Url.Routes.filesUp, { id: this.file.id });
            this.loading = true;
            try {
                await window.axios[Url.Routes.filesUp.method](uri, { index });
                this.showSuccess('Порядок изменён');
                this.$emit('updated', true);
            }
            catch (err) {
                this.parseResponseErrors(err);
            }
            finally {
                this.loading = false;
            }
        },
        async sortDown (index) {
            const uri    = Url.Generator.makeUri(Url.Routes.filesDown, { id: this.file.id });
            this.loading = true;
            try {
                await window.axios[Url.Routes.filesDown.method](uri, { index });
                this.showSuccess('Порядок изменён');
                this.$emit('updated', true);
            }
            catch (err) {
                this.parseResponseErrors(err);
            }
            finally {
                this.loading = false;
            }
        },
    },
};
</script>