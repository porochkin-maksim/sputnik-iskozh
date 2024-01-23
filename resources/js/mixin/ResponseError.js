export default {
    data () {
        return {
            errors: {},
        };
    },
    methods: {
        clearResponseErrors () {
            this.errors = {};
        },
        clearError (name) {
            delete this.errors[name];
        },
        parseResponseErrors (error) {
            this.clearResponseErrors();
            if (error.response && error.response.data && error.response.data.errors) {
                this.errors = error.response.data.errors;
            }
            else {
                switch (error.response.status) {
                    case 500:
                        alert('Произошла непредвиденная внутренняя ошибка. Попробуйте позже.');
                }
            }
        },
    },
};
