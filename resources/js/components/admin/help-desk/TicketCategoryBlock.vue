<template>
    <div class="ticket-category-block">
        <loading-spinner
            v-if="loading"
            size="lg"
            color="primary"
            text="Загрузка категорий..."
            wrapper-class="py-5"
        />

        <div
            v-else
            class="row"
        >
            <div class="col-md-4">
                <ticket-category-list
                    :categories="categories"
                    :selected-category="selectedCategory"
                    @open-type-dialog="openTypeDialog"
                    @select-category="selectCategory"
                />
            </div>

            <div class="col-md-8">
                <ticket-category-editor
                    v-if="selectedCategory"
                    :form-data="formData"
                    :saving="saving"
                    :selected-category="selectedCategory"
                    :form-title="formTitle"
                    :types="types"
                    @delete-category="deleteCategory"
                    @reset-form="resetForm"
                    @save-category="saveCategory"
                />

                <div
                    v-else
                    class="alert alert-info"
                >
                    Выберите категорию из списка или создайте новую
                </div>

                <div
                    v-if="selectedCategory && selectedCategory.id"
                    class="mt-2"
                >
                    <ticket-service-block :category-id="selectedCategory.id" />
                </div>
            </div>
        </div>

        <ticket-category-type-dialog
            v-model:hide-dialog="hideTypeDialog"
            v-model:show-dialog="showTypeDialog"
            :creating="creating"
            :selected-type-id="selectedTypeId"
            :types="types"
            @create-category="createCategory"
            @hidden="onTypeDialogClose"
        />
    </div>
</template>

<script setup>
import {
    onMounted,
} from 'vue';

import { useTicketCategoryBlock } from './ticket-category-block/useTicketCategoryBlock';
import LoadingSpinner             from '@common/LoadingSpinner.vue';
import TicketServiceBlock         from './TicketServiceBlock.vue';
import TicketCategoryEditor       from './ticket-category-block/TicketCategoryEditor.vue';
import TicketCategoryList         from './ticket-category-block/TicketCategoryList.vue';
import TicketCategoryTypeDialog   from './ticket-category-block/TicketCategoryTypeDialog.vue';

const {
          categories,
          creating,
          deleteCategory,
          formData,
          formTitle,
          hideTypeDialog,
          loading,
          onTypeDialogClose,
          openTypeDialog,
          resetForm,
          saveCategory,
          selectCategory,
          selectedCategory,
          selectedTypeId,
          showTypeDialog,
          types,
          saving,
          createCategory,
      } = useTicketCategoryBlock();

onMounted(() => {
    // composable handles initial loading internally
});
</script>
