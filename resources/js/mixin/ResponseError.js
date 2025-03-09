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
            this.$store.dispatch('alerts/removeErrors');
        },
        clearError (name) {
            delete this.errors[name];
        },
        parseResponseErrors (error) {
            this.clearResponseErrors();
            if (error.response && error.response.data && error.response.data.errors) {
                this.errors = error.response.data.errors;

                Object.keys(this.errors).forEach(key => {
                    this.$store.dispatch('alerts/addError', {
                        id: key,
                        text: this.errors[key].join(','),
                    });
                });
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
        },
        showInfo(text) {
            this.$store.dispatch('alerts/addMessage', {
                id: new Date().getTime(),
                text: text,
                type: 'info',
            });
        },
        showSuccess(text) {
            this.$store.dispatch('alerts/addMessage', {
                id: new Date().getTime(),
                text: text,
                type: 'success',
            });
        },
        showDanger(text) {
            this.$store.dispatch('alerts/addMessage', {
                id: new Date().getTime(),
                text: text,
                type: 'danger',
            });
        },


        /**
         * @deprecated
         */
        eventSuccess () {
            this.clearResponseErrors();

            this.alertClass = 'alert-success';
            setTimeout(() => {
                this.alertClass = null;
            }, 2000);
        },

        /**
         * @deprecated
         */
        eventFail () {
            this.alertClass = 'alert-danger';
            setTimeout(() => {
                this.alertClass = null;
            }, 2000);
        },
    },
};
