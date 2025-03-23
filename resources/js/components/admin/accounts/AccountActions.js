import Url           from '../../../utils/Url.js';
import ResponseError from '../../../mixin/ResponseError.js';

export default {
    mixins: [ResponseError],
    data () {
        return {
            account: null,
        };
    },
    methods: {
        getAccountAction (id) {
            let uri = Url.Generator.makeUri(Url.Routes.adminAccountGet, {
                accountId: id,
            });
            window.axios[Url.Routes.adminAccountGet.method](uri).then(response => {
                this.account = response.data;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
