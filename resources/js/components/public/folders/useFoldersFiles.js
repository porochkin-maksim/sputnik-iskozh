import {
    computed,
    ref,
    watch,
}                           from 'vue';
import { Img }              from '@utils/Img.js';
import {
    ApiFilesDelete,
    ApiFilesList,
    ApiFilesMove,
    ApiFilesReplace,
    ApiFilesStore,
    ApiFilesSave,
}                           from '@api';
import { useResponseError } from '@composables/useResponseError';

export function useFoldersFiles (navigation) {
    const { parseResponseErrors } = useResponseError();

    const fileElem        = ref(null);
    const replaceFileElem = ref(null);
    const showFileForm    = ref(false);
    const file            = ref(null);
    const fileName        = ref(null);
    const files           = ref([]);
    const copiedFileId    = ref(null);
    const cutedFileId     = ref(null);
    const replaceFileId   = ref(null);

    const toggleFileFormAction = (selectedFile) => {
        if (!navigation.edit.value) {
            return;
        }
        file.value         = selectedFile;
        fileName.value     = selectedFile ? getFileName(selectedFile) : null;
        showFileForm.value = !showFileForm.value;
    };

    const loadFilesList = () => {
        ApiFilesList({
            parent_id: navigation.parentId.value ? navigation.parentId.value : '',
            sort_by  : 'name',
        }).then(response => {
            files.value = response.data.files;
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const loadAllFiles = () => {
        loadFilesList();
    };

    watch(() => navigation.parentId.value, () => {
        loadFilesList();
    });

    const getImgByFile = (selectedFile) => {
        if (selectedFile.isImage) {
            return selectedFile.url;
        }
        switch (selectedFile.ext) {
            case 'pdf':
                return Img.PDF;
            case 'doc':
            case 'docx':
                return Img.Word;
            case 'xls':
            case 'xlsx':
                return Img.Excel;
            default:
                return Img.Default;
        }
    };

    const chooseFile = () => {
        fileElem.value?.click();
    };

    const getFileName = (selectedFile) => selectedFile?.name.replace(`.${selectedFile?.ext}`, '');

    const uploadFile = (event) => {
        const form = new FormData();
        for (const uploadedFile of event.target.files) {
            form.append(uploadedFile.name, uploadedFile);
        }
        form.append('parent_id', navigation.parentId.value);

        ApiFilesStore({}, form).then(() => {
            loadFilesList();
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const saveFile = () => {
        ApiFilesSave({}, {
            id  : file.value.id,
            name: `${fileName.value}.${file.value.ext}`,
        }).then(() => {
            toggleFileFormAction(null);
            loadFilesList();
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const deleteFile = (id) => {
        if (!confirm('Удалить файл?')) {
            return;
        }
        ApiFilesDelete(id).then(() => {
            loadFilesList();
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const replaceFile = (id) => {
        replaceFileId.value = id;
        replaceFileElem.value?.click();
    };

    const uploadReplacedFile = (event) => {
        const form = new FormData();
        form.append('file', event.target.files[0]);
        form.append('id', replaceFileId.value);

        ApiFilesReplace({}, form).then(() => {
            loadFilesList();
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const copyFile = (id) => {
        cutedFileId.value  = null;
        copiedFileId.value = id;
    };

    const cutFile = (id) => {
        copiedFileId.value = null;
        cutedFileId.value  = id;
    };

    const pasteFile = () => {
        ApiFilesMove({}, {
            file  : movedFileId.value,
            folder: navigation.parentId.value,
            type  : moveType.value,
        }).then(response => {
            loadFilesList();
            if (response.data) {
                copiedFileId.value = null;
                cutedFileId.value  = null;
            }
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const movedFileId = computed(() => copiedFileId.value ? copiedFileId.value : cutedFileId.value);
    const moveType    = computed(() => copiedFileId.value ? 'copy' : 'cut');

    return {
        fileElem,
        replaceFileElem,
        showFileForm,
        file,
        fileName,
        files,
        copiedFileId,
        cutedFileId,
        replaceFileId,
        movedFileId,
        moveType,
        toggleFileFormAction,
        loadFilesList,
        loadAllFiles,
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
    };
}
