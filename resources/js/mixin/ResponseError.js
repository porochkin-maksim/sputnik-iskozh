export default {
    data () {
        return {
            errors: {},

            alertClass: null,
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
            else if (error.response) {
                switch (error.response.status) {
                    case 500:
                        alert('Произошла непредвиденная внутренняя ошибка. Попробуйте позже.');
                }
            }
            else {
                alert(error);
            }
            this.eventFail();
        },
        eventSuccess () {
            this.clearResponseErrors();

            this.alertClass = 'alert-success';
            setTimeout(() => {
                this.alertClass = null;
            }, 2000);
        },

        eventFail () {
            this.alertClass = 'alert-danger';
            setTimeout(() => {
                this.alertClass = null;
            }, 2000);
        },
    },
};
