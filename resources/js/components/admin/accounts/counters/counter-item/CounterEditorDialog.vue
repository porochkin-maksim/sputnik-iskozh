<template>
    <view-dialog
        v-model:show="showDialog"
        v-model:hide="hideDialog"
        @hidden="closeAction"
    >
        <template #title>
            {{ localCounter?.id ? 'Редактирование счётчика' : 'Добавление счётчика' }}
        </template>
        <template #body>
            <div class="container-fluid">
                <div>
                    <custom-input
                        v-model="localCounter.number"
                        name="number"
                        :type="'text'"
                        :label="'Серийный номер устройства'"
                        :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-checkbox
                        v-model="localCounter.isInvoicing"
                        name="isInvoicing"
                        :label="'Выставлять счета'"
                        switch-style
                    />
                </div>
                <div
                    v-if="!localCounter?.id"
                    class="mt-2"
                >
                    <custom-input
                        v-model="localCounter.value"
                        name="value"
                        :type="'number'"
                        :label="'Текущие показания на счётчике'"
                        :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-calendar
                        v-model="localCounter.expireAt"
                        name="expireAt"
                        :required="true"
                        :label="'Дата истечения поверки'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input
                        v-model="localCounter.increment"
                        name="increment"
                        :type="'number'"
                        :min="0"
                        :step="1"
                        :label="'Ежемесячное увеличение показаний на кВт'"
                        :required="true"
                        @focusout="calculateIncrement"
                    />
                </div>
                <div
                    v-if="!localCounter?.id"
                    class="mt-2"
                >
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
                            class="btn btn-outline-secondary w-100"
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
                <div class="mt-2">
                    <template v-if="passportFile">
                        <button
                            class="btn btn-sm btn-danger"
                            @click="removePassportFile"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ passportFile.name }}
                    </template>
                    <template v-else>
                        <button
                            v-if="!passportFile"
                            class="btn btn-outline-secondary w-100"
                            @click="choosePassportFile"
                        >
                            <i class="fa fa-paperclip "></i>&nbsp;Паспорт счётчика
                        </button>
                        <input
                            ref="filePassportElem"
                            class="d-none"
                            type="file"
                            accept="image/*, application/pdf"
                            @change="appendPassportFile"
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
                {{ localCounter?.id ? 'Сохранить' : 'Добавить' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import CustomCalendar     from '@common/form/CustomCalendar.vue';
import CustomCheckbox     from '@common/form/CustomCheckbox.vue';
import CustomInput        from '@common/form/CustomInput.vue';
import ViewDialog         from '@common/ViewDialog.vue';
import { useCounterItem } from './useCounterItem';

const props = defineProps({
    account : {
        type   : Object,
        default: null,
    },
    counter : {
        type   : Object,
        default: null,
    },
    showForm: {
        type   : Boolean,
        default: false,
    },
});

const emit = defineEmits(['counterUpdated']);

const {
          appendFile,
          appendPassportFile,
          calculateIncrement,
          canSubmitAction,
          closeAction,
          file,
          fileElem,
          filePassportElem,
          hideDialog,
          localCounter,
          passportFile,
          removeFile,
          removePassportFile,
          saveAction,
          chooseFile,
          choosePassportFile,
          showDialog,
      } = useCounterItem(props, emit);
</script>
