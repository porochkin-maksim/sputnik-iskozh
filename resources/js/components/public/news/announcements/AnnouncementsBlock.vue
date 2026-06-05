<template>
    <page-template>
        <template v-slot:main>
            <template v-if="edit">
                <div class="public-action-bar mb-2">
                    <button class="btn btn-success public-action-bar__button"
                            v-on:click="showFormAction">Добавить объявление
                    </button>
                </div>
            </template>
            <div v-if="showForm">
                <wrapper @close="showForm=false"
                         :container-class="'w-lg-75 w-md-100'">
                    <div class="container-fluid">
                        <news-item-edit :model-value="id"
                                        @updated="createdItem" />
                    </div>
                </wrapper>
            </div>
            <announcements-list v-model:reloadList="reloadList"
                                v-model:canEdit="edit"
                                :showPagination="true"
                                :current-page="currentPage"
                                class="mt-3"
            />
        </template>
    </page-template>
</template>

<script setup>
import { ref }           from 'vue';
import NewsItemEdit      from '../list/NewsItemEdit.vue';
import AnnouncementsList from './AnnouncementsList.vue';
import Wrapper           from '@common/Wrapper.vue';
import PageTemplate      from '@components/public/pages/SingleColumnPage.vue';

const props      = defineProps({
    currentPage: {
        type   : Number,
        default: 1,
    },
});

const showForm   = ref(false);
const reloadList = ref(false);
const edit       = ref(false);
const id         = ref(null);

const showFormAction = () => {
    showForm.value = !showForm.value;
};

const createdItem = () => {
    reloadList.value = true;
    showForm.value   = false;
};
</script>
