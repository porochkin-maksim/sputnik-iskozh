<template>
    <page-template>
        <template v-slot:sub
                  v-if="edit">
            <div class="d-flex justify-content-between">
                <button class="btn btn-success"
                        v-on:click="showFormAction">Добавить отчёт
                </button>
                <div class="btn-group d-none">
                    <button class="btn"
                            :class="viewMode === ViewMode.Row ? 'btn-success' : 'btn-outline-secondary'"
                            @click="viewMode = ViewMode.Row"
                    >
                        <i class="fa fa-reorder"></i>
                    </button>
                    <button class="btn"
                            :class="viewMode === ViewMode.Plate ? 'btn-success' : 'btn-outline-secondary'"
                            @click="viewMode = ViewMode.Plate"
                    >
                        <i class="fa fa-cubes"></i>
                    </button>
                </div>
            </div>
        </template>
        <template v-slot:main>
            <div v-if="showForm">
                <wrapper @close="showForm=false">
                    <div class="container-fluid">
                        <report-item-edit :model-value="id"
                                          @updated="createdItem" />
                    </div>
                </wrapper>
            </div>
            <report-list :view-mode="viewMode"
                         v-model:reloadList="reloadList"
                         v-model:canEdit="edit"
                         class="mt-3"
            />
        </template>
    </page-template>
</template>

<script>
import ResponseError  from '../../../mixin/ResponseError.js';
import ReportItemEdit from './ReportItemEdit.vue';
import ReportList     from './ReportList.vue';
import { ViewMode }   from './ReportList.vue';
import Wrapper        from '../../common/Wrapper.vue';
import PageTemplate   from '../pages/TwoColumnsPage.vue';

export default {
    name      : 'ReportsBlock',
    components: {
        PageTemplate,
        Wrapper,
        ReportItemEdit,
        ReportList,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            ViewMode  : ViewMode,
            showForm  : false,
            reloadList: false,
            edit      : false,
            id        : null,
            viewMode  : ViewMode.Row,
        };
    },
    methods: {
        showFormAction () {
            this.showForm = !this.showForm;
        },
        createdItem () {
            this.reloadList = true;
            this.showForm   = false;
        },
    },
};
</script>