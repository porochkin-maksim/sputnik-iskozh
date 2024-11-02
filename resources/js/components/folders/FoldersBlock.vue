<template>
    <page-template>
        <template v-slot:main>
            <div class="btn-group mb-3">
                <a class="btn border"
                   v-if="selectedFolder"
                   @click="exitFolder(selectedFolder)">
                    <i class="fa fa-arrow-left"></i>&nbsp;{{ selectedFolder.name }}
                </a>
                <template v-if="edit">
                    <button class="btn btn-light border"
                            @click="toggleFolderFormAction(null)"
                    >
                        добавить папку
                    </button>
                    <button class="btn btn-light border"
                            @click="chooseFile()"
                    >
                        загрузить файл
                    </button>
                    <template v-if="movedFileId">
                        <button class="btn btn-light border"
                                @click="pasteFile()"
                        >
                            <i class="fa fa-clipboard"></i>&nbsp;Вставить
                        </button>
                    </template>
                </template>
            </div>
            <input class="d-none"
                   type="file"
                   ref="fileElem"
                   multiple
                   @change="uploadFile">
            <input class="d-none"
                   type="file"
                   ref="replaceFileElem"
                   multiple
                   @change="uploadReplacedFile">

            <template v-if="edit">
                <div v-if="showFolderForm">
                    <wrapper @close="showFolderForm=false"
                             :container-class="'w-lg-25 w-md-50 w-100'">
                        <div class="container-fluid">
                            <div class="card form">
                                <div class="card-header">
                                    Укажите название папки
                                </div>
                                <div class="card-body">
                                    <custom-input v-model="folderName"
                                                  :errors="errors.folderName"
                                                  :required="true"
                                    />
                                </div>
                                <div class="card-footer d-flex justify-content-end">
                                    <button class="btn btn-sm btn-success"
                                            @click="createNewFolder"
                                            :disabled="!folderName"
                                    >
                                        {{ this.folderId ? 'Сохранить' : 'Создать' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </wrapper>
                </div>
                <div v-if="showFileForm">
                    <wrapper @close="showFileForm=false"
                             :container-class="'w-lg-25 w-md-50 w-100'">
                        <div class="container-fluid">
                            <div class="card form">
                                <div class="card-header">
                                    Укажите название файла
                                </div>
                                <div class="card-body">
                                    <custom-input v-model="fileName"
                                                  :errors="errors.fileName"
                                                  :required="true"
                                    />
                                </div>
                                <div class="card-footer d-flex justify-content-end">
                                    <button class="btn btn-sm btn-success"
                                            @click="saveFile"
                                            :disabled="!fileName"
                                    >
                                        Сохранить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </wrapper>
                </div>
            </template>

            <div class="d-flex flex-column flex-wrap">
                <template v-for="folder in folders">
                    <div class="folder-item">
                        <template v-if="edit">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border"
                                        type="button"
                                        :id="'folder-'+folder.id"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu"
                                    :aria-labelledby="'folder-'+folder.id">
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="toggleFolderFormAction(folder.id, folder.name)"><i class="fa fa-pencil"></i>&nbsp;Переименовать</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="deleteFolder(folder.id)"><i class="fa fa-trash"></i>&nbsp;Удалить</a>
                                    </li>
                                </ul>
                            </div>
                        </template>
                        <template v-else>
                            <div class="dropdown opacity-0">
                                <button class="btn btn-sm">...</button>
                            </div>
                        </template>
                        <div class="folder-image"
                             :style="{backgroundImage: 'url('+Img.Folder+')'}"
                             @click="enterFolder(folder)">
                        </div>
                        <div class="name" @click="enterFolder(folder)">
                            {{ folder.name }}
                        </div>
                    </div>
                </template>
            </div>
            <div class="d-flex flex-column flex-wrap">
                <template v-for="file in files">
                    <div class="folder-item">
                        <template v-if="edit">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border"
                                        type="button"
                                        :id="'file-'+file.id"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu"
                                    :aria-labelledby="'file-'+file.id">
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="toggleFileFormAction(file)"><i class="fa fa-pencil"></i>&nbsp;Переименовать</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="deleteFile(file.id)"><i class="fa fa-trash"></i>&nbsp;Удалить</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="copyFile(file.id)"><i class="fa fa-copy"></i>&nbsp;Скопировать</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="replaceFile(file.id)"><i class="fa fa-refresh"></i>&nbsp;Заменить</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           href="#"
                                           @click="cutFile(file.id)"><i class="fa fa-cut"></i>&nbsp;Вырезать</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           :href="file.url"
                                           :download="file.name"><i class="fa fa-download"></i>&nbsp;Скачать</a></li>
                                </ul>
                            </div>
                        </template>
                        <template v-else>
                            <div class="dropdown">
                                <a class="btn btn-sm btn-light border"
                                   :href="file.url"
                                   :download="file.name">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </template>

                        <template v-if="file.isImage">
                            <a class="folder-image"
                               data-lightbox="files"
                               target="_blank"
                               :style="{backgroundImage: 'url('+getImgByFile(file)+')'}"
                               :href="file.url"
                               :data-title="file.name"
                            >
                            </a>
                            <a class="name"
                               data-lightbox="files"
                               target="_blank"
                               :href="file.url"
                               :data-title="file.name"
                            >
                                {{ file.name }}
                            </a>
                        </template>
                        <template v-else>
                            <a class="folder-image"
                               target="_blank"
                               :style="{backgroundImage: 'url('+getImgByFile(file)+')'}"
                               :href="file.url"
                               :data-title="file.name"
                            >
                            </a>
                            <a class="name"
                               target="_blank"
                               :href="file.url"
                               :data-title="file.name">
                                {{ file.name }}
                            </a>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </page-template>
</template>

<script>
import Url           from '../../utils/Url.js';
import Img           from '../../utils/Img.js';
import ResponseError from '../../mixin/ResponseError.js';
import PageTemplate  from '../pages/SingleColumnPage.vue';
import Wrapper       from '../common/Wrapper.vue';
import CustomInput   from '../common/form/CustomInput.vue';

export default {
    name      : 'FoldersBlock',
    components: {
        CustomInput,
        Wrapper,
        PageTemplate,
        FileList,
    },
    mixins    : [
        ResponseError,
    ],
    props     : {
        currentFolder: {
            default: {},
        },
    },
    created () {
        if (this.currentFolder) {
            this.selectedFolder = this.currentFolder;
            this.parentId       = this.currentFolder.id;
        }
        this.loadFoldersList();
        this.loadFilesList();
    },
    data () {
        return {
            routeState    : 0,
            selectedFolder: null,
            Img           : Img,

            showFolderForm: false,
            showFileForm  : false,
            edit          : false,

            folderId  : null,
            folderName: null,
            parentId  : null,

            file    : null,
            fileName: null,

            folders: [],
            files  : [],

            copiedFileId : null,
            cutedFileId  : null,
            replaceFileId: null,
        };
    },
    methods : {
        /**
         * папки
         */
        toggleFolderFormAction (id = null, name = 'Новая папка') {
            if (!this.edit) {
                return;
            }
            this.folderId       = id;
            this.folderName     = name;
            this.showFolderForm = !this.showFolderForm;
        },
        toggleFileFormAction (file) {
            if (!this.edit) {
                return;
            }
            this.file         = file;
            this.fileName     = file ? this.getFileName(file) : null;
            this.showFileForm = !this.showFileForm;
        },
        createNewFolder () {
            window.axios[Url.Routes.foldersSave.method](Url.Routes.foldersSave.uri, {
                id       : this.folderId,
                parent_id: this.parentId,
                name     : this.folderName,
            }).then(response => {
                this.loadFoldersList();
                this.toggleFolderFormAction();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        loadFoldersList () {
            window.axios[Url.Routes.foldersList.method](Url.Routes.foldersList.uri, {
                params: {
                    parent_id: this.parentId,
                },
            }).then(response => {
                this.folders = response.data.folders;
                this.edit    = response.data.edit;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        deleteFolder (id) {
            if (!confirm('Удалить папку и все папки и файлы в ней?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.foldersDelete, {
                id: id,
            });

            window.axios[Url.Routes.foldersDelete.method](uri).then(response => {
                this.loadFoldersList();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getFolderUrl (uid) {
            uid = uid ? uid : '';
            return Url.Generator.makeUri(Url.Routes.filesIndex, {
                folder: uid,
            });
        },
        enterFolder (folder) {
            this.selectedFolder = folder;
            this.parentId       = folder.id;
            this.changeFolder(folder.uid);
        },
        async exitFolder (folder) {
            if (folder.parentId) {
                let uri = Url.Generator.makeUri(Url.Routes.foldersShow, {
                    id: folder.parentId,
                });

                await window.axios[Url.Routes.foldersShow.method](uri).then(response => {
                    this.selectedFolder = response.data.folder;
                    this.parentId       = response.data.folder.id;
                    this.edit           = response.data.edit;

                    this.changeFolder(response.data.folder.uid);
                }).catch(response => {
                    this.parseResponseErrors(response);
                });
            }
            else {
                this.selectedFolder = null;
                this.parentId       = null;
                this.changeFolder();
            }
        },

        changeFolder (uid = '') {
            let uri = this.getFolderUrl(uid);
            window.history.pushState({ state: this.routeState++ }, '', uri);

            this.folders = [];
            this.files   = [];
            this.loadFoldersList();
            this.loadFilesList();
        },

        /**
         * файлы
         */
        loadFilesList () {
            window.axios[Url.Routes.filesList.method](Url.Routes.filesList.uri, {
                params: {
                    parent_id: this.parentId ? this.parentId : '',
                    sort_by  : 'name',
                },
            }).then(response => {
                this.files = response.data.files;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getImgByFile (file) {
            if (file.isImage) {
                return file.url;
            }

            switch (file.ext) {
                case 'pdf':
                    return this.Img.PDF;
                case 'doc':
                case 'docx':
                    return this.Img.Word;
                case 'xls':
                case 'xlsx':
                    return this.Img.Excel;
                default:
                    return this.Img.Default;
            }
        },
        chooseFile () {
            this.$refs.fileElem.click();
        },
        getFileName (file) {
            return file?.name.replace('.' + file?.ext, '');
        },
        uploadFile (event) {
            let form = new FormData();
            for (let i = 0; i < event.target.files.length; i++) {
                form.append(event.target.files[i].name, event.target.files[i]);
            }
            form.append('parent_id', this.parentId);

            let uri = Url.Generator.makeUri(Url.Routes.filesStore);
            window.axios[Url.Routes.filesStore.method](
                uri,
                form,
            ).then(response => {
                this.loadFilesList();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveFile () {
            window.axios[Url.Routes.filesSave.method](Url.Routes.filesSave.uri, {
                id  : this.file.id,
                name: this.fileName + '.' + this.file.ext,
            }).then(response => {
                this.toggleFileFormAction(null);
                this.loadFilesList();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        deleteFile (id) {
            if (!confirm('Удалить файл?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.filesDelete, {
                id: id,
            });

            window.axios[Url.Routes.filesDelete.method](uri).then(response => {
                this.loadFilesList();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        replaceFile (id) {
            this.replaceFileId = id;
            this.$refs.replaceFileElem.click();
        },
        uploadReplacedFile (event) {
            let form = new FormData();
            form.append('file', event.target.files[0]);
            form.append('id', this.replaceFileId);

            window.axios[Url.Routes.filesReplace.method](
                Url.Routes.filesReplace.uri,
                form,
            ).then(response => {
                this.loadFilesList();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        copyFile (id) {
            this.cutedFileId  = null;
            this.copiedFileId = id;
        },
        cutFile (id) {
            this.copiedFileId = null;
            this.cutedFileId  = id;
        },
        pasteFile () {
            window.axios[Url.Routes.filesMove.method](Url.Routes.filesMove.uri, {
                file  : this.movedFileId,
                folder: this.parentId,
                type  : this.moveType,
            }).then(response => {
                this.loadFilesList();
                if (response.data) {
                    this.copiedFileId = null;
                    this.cutedFileId  = null;
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        movedFileId () {
            return this.copiedFileId ? this.copiedFileId : this.cutedFileId;
        },
        moveType () {
            return this.copiedFileId ? 'copy' : 'cut';
        },
    },
};
</script>