<template>
    <div class="options-block">
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading"
            size="lg"
            color="success"
            text="Загрузка опций..."
            wrapper-class="py-5"
        />

        <template v-else>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th style="width: 30%">Название</th>
                        <th>Значения</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="option in options" :key="option.id">
                        <td>
                            <div>{{ option.name }}</div>
                            <div class="mt-2">
                                <button
                                    v-if="actions.edit && isOptionChanged(option.id)"
                                    class="btn btn-success btn-sm"
                                    @click="saveAction(option)"
                                    :disabled="saving[option.id]"
                                >
                                    <template v-if="saving[option.id]">
                                        <i class="fa fa-spinner fa-spin"></i> Сохранение
                                    </template>
                                    <template v-else>
                                        <i class="fa fa-save"></i> Сохранить
                                    </template>
                                </button>
                            </div>
                        </td>
                        <td>
                            <div v-if="option.data" class="option-properties">
                                <div
                                    v-for="prop in option.data.properties"
                                    :key="prop.key"
                                    class="mb-1"
                                >
                                    <!-- Чекбокс -->
                                    <div v-if="prop.inputType === 'checkbox'" class="form-check">
                                        <custom-checkbox
                                            :id="'prop-' + option.id + '-' + prop.key"
                                            v-model="editedOptions[option.id][prop.key]"
                                            :label="prop.title"
                                            :disabled="!actions.edit"
                                        />
                                    </div>

                                    <!-- Другие типы полей -->
                                    <custom-input
                                        v-else
                                        :id="'prop-' + option.id + '-' + prop.key"
                                        v-model="editedOptions[option.id][prop.key]"
                                        :type="prop.inputType"
                                        :label="prop.title"
                                        :disabled="!actions.edit"
                                        class="form-control-sm"
                                    />
                                </div>
                            </div>
                            <div v-else class="text-muted">Нет данных</div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </div>
</template>

<script setup>
import {
    ref,
    reactive,
    onMounted,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import CustomInput          from '../../common/form/CustomInput.vue';
import CustomCheckbox       from '../../common/form/CustomCheckbox.vue';
import {
    ApiAdminOptionsList,
    ApiAdminOptionsSave,
}                           from '@api';

defineOptions({
    name: 'OptionsBlock',
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

const options         = ref([]);
const editedOptions   = ref({});
const originalOptions = ref({});
const loading         = ref(true);
const saving          = ref({});
const actions         = ref({
    edit: false,
});

// Загрузка списка опций
const loadOptions = async () => {
    try {
        const response = await ApiAdminOptionsList();
        options.value  = response.data.options || [];
        actions.value  = response.data.actions || { edit: false };

        // Инициализация объектов для редактирования и оригинала
        options.value.forEach(option => {
            if (option.data) {
                // Используем reactive для вложенных объектов? Нет, просто присваиваем в ref-объект.
                editedOptions.value[option.id]   = {};
                originalOptions.value[option.id] = {};

                option.data.properties.forEach(prop => {
                    editedOptions.value[option.id][prop.key]   = prop.value;
                    originalOptions.value[option.id][prop.key] = prop.value;
                });
            }
        });
    }
    catch (error) {
        showDanger('Ошибка при загрузке опций');
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Проверка, были ли изменения для конкретной опции
const isOptionChanged = (optionId) => {
    const edited   = editedOptions.value[optionId];
    const original = originalOptions.value[optionId];
    if (!edited || !original) {
        return false;
    }

    return Object.keys(edited).some(key => edited[key] !== original[key]);
};

// Сохранение изменений для опции
const saveAction = async (option) => {
    if (!isOptionChanged(option.id)) {
        return;
    }

    saving.value[option.id] = true;

    const data = {
        id  : option.id,
        data: {},
    };

    // Собираем только изменённые поля? Можно все, сервер разберётся.
    Object.keys(editedOptions.value[option.id]).forEach(key => {
        data.data[key] = editedOptions.value[option.id][key];
    });

    try {
        await ApiAdminOptionsSave({}, data);

        // После успешного сохранения обновляем оригинальные значения
        Object.keys(editedOptions.value[option.id]).forEach(key => {
            originalOptions.value[option.id][key] = editedOptions.value[option.id][key];
        });

        showInfo('Опция сохранена');
    }
    catch (error) {
        showDanger('Ошибка при сохранении опции');
        parseResponseErrors(error);
    }
    finally {
        saving.value[option.id] = false;
    }
};

onMounted(loadOptions);
</script>