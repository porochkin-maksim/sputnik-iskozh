<template>
    <div class="ticket-category-block">
        <loading-spinner
            v-if="loading"
            size="lg"
            color="primary"
            text="Загрузка категорий..."
            wrapper-class="py-5"
        />

        <div v-else class="row">
            <!-- Левая колонка: список категорий -->
            <div class="col-md-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>Категории</h5>
                    <button class="btn btn-sm btn-primary" @click="openTypeDialog">
                        <i class="fa fa-plus"></i> Добавить
                    </button>
                </div>

                <div class="list-group">
                    <template
                        v-for="cat in categories"
                        :key="cat.id">
                        <button
                            v-if="cat.id"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            :class="{ active: selectedCategory && selectedCategory.id === cat.id }"
                            @click="selectCategory(cat)"
                        >
                            <span>
                                <strong>{{ cat.name }}</strong>

                                &nbsp;<small class="text-muted">{{ cat.type_name }}</small>
                            </span>
                            <span v-if="!cat.is_active" class="badge bg-secondary">Неактивна</span>
                        </button>
                    </template>
                </div>

                <div v-if="categories.length === 0" class="text-muted mt-3">
                    Категории не найдены
                </div>
            </div>

            <!-- Правая колонка: форма редактирования -->
            <div class="col-md-8">
                <div v-if="selectedCategory" class="card">
                    <div class="card-header bg-white">
                        <h5 class="m-0">{{ formTitle }}</h5>
                    </div>
                    <form @submit.prevent="saveCategory">
                        <div class="card-body">
                            <div class="mb-2">
                                <simple-select
                                    v-model="formData.type"
                                    :options="types"
                                    label="Тип заявки"
                                    :clearable="false"
                                />
                            </div>

                            <div class="mb-2">
                                <custom-input
                                    v-model="formData.name"
                                    label="Название категории"
                                    :disabled="saving"
                                    required
                                />
                            </div>

                            <div class="mb-2">
                                <custom-input
                                    v-model="formData.sort_order"
                                    label="Порядок сортировки"
                                    type="number"
                                    :disabled="saving"
                                />
                            </div>

                            <div class="mb-2">
                                <custom-checkbox
                                    v-model="formData.is_active"
                                    label="Активна"
                                    :disabled="saving"
                                    switch-style
                                />
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex gap-2">
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <i v-if="saving" class="fa fa-spinner fa-spin"></i>
                                {{ saving ? 'Сохранение...' : 'Сохранить' }}
                            </button>
                            <button
                                v-if="selectedCategory.id"
                                type="button"
                                class="btn btn-outline-danger"
                                :disabled="saving"
                                @click="deleteCategory"
                            >
                                Удалить
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                @click="resetForm"
                            >
                                Отмена
                            </button>
                        </div>
                    </form>
                </div>

                <div v-else class="alert alert-info">
                    Выберите категорию из списка или создайте новую
                </div>

                <div v-if="selectedCategory && selectedCategory.id" class="mt-2">
                    <ticket-service-block :category-id="selectedCategory.id"></ticket-service-block>
                </div>
            </div>
        </div>

        <!-- Диалог выбора типа для новой категории -->
        <view-dialog
            v-model:show="showTypeDialog"
            v-model:hide="hideTypeDialog"
            @hidden="onTypeDialogClose"
        >
            <template v-slot:title>Выберите тип категории</template>
            <template v-slot:body>
                <simple-select
                    v-model="selectedTypeId"
                    :options="types"
                    label="Тип заявки"
                    :clearable="false"
                />
            </template>
            <template v-slot:footer>
                <button class="btn btn-primary" :disabled="creating" @click="createCategory">
                    <i v-if="creating" class="fa fa-spinner fa-spin"></i>
                    {{ creating ? 'Создание...' : 'Создать' }}
                </button>
            </template>
        </view-dialog>
    </div>
</template>

<script setup>
import {
    ref,
    reactive,
    computed,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import CustomInput          from '../../common/form/CustomInput.vue';
import CustomCheckbox       from '../../common/form/CustomCheckbox.vue';
import ViewDialog           from '../../common/ViewDialog.vue';
import SimpleSelect         from '../../common/form/SimpleSelect.vue';
import {
    ApiAdminHelpDeskSettingsTypesList,
    ApiAdminHelpDeskSettingsCategoriesList,
    ApiAdminHelpDeskSettingsCategoriesGet,
    ApiAdminHelpDeskSettingsCategoriesCreate,
    ApiAdminHelpDeskSettingsCategoriesSave,
    ApiAdminHelpDeskSettingsCategoriesDelete,
}                           from '@api';
import TicketServiceBlock   from '@components/admin/help-desk/TicketServiceBlock.vue';

const { parseResponseErrors, showInfo, showSuccess, showDanger } = useResponseError();

const loading          = ref(true);
const saving           = ref(false);
const creating         = ref(false);
const categories       = ref([]);
const types            = ref([]);
const selectedCategory = ref(null);

// Диалог выбора типа
const showTypeDialog = ref(false);
const hideTypeDialog = ref(false);
const selectedTypeId = ref(null);

const formData = reactive({
    id        : null,
    type      : null,
    name      : '',
    code      : '',
    sort_order: 0,
    is_active : true,
});

const formTitle = computed(() => {
    return formData.id ? 'Редактирование категории' : 'Новая категория';
});

const loadTypes = async () => {
    try {
        const response = await ApiAdminHelpDeskSettingsTypesList();
        types.value    = response.data.types || [];
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const loadCategories = async () => {
    try {
        const response   = await ApiAdminHelpDeskSettingsCategoriesList();
        categories.value = response.data.categories || [];
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const selectCategory = async (category) => {
    try {
        const response = await ApiAdminHelpDeskSettingsCategoriesGet(category.id);
        const cat      = response.data.category;
        Object.assign(formData, {
            id        : cat.id,
            type      : cat.type,
            name      : cat.name,
            code      : cat.code,
            sort_order: cat.sort_order,
            is_active : cat.is_active,
        });
        selectedCategory.value = cat;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const openTypeDialog = () => {
    selectedTypeId.value = types.value[0]?.value || null;
    showTypeDialog.value = true;
    hideTypeDialog.value = false;
};

const onTypeDialogClose = () => {
    showTypeDialog.value = false;
    hideTypeDialog.value = true;
    selectedTypeId.value = null;
};

const createCategory = async () => {
    if (!selectedTypeId.value) {
        showDanger('Выберите тип');
        return;
    }

    creating.value = true;

    try {
        const response    = await ApiAdminHelpDeskSettingsCategoriesCreate(selectedTypeId.value);
        const newCategory = response.data.category;

        // Добавляем новую категорию в список
        categories.value.push(newCategory);

        // Заполняем форму данными новой категории
        Object.assign(formData, {
            id        : newCategory.id,
            type      : newCategory.type,
            name      : newCategory.name,
            code      : newCategory.code,
            sort_order: newCategory.sort_order,
            is_active : newCategory.is_active,
        });

        // Устанавливаем выбранную категорию (откроется форма справа)
        selectedCategory.value = newCategory;

        // Закрываем диалог
        onTypeDialogClose();
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        creating.value = false;
    }
};

const saveCategory = async () => {
    saving.value = true;
    try {
        const payload  = {
            id        : formData.id,
            type      : formData.type,
            name      : formData.name,
            code      : formData.code,
            sort_order: formData.sort_order,
            is_active : formData.is_active,
        };
        const response = await ApiAdminHelpDeskSettingsCategoriesSave({}, payload);
        const saved    = response.data.category;
        const index    = categories.value.findIndex(c => c.id === saved.id);
        if (index !== -1) {
            categories.value[index] = saved;
        }
        else {
            categories.value.push(saved);
        }
        selectedCategory.value = saved;
        Object.assign(formData, {
            id        : saved.id,
            type      : saved.type,
            name      : saved.name,
            code      : saved.code,
            sort_order: saved.sort_order,
            is_active : saved.is_active,
        });
        showSuccess('Категория сохранена');
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        saving.value = false;
    }
};

const deleteCategory = async () => {
    if (!confirm(`Удалить категорию "${formData.name}"?`)) {
        return;
    }

    saving.value = true;
    try {
        await ApiAdminHelpDeskSettingsCategoriesDelete(formData.id);
        categories.value = categories.value.filter(c => c.id !== formData.id);
        resetForm();
        showInfo('Категория удалена');
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        saving.value = false;
    }
};

const resetForm = () => {
    selectedCategory.value = null;
    formData.id            = null;
    formData.type          = null;
    formData.name          = '';
    formData.code          = '';
    formData.sort_order    = 0;
    formData.is_active     = true;
};

onMounted(async () => {
    await loadTypes();
    await loadCategories();
    loading.value = false;
});
</script>

<style scoped>
.list-group-item.active {
    background-color : #e9ecef !important;
    border-color     : #dee2e6 !important;
    color            : #212529 !important;
}
</style>