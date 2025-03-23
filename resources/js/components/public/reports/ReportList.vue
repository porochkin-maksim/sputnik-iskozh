<template>
    <div class="row custom-list list report-list">
        <report-list-item v-for="report in reports"
                          :report="report"
                          :edit="edit"
                          @updated="loadList"
        />
    </div>
</template>

<script>
import Url            from '../../../utils/Url.js';
import ResponseError  from '../../../mixin/ResponseError.js';
import ReportListItem from './ReportListItem.vue';

export const ViewMode = {
    Row  : 1,
    Plate: 2,
};

export default {
    components: {
        ReportListItem,
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
                this.$emit('update:reloadList', false);
            }
        },
    },
};
</script>