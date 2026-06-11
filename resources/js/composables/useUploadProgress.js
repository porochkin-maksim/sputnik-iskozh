import { ref } from 'vue';

export function useUploadProgress () {
    const uploadProgress = ref(0);
    const isUploading    = ref(false);

    const startUpload = () => {
        isUploading.value    = true;
        uploadProgress.value = 0;
    };

    const onProgress = (progressEvent) => {
        if (progressEvent.total) {
            uploadProgress.value = Math.round((progressEvent.loaded / progressEvent.total) * 100);
        }
    };

    const finishUpload = () => {
        isUploading.value    = false;
        uploadProgress.value = 0;
    };

    return {
        uploadProgress,
        isUploading,
        startUpload,
        onProgress,
        finishUpload,
    };
}