// composables/useResponseError.js
import { ref }      from 'vue';
import { useStore } from 'vuex';

export function useResponseError () {
    const store  = useStore();
    const errors = ref({});

    const clearResponseErrors = () => {
        errors.value = {};
        store.dispatch('alerts/removeErrors').then(r => {
            r ? console.log(r) : '';
        });
    };

    const clearError = (name) => {
        delete errors.value[name];
    };

    const parseResponseErrors = (error) => {
        clearResponseErrors();
        if (error.response && error.response.data && error.response.data.message) {
            showDanger(error.response.data.message)
        }

        if (error.response && error.response.data && error.response.data.errors) {
            errors.value = error.response.data.errors;
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
        }).then(r => {
            r ? console.log(r) : '';
        });
    };

    const showSuccess = (text) => {
        store.dispatch('alerts/addMessage', {
            id  : new Date().getTime(),
            text,
            type: 'success',
        }).then(r => {
            r ? console.log(r) : '';
        });
    };

    const showDanger = (text) => {
        store.dispatch('alerts/addMessage', {
            id  : new Date().getTime(),
            text,
            type: 'danger',
        }).then(r => {
            r ? console.log(r) : '';
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