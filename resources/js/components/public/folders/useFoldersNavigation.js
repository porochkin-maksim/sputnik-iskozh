import {
    ref,
}                           from 'vue';
import {
    ApiFoldersDelete,
    ApiFoldersInfo,
    ApiFoldersList,
    ApiFoldersSave,
    ApiFoldersShow,
}                           from '@api';
import { routeUri }         from '@utils/routeUri.js';
import { useResponseError } from '@composables/useResponseError';
import { useBreadcrumbHtml } from './useBreadcrumbHtml.js';

export function useFoldersNavigation (props) {
    const { errors, parseResponseErrors } = useResponseError();
    const { setBreadcrumbHtml } = useBreadcrumbHtml();

    const routeState     = ref(0);
    const selectedFolder = ref(null);
    const showFolderForm = ref(false);
    const edit           = ref(false);
    const folderId       = ref(null);
    const folderName     = ref(null);
    const parentId       = ref(null);
    const folders        = ref([]);

    if (props.currentFolder) {
        selectedFolder.value = props.currentFolder;
        parentId.value       = props.currentFolder.id;
    }

    const toggleFolderFormAction = (id = null, name = 'Новая папка') => {
        if (!edit.value) {
            return;
        }
        folderId.value       = id;
        folderName.value     = name;
        showFolderForm.value = !showFolderForm.value;
    };

    const loadFoldersList = () => {
        ApiFoldersList({
            parent_id: parentId.value,
        }).then(response => {
            folders.value = response.data.folders;
            edit.value    = response.data.edit;
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const createNewFolder = () => {
        ApiFoldersSave({}, {
            id       : folderId.value,
            parent_id: parentId.value,
            name     : folderName.value,
        }).then(() => {
            loadFoldersList();
            toggleFolderFormAction();
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const deleteFolder = (id) => {
        if (!confirm('Удалить папку и все папки и файлы в ней?')) {
            return;
        }
        ApiFoldersDelete(id).then(() => {
            loadFoldersList();
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const getFolderUrl = (uid) => routeUri('filesIndex', { folder: uid ? uid : '' });

    const enterFolder = (folder) => {
        selectedFolder.value = folder;
        parentId.value       = folder.id;
        changeFolder(folder.uid);
    };

    const exitFolder = (folder) => {
        if (folder.parentId) {
            ApiFoldersShow(folder.parentId).then(response => {
                selectedFolder.value = response.data.folder;
                parentId.value       = response.data.folder.id;
                edit.value           = response.data.edit;
                changeFolder(response.data.folder.uid);
            }).catch(response => {
                parseResponseErrors(response);
            });
        }
        else {
            selectedFolder.value = null;
            parentId.value       = null;
            changeFolder();
        }
    };

    const changeFolder = (uid = '') => {
        const uri = getFolderUrl(uid);
        window.history.pushState({ state: routeState.value++ }, '', uri);
        folders.value = [];
        loadFoldersList();
    };

    const loadFolderPath = () => {
        const id = selectedFolder.value?.id;
        if (!id) {
            return;
        }
        ApiFoldersInfo(id).then(response => {
            setBreadcrumbHtml(response.data.breadcrumbs);
        }).catch(response => {
            parseResponseErrors(response);
        });
    };

    const loadAllFolders = () => {
        loadFoldersList();
        loadFolderPath();
    };

    return {
        errors,
        selectedFolder,
        showFolderForm,
        edit,
        folderId,
        folderName,
        parentId,
        folders,
        toggleFolderFormAction,
        loadFoldersList,
        createNewFolder,
        deleteFolder,
        getFolderUrl,
        enterFolder,
        exitFolder,
        changeFolder,
        loadFolderPath,
        loadAllFolders,
    };
}
