<template>
    <div class="row custom-list list options-list">
        <option-list-item v-for="item in options"
                          :option="item"
                          :edit="edit"
                          @updated="loadList"
        />
    </div>
</template>

<script>
import Url            from '../../utils/Url.js';
import ResponseError  from '../../mixin/ResponseError.js';
import OptionListItem from './OptionListItem.vue';

export default {
    components: {
        OptionListItem,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'viewMode',
        'reloadList',
        'canEdit',
        'itemsCount',
    ],
    data () {
        return {
            showForm: false,

            options: [],
            edit   : true,
        };
    },
    mounted () {
        this.loadList();
    },
    methods : {
        loadList () {
            window.axios[Url.Routes.optionsList.method](Url.Routes.optionsList.uri, {
                params: {
                    all: true,
                },
            }).then(response => {
                this.options = response.data.options;
                this.total   = response.data.total;
                // this.edit    = response.data.edit;

                this.$emit('update:canEdit', this.edit);
                this.$emit('update:itemsCount', this.options.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onPaginationUpdate (skip) {
            this.skip = skip;
            this.loadList();
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
    computed: {
        perPage () {
            return this.limit ? this.limit : 10;
        },
    },
};
</script>