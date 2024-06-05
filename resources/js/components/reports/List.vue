<template>
    <div class="row report-list"
         :class="listClass">
        <list-item v-for="report in reports"
                   :itemClass="itemClass"
                   :report="report"
                   :edit="edit"
                   @updated="loadList"
        />
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import ListItem      from './ListItem.vue';

export const ViewMode = {
    Row  : 1,
    Plate: 2,
};

export default {
    name      : 'List',
    components: {
        ListItem,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'viewMode',
        'reloadList',
        'canEdit',
    ],
    data () {
        return {
            showForm: false,

            reports: [],
            edit   : false,
        };
    },
    mounted () {
        this.loadList();
    },
    methods : {
        loadList () {
            window.axios[Url.Routes.reportsList.method](Url.Routes.reportsList.uri).then(response => {
                this.reports = response.data.reports;
                this.edit    = response.data.edit;
                this.$emit('update:canEdit', this.edit);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch   : {
        reloadList () {
            if (this.reloadList) {
                this.loadList();
                this.$emit('reloadList', false);
            }
        },
    },
    computed: {
        itemClass () {
            if (this.viewMode === ViewMode.Plate) {
                return 'col-12 col-md-6 col-lg-4 col-xl-3 col-xxl-2';
            }
            return 'w-100 d-block';
            // return 'w-lg-50 w-md-75 d-block';
        },
        listClass () {
            if (this.viewMode === ViewMode.Plate) {
                return 'plate';
            }
            return 'list d-md-block';
        },
    },
};
</script>