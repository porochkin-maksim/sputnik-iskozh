// composables/useResponseError.js
import { computed } from 'vue';
import { useStore } from 'vuex';

export function useResponseError () {
    const store  = useStore();
    const errors = computed(() => store.getters['alerts/fieldErrors'] || {});

    const clearResponseErrors = () => {
        store.dispatch('alerts/removeFieldErrors');
    };

    const clearError = (name) => {
        store.dispatch('alerts/removeFieldError', name);
    };

    const parseResponseErrors = (error) => {
        clearResponseErrors();
        if (error.response?.data?.message) {
            showDanger(error.response.data.message);
        }

        if (error.response?.data?.errors) {
            store.dispatch('alerts/setFieldErrors', error.response.data.errors);
        }
        else if (error.response) {
            switch (error.response.status) {
                case 500:
                    alert('Произошла непредвиденная внутренняя ошибка. Попробуйте позже.');
            }
        }
        else {
            console.log(error);
            alert(error);
        }
    };

    const showInfo = (text) => {
        store.dispatch('alerts/addMessage', {
            id  : new Date().getTime(),
            text,
            type: 'info',
        });
    };

    const showSuccess = (text) => {
        store.dispatch('alerts/addMessage', {
            id  : new Date().getTime(),
            text,
            type: 'success',
        });
    };

    const showDanger = (text) => {
        store.dispatch('alerts/addMessage', {
            id  : new Date().getTime(),
            text,
            type: 'danger',
        });
    };

    return {
        errors,
        clearResponseErrors,
        clearError,
        parseResponseErrors,
        showInfo,
        showSuccess,
        showDanger,
    };
}
