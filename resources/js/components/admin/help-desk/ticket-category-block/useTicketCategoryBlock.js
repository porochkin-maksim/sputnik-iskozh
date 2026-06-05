import {
    computed,
    onMounted,
    reactive,
    ref,
} from 'vue';

import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminHelpDeskSettingsTypesList,
    ApiAdminHelpDeskSettingsCategoriesList,
    ApiAdminHelpDeskSettingsCategoriesGet,
    ApiAdminHelpDeskSettingsCategoriesCreate,
    ApiAdminHelpDeskSettingsCategoriesSave,
    ApiAdminHelpDeskSettingsCategoriesDelete,
}                           from '@api';

export function useTicketCategoryBlock () {
    const { parseResponseErrors, showInfo, showSuccess, showDanger } = useResponseError();

    const loading          = ref(true);
    const saving           = ref(false);
    const creating         = ref(false);
    const categories       = ref([]);
    const types            = ref([]);
    const selectedCategory = ref(null);
    const showTypeDialog   = ref(false);
    const hideTypeDialog   = ref(false);
    const selectedTypeId   = ref(null);

    const formData = reactive({
        id        : null,
        type      : null,
        name      : '',
        code      : '',
        sort_order: 0,
        is_active : true,
    });

    const formTitle = computed(() => formData.id ? 'Редактирование категории' : 'Новая категория');

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
            categories.value.push(newCategory);

            Object.assign(formData, {
                id        : newCategory.id,
                type      : newCategory.type,
                name      : newCategory.name,
                code      : newCategory.code,
                sort_order: newCategory.sort_order,
                is_active : newCategory.is_active,
            });

            selectedCategory.value = newCategory;
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
            const index    = categories.value.findIndex(category => category.id === saved.id);
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
            categories.value = categories.value.filter(category => category.id !== formData.id);
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

    return {
        categories,
        createCategory,
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
    };
}
