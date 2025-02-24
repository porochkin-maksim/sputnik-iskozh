<template>
    <page-template>
        <template v-slot:main>
            <div v-if="showForm">
                <wrapper @close="showForm=false"
                         :container-class="'w-lg-75 w-md-100'">
                    <div class="container-fluid">
                        <option-item-edit :model-value="id"
                                        @updated="createdItem" />
                    </div>
                </wrapper>
            </div>
            <option-list v-model:reloadList="reloadList"
                       v-model:canEdit="edit"
                       :showPagination="true"
                       class="mt-3"
            />
        </template>
    </page-template>
</template>

<script>
import ResponseError  from '../../../mixin/ResponseError.js';
import OptionItemEdit from './OptionItemEdit.vue';
import OptionList     from './OptionList.vue';
import Wrapper        from '../../common/Wrapper.vue';
import PageTemplate   from '../pages/TwoColumnsPage.vue';

export default {
    name      : 'OptionsBlock',
    components: {
        PageTemplate,
        Wrapper,
        OptionItemEdit,
        OptionList,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            showForm  : false,
            reloadList: false,
            edit      : false,
            id        : null,
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