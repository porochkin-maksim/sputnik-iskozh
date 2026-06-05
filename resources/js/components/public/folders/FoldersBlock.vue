<template>
    <page-template>
        <template v-slot:main>
            <div class="public-folders-block" :class="{ 'public-folders-block--readonly': !edit }">
                <div class="public-folders-block__toolbar">
                    <div class="public-folders-block__nav">
                        <a v-if="selectedFolder && selectedFolder.name"
                       class="public-folders-block__breadcrumb"
                       @click="exitFolder(selectedFolder)">
                        <i class="fa fa-folder-open"></i>
                        <span class="public-folders-block__breadcrumb-separator"></span>
                        <span class="public-folders-block__breadcrumb-value">{{ selectedFolder.name }}</span>
                    </a>
                    </div>
                    <div v-if="edit" class="public-folders-block__controls">
                        <button class="btn btn-outline-success btn-sm public-folders-block__action"
                                @click="toggleFolderFormAction(null)">
                            добавить папку
                        </button>
                        <button class="btn btn-outline-success btn-sm public-folders-block__action"
                                @click="chooseFile()">
                            загрузить файл
                        </button>
                        <template v-if="movedFileId">
                            <button class="btn btn-outline-success btn-sm public-folders-block__action"
                                    @click="pasteFile()">
                                <i class="fa fa-clipboard"></i>&nbsp;Вставить
                            </button>
                        </template>
                    </div>
                </div>
                <input class="d-none"
                       type="file"
                       accept="*/*"
                       ref="fileElem"
                       multiple
                       @change="uploadFile">
                <input class="d-none"
                       type="file"
                       accept="*/*"
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
                                                :disabled="!folderName">
                                            {{ folderId ? 'Сохранить' : 'Создать' }}
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
                                                :disabled="!fileName">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </wrapper>
                    </div>
                </template>

                <div v-if="folders.length" class="public-folders-block__list public-folders-block__list--folders">
                    <template v-for="folder in folders" :key="folder.id">
                        <folder-item
                            :folder="folder"
                            :folder-url="folder.url"
                            :edit="edit"
                            :folder-image="Img.Folder"
                            @open="enterFolder"
                            @rename="toggleFolderFormAction($event.id, $event.name)"
                            @delete="deleteFolder"
                        />
                    </template>
                </div>
                <div v-if="files.length" class="public-folders-block__list public-folders-block__list--files">
                    <template v-for="file in files" :key="file.id">
                        <folder-file-item
                            :file="file"
                            :edit="edit"
                            :file-image="getImgByFile(file)"
                            @rename="toggleFileFormAction($event)"
                            @delete="deleteFile"
                            @copy="copyFile"
                            @replace="replaceFile"
                            @cut="cutFile"
                        />
                    </template>
                </div>
            </div>
        </template>
    </page-template>
</template>

<script setup>
import { onMounted }       from 'vue';
import { Img }             from '@utils/Img.js';
import PageTemplate        from '@components/public/pages/SingleColumnPage.vue';
import Wrapper             from '@common/Wrapper.vue';
import CustomInput         from '@common/form/CustomInput.vue';
import FolderItem          from './FolderItem.vue';
import FolderFileItem      from './FolderFileItem.vue';
import { useFoldersBlock } from './useFoldersBlock.js';

const props = defineProps({
    currentFolder: {
        default: {},
    },
});

const {
          errors,
          fileElem,
          replaceFileElem,
          selectedFolder,
          showFolderForm,
          showFileForm,
          edit,
          folderId,
          folderName,
          fileName,
          folders,
          files,
          movedFileId,
          toggleFolderFormAction,
          toggleFileFormAction,
          createNewFolder,
          deleteFolder,
          enterFolder,
          exitFolder,
          getImgByFile,
          chooseFile,
          uploadFile,
          saveFile,
          deleteFile,
          replaceFile,
          uploadReplacedFile,
          copyFile,
          cutFile,
          pasteFile,
          loadAllFolders,
          loadAllFiles,
      } = useFoldersBlock(props);

onMounted(() => {
    loadAllFolders();
    loadAllFiles();
});
</script>
