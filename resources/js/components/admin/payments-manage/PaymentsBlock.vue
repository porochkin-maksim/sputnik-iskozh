<template>
    <div class="card">
        <div v-if="showHeader" class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <h5 class="m-0">Платежи</h5>
                <span v-if="accountBalance !== null" class="badge bg-success fs-6 fw-normal">
                    Баланс: {{ formatMoney(accountBalance) }}
                </span>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <div class="d-flex align-items-center gap-1">
                    <custom-calendar
                        v-model="exportDate"
                        placeholder="дд.мм.гггг"
                        classes="w-auto"
                    />
                    <button
                        class="btn btn-sm btn-outline-primary"
                        :disabled="!exportDate"
                        @click="downloadExport"
                        title="Скачать Excel"
                    >
                        <i class="fa fa-download"></i>
                    </button>
                </div>
                <button
                    class="btn btn-success"
                    v-if="canEdit"
                    :disabled="loading"
                    @click="makeAction"
                >
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Добавить платёж
                </button>
            </div>
        </div>

        <div class="card-body">

            <payments-manage-list
                :invoice-id="invoice?.id"
                :account-id="accountId"
                v-model:selected-id="selectedId"
                v-model:reload="reloadList"
                v-model:count="paymentsCount"
                @update:count="onUpdatedCount"
                @delete="deleteAction"
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

                    <div class="mb-3" v-if="(!payment.id || (canEdit && payment.actions.edit)) && !payment.isVerified">
                        <search-select
                            v-model="payment.accountId"
                            :items="payment.accounts || []"
                            :disabled="!canEdit || !payment.actions.edit || loading"
                            label="Участок"
                            placeholder="Выберите участок"
                        />
                    </div>

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

                    <button
                        v-if="!fileCountExceed && canEdit"
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

                    <template v-if="transactions.length">
                        <table class="table table-sm table-bordered admin-table-firm my-3">
                            <template v-for="group in groupedTransactions" :key="group.invoiceId ?? 'unallocated'">
                                <tbody>
                                <tr v-if="group.invoiceId" class="table-info">
                                    <th colspan="3" class="text-center">
                                        <a :href="routeUri('adminInvoiceView', {id: group.invoiceId})" target="_blank"
                                           class="link-firm">
                                            Счёт №{{ group.invoiceId }}
                                        </a>
                                    </th>
                                </tr>
                                <tr class="table-success">
                                    <th>Услуга</th>
                                    <th class="text-end">Сумма</th>
                                    <th class="text-end">Дата</th>
                                </tr>
                                <tr v-for="t in group.transactions" :key="t.id">
                                    <td>{{ t.name }}</td>
                                    <td class="text-end">{{ formatMoney(t.cost) }}</td>
                                    <td class="text-end">{{ t.createdAt }}</td>
                                </tr>
                                </tbody>
                            </template>
                        </table>
                    </template>
                </template>

                <template #footer>
                    <div v-if="canEdit && payment.actions.edit" class="d-flex justify-content-end w-100">
                        <button
                            class="btn btn-success"
                            :disabled="!canSave || loading"
                            @click="saveAction"
                        >
                            <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                            {{ payment.id ? 'Подтвердить' : 'Создать' }}
                        </button>
                    </div>
                </template>
            </view-dialog>

        </div>
    </div>
</template>

<script setup>
import {
    computed,
    onMounted,
    ref,
    watch,
}                                              from 'vue';
import ViewDialog                              from '@common/ViewDialog.vue';
import FileItem                                from '@common/files/FileItem.vue';
import CustomInput                             from '@common/form/CustomInput.vue';
import CustomCalendar                          from '@common/form/CustomCalendar.vue';
import CustomTextarea                          from '@common/form/CustomTextarea.vue';
import SearchSelect                            from '@common/form/SearchSelect.vue';
import PaymentsManageList                      from './PaymentsManageList.vue';
import { usePermissions }                      from '@composables/usePermissions.js';
import { usePaymentsManage }                   from './usePaymentsManage.js';
import { useResponseError }                    from '@composables/useResponseError';
import { routeUri }                            from '@utils/routeUri.js';
import { ApiAdminPaymentManageAccountBalance } from '@api';

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

const props = defineProps({
    invoice   : {
        type   : Object,
        default: null,
    },
    accountId : {
        type   : Number,
        default: null,
    },
    reload    : {
        type   : Boolean,
        default: false,
    },
    count     : {
        type   : Number,
        default: 0,
    },
    showHeader: {
        type   : Boolean,
        default: true,
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
          deleteAction,
          fileCountExceed,
          fileElem,
          fileSizeExceed,
          files,
          filesSize,
          formatMoney,
          hideDialog,
          loading,
          makeAction,
          onFileUpdated,
          onUpdatedCount,
          payment,
          paymentsCount,
          reloadList,
          saveAction,
          selectedId,
          showDialog,
          transactions,
      } = usePaymentsManage(props, emit);

const groupedTransactions = computed(() => {
    const groups = {};
    for (const t of transactions.value) {
        const key = t.invoiceId ?? 'unallocated';
        if (!groups[key]) {
            groups[key] = { invoiceId: t.invoiceId, transactions: [] };
        }
        groups[key].transactions.push(t);
    }
    return Object.values(groups);
});

const exportDate = ref('');

const downloadExport = () => {
    if (!exportDate.value) {
        return;
    }
    const url = routeUri('adminPaymentExport', {}, { date: exportDate.value });
    window.open(url, '_blank');
};

const accountBalance = ref(null);

const loadBalance = async () => {
    if (!props.accountId) {
        accountBalance.value = null;
        return;
    }
    try {
        const response       = await ApiAdminPaymentManageAccountBalance(props.accountId);
        accountBalance.value = response.data?.balance ?? null;
    }
    catch {
        accountBalance.value = null;
    }
};

watch(() => props.reload, (val) => {
    if (val) {
        loadBalance();
    }
});

watch(() => props.accountId, () => {
    loadBalance();
});

onMounted(loadBalance);

</script>
