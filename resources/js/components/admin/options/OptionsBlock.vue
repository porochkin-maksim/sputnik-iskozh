<template>
    <div class="options-block">
        <div v-if="loading"
             class="text-center py-5">
            <div class="spinner-border text-success"
                 role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th style="width: 30%">Название</th>
                        <th>Значения</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="option in options"
                        :key="option.id">
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
                            <div v-if="option.data"
                                 class="option-properties">
                                <div v-for="prop in option.data.properties"
                                     :key="prop.key"
                                     class="mb-1">
                                    <label :for="'prop-' + option.id + '-' + prop.key"
                                           class="form-label mb-0">
                                        {{ prop.title }}
                                    </label>
                                    <input v-if="prop.inputType !== 'checkbox'"
                                           :id="'prop-' + option.id + '-' + prop.key"
                                           v-model="editedOptions[option.id][prop.key]"
                                           :type="prop.inputType"
                                           class="form-control form-control-sm"
                                           :disabled="!actions.edit"
                                    />
                                    <div v-else
                                         class="form-check">
                                        <input type="checkbox"
                                               :id="'prop-' + option.id + '-' + prop.key"
                                               v-model="editedOptions[option.id][prop.key]"
                                               class="form-check-input"
                                               :disabled="!actions.edit"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div v-else
                                 class="text-muted">
                                Нет данных
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';

export default {
    name  : 'OptionsBlock',
    mixins: [ResponseError],
    data () {
        return {
            options        : [],
            editedOptions  : {},
            originalOptions: {},
            loading        : true,
            error          : null,
            saving         : {},
            actions        : {
                edit: false,
            },
            Url            : Url,
        };
    },
    created () {
        this.listAction();
    },
    methods: {
        listAction () {
            this.options = [];
            window.axios[Url.Routes.adminOptionsList.method](Url.Routes.adminOptionsList.uri).then(response => {
                this.options = response.data.options;
                this.actions = response.data.actions;

                // Инициализация редактируемых значений
                this.options.forEach(option => {
                    if (option.data) {
                        this.editedOptions[option.id]   = {};
                        this.originalOptions[option.id] = {};

                        option.data.properties.forEach(prop => {
                            this.editedOptions[option.id][prop.key]   = prop.value;
                            this.originalOptions[option.id][prop.key] = prop.value;
                        });
                    }
                });
            }).catch(response => {
                this.showDanger('Ошибка при загрузке опций');
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },

        isOptionChanged (optionId) {
            if (!this.editedOptions[optionId] || !this.originalOptions[optionId]) {
                return false;
            }

            return Object.keys(this.editedOptions[optionId]).some(key =>
                this.editedOptions[optionId][key] !== this.originalOptions[optionId][key],
            );
        },

        saveAction (option) {
            if (!this.isOptionChanged(option.id)) {
                return;
            }

            this.saving[option.id] = true;

            const data = {
                id  : option.id,
                data: {},
            };

            // Преобразуем отредактированные значения в формат для сохранения
            Object.keys(this.editedOptions[option.id]).forEach(key => {
                data.data[key] = this.editedOptions[option.id][key];
            });

            window.axios[Url.Routes.adminOptionsSave.method](
                Url.Routes.adminOptionsSave.uri,
                data,
            ).then(() => {
                // Обновляем оригинальные значения после успешного сохранения
                Object.keys(this.editedOptions[option.id]).forEach(key => {
                    this.originalOptions[option.id][key] = this.editedOptions[option.id][key];
                });

                this.showInfo('Опция сохранена');
            }).catch(response => {
                this.showDanger('Ошибка при сохранении опции');
                this.parseResponseErrors(response);
            }).then(() => {
                this.saving[option.id] = false;
            });
        },
    },
};
</script>