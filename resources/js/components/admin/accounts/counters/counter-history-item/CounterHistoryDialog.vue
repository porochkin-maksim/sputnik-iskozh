<template>
    <view-dialog
        v-model:show="showDialog"
        v-model:hide="hideDialog"
        @hidden="closeAction"
    >
        <template #title>
            {{ localHistory?.id ? 'Изменение показаний счётчика' : 'Внесение показаний счётчика' }}
        </template>
        <template #body>
            <div class="container-fluid">
                <div
                    v-if="counter.isInvoicing"
                    class="alert alert-info"
                >
                    <template v-if="localHistory?.id">
                        При обновлении показаний будет автоматически пересчитана услуга к регулярному счёту текущего периода, либо будет создан новый доходный счёт
                    </template>
                    <template v-else>
                        При добавлении показаний будет автоматически создана услуга к регулярному счёту текущего периода, либо будет создан новый доходный счёт
                    </template>
                </div>
                <div class="mt-2">
                    <custom-input
                        v-model="formData.value"
                        name="value"
                        :type="'number'"
                        :label="'Текущие показания на счётчике'"
                        :required="true"
                    />
                </div>
                <div class="mt-2">
                    <label class="text-secondary">Дата показаний</label>
                    <custom-calendar
                        v-model="formData.date"
                        name="date"
                        :required="true"
                    />
                </div>
                <div class="mt-2">
                    <div v-if="file">
                        <button
                            class="btn btn-sm btn-danger"
                            @click="removeFile"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button
                            v-if="!file"
                            class="btn btn-outline-secondary"
                            @click="chooseFile"
                        >
                            <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                        </button>
                        <input
                            ref="fileElem"
                            class="d-none"
                            type="file"
                            accept="image/*"
                            @change="appendFile"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template #footer>
            <button
                class="btn btn-success"
                :disabled="!canSubmitAction"
                @click="saveAction"
            >
                {{ localHistory?.id ? 'Сохранить' : 'Добавить' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import CustomCalendar            from '@common/form/CustomCalendar.vue';
import CustomInput               from '@common/form/CustomInput.vue';
import ViewDialog                from '@common/ViewDialog.vue';
import { useCounterHistoryItem } from './useCounterHistoryItem';

const props = defineProps({
    counter: {
        type   : Object,
        default: null,
    },
    history: {
        type   : Object,
        default: null,
    },
});

const emit = defineEmits(['historyUpdated']);

const {
          appendFile,
          canSubmitAction,
          chooseFile,
          closeAction,
          errors,
          file,
          fileElem,
          formData,
          hideDialog,
          localHistory,
          removeFile,
          saveAction,
          showDialog,
      } = useCounterHistoryItem(props, emit);
</script>
