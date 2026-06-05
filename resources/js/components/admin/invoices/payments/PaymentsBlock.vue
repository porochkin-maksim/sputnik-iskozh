<template>
    <div>
        <h5>Платежи</h5>

        <div class="d-flex mb-2">
            <button
                class="btn btn-success"
                v-if="canEdit && invoice.actions.payments.edit"
                :disabled="loading"
                @click="makeAction"
            >
                <i class="fa fa-plus" aria-hidden="true"></i>
                Добавить платёж
            </button>

            <button
                class="btn btn-outline-success ms-2"
                v-if="canEdit && invoice.actions.payments.edit && !invoice.isPaid && !forcePaid"
                :disabled="loading"
                @click="makePaid"
            >
                <i class="fa fa-credit-card" aria-hidden="true"></i>
                Оплатить всё
            </button>
        </div>

        <payments-list
            :invoice-id="invoice.id"
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
            v-model:count="paymentsCount"
            @update:count="onUpdatedCount"
        />

        <view-dialog
            v-model:show="showDialog"
            v-model:hide="hideDialog"
            @hidden="closeAction"
            v-if="payment && (canEdit || canView) && (payment.actions.edit || payment.actions.view)"
        >
            <template #title>
                {{ payment.id ? (canEdit && payment.actions.edit ? 'Редактирование платёжа' : 'Просмотр платёжа') : 'Добавление платежа' }}
            </template>

            <template #body>
                <!-- Название платежа -->
                <div class="mb-3">
                    <custom-input
                        v-model="payment.name"
                        :errors="errors?.name"
                        label="Название платежа"
                        type="text"
                        :disabled="!canEdit || !payment.actions.edit || loading"
                        @update:modelValue="clearError('name')"
                    />
                </div>

                <!-- Стоимость и дата -->
                <div class="row mb-3">
                    <div class="col-6">
                        <custom-input
                            v-model="payment.cost"
                            :errors="errors?.cost"
                            label="Стоимость"
                            type="number"
                            step="0.01"
                            :disabled="!canEdit || !payment.actions.edit || loading"
                            @update:modelValue="clearError('cost')"
                        />
                    </div>
                    <div class="col-6">
                        <custom-calendar
                            v-model="payment.paid"
                            :error="errors?.paid"
                            label="Дата платежа"
                            :disabled="!canEdit || !payment.actions.edit || loading"
                            @update:modelValue="clearError('paid')"
                        />
                    </div>
                </div>

                <!-- Комментарий -->
                <div class="mb-3">
                    <custom-textarea
                        v-model="payment.comment"
                        :errors="errors?.comment"
                        label="Комментарий"
                        :disabled="!canEdit || !payment.actions.edit || loading"
                        :rows="4"
                        @update:modelValue="clearError('comment')"
                    />
                </div>

                <!-- Существующие файлы -->
                <template v-if="payment.files?.length">
                    <div class="mb-3">
                        <label class="form-label">Прикреплённые файлы</label>
                        <file-item
                            v-for="(file, index) in payment.files"
                            :key="file.id"
                            :file="file"
                            :edit="true"
                            :index="index"
                            :use-up-sort="index !== 0"
                            :use-down-sort="index !== payment.files.length - 1"
                            class="mb-2"
                            @updated="onFileUpdated"
                        />
                    </div>
                </template>

                <!-- Новые файлы -->
                <template v-if="files.length">
                    <div class="mb-3">
                        <label class="form-label">Новые файлы</label>
                        <ul class="list-unstyled">
                            <li v-for="(file, index) in files"
                                :key="index"
                                class="mb-2 d-flex justify-content-between align-items-center p-2 border rounded"
                            >
                                <div>
                                    <button
                                        class="btn btn-sm btn-danger me-2"
                                        @click="removeFile(index)"
                                        type="button"
                                    >
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                    {{ index + 1 }}. {{ file.name }}
                                </div>
                                <span class="text-secondary small">
                                    {{ (file.size / (1024 * 1024)).toFixed(2) }} MB
                                </span>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-end small">
                            <span :class="fileSizeExceed ? 'text-danger' : 'text-secondary'">
                                Общий размер: {{ filesSize }} MB
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Кнопка добавления файлов -->
                <button
                    v-if="!fileCountExceed"
                    class="btn btn-outline-secondary w-100"
                    @click="chooseFiles"
                    :disabled="loading"
                    type="button"
                >
                    <i class="fa fa-paperclip me-2" aria-hidden="true"></i>
                    Добавить файлы
                </button>

                <input
                    ref="fileElem"
                    type="file"
                    class="d-none"
                    accept="image/*,application/pdf"
                    @change="appendFiles"
                    multiple
                />
            </template>

            <template #footer>
                <div v-if="canEdit && payment.actions.edit" class="d-flex justify-content-end w-100">
                    <button
                        class="btn btn-success"
                        :disabled="!canSave || loading"
                        @click="saveAction"
                    >
                        <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        {{ payment.id ? 'Сохранить' : 'Создать' }}
                    </button>
                </div>
            </template>
        </view-dialog>
    </div>
</template>

<script setup>
import PaymentsList         from './PaymentsList.vue';
import ViewDialog           from '@common/ViewDialog.vue';
import FileItem             from '@common/files/FileItem.vue';
import CustomInput          from '@common/form/CustomInput.vue';
import CustomCalendar       from '@common/form/CustomCalendar.vue';
import CustomTextarea       from '@common/form/CustomTextarea.vue';
import { usePermissions }   from '@composables/usePermissions.js';
import { usePaymentsBlock } from './usePaymentsBlock.js';
import { computed }         from 'vue';

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
    reload : {
        type   : Boolean,
        default: false,
    },
    count  : {
        type   : Number,
        default: 0,
    },
});

const emit    = defineEmits(['update:reload', 'update:count']);
const { has } = usePermissions();
const canEdit = computed(() => has('payments', 'edit'));
const canView = computed(() => has('payments', 'view'));

const {
          canSave,
          clearError,
          closeAction,
          chooseFiles,
          fileCountExceed,
          fileElem,
          fileSizeExceed,
          files,
          filesSize,
          forcePaid,
          formatMoney,
          getAction,
          hideDialog,
          loading,
          makeAction,
          makePaid,
          onFileUpdated,
          onUpdatedCount,
          payment,
          paymentsCount,
          reloadList,
          saveAction,
          selectedId,
          showDialog,
      } = usePaymentsBlock(props, emit);
</script>
