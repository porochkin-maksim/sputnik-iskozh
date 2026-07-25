<template>
    <div>
        <loading-spinner
            v-if="isLoading"
            size="lg"
            color="primary"
            text="Загрузка платежей..."
            wrapper-class="py-5"
        />

        <template v-else>
            <table class="table table-sm table-bordered admin-table-firm mb-0">
                <thead>
                <tr>
                    <th class="text-center table-thin-column">№</th>
                    <th class="text-center" v-if="accountId !== null">Название</th>
                    <th class="text-center" v-if="accountId === null">Участок</th>
                    <th class="text-center">Сумма</th>
                    <th class="text-center" v-if="accountId !== null">Использовано</th>
                    <th class="text-center" v-if="accountId !== null">Остаток</th>
                    <th class="text-center payments-files-column">Файлы</th>
                    <th class="text-center">Оплачен</th>
                    <th class="text-center">Создан</th>
                    <th class="text-center table-thin-column">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(payment) in payments" :key="payment.id">
                    <td class="table-thin-column text-center">
                        <span class="link-firm"
                              @click="editAction(payment.id)">
                            {{ payment.id }}
                        </span>
                    </td>
                    <td v-if="accountId !== null">{{ payment.name }}</td>
                    <td class="text-center" v-if="accountId === null">
                        <a :href="payment.account.viewUrl" v-if="payment.account.viewUrl && canViewAccounts" class="link-firm">
                            {{ payment.accountNumber }}
                        </a>
                        <span v-else>
                            {{ payment.accountNumber }}
                        </span>
                    </td>
                    <td class="text-end">{{ formatMoney(payment.cost) }}</td>
                    <td class="text-end" v-if="accountId !== null">{{ formatMoney(payment.allocated) }}</td>
                    <td class="text-end" v-if="accountId !== null">{{ formatMoney(payment.unallocated) }}</td>
                    <td class="payments-files-column">
                        <div v-if="payment.files?.length" class="d-flex flex-column gap-1">
                            <file-item
                                v-for="(file, index) in payment.files"
                                :key="file.id"
                                :file="file"
                                :edit="true"
                                :index="index"
                                :use-up-sort="index !== 0"
                                :use-down-sort="index !== payment.files.length - 1"
                                @updated="onFileUpdated"
                            />
                        </div>
                    </td>
                    <td class="table-thin-column text-center">{{ payment.paid }}</td>
                    <td class="table-thin-column text-center">{{ payment.created }}</td>
                    <td class="table-thin-column text-center">
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            <history-btn
                                v-if="payment.historyUrl"
                                class="btn-link underline-none p-0"
                                :url="payment.historyUrl"
                            />

                            <button
                                v-if="canDrop"
                                class="btn btn-sm btn-outline-danger admin-action-btn"
                                type="button"
                                :disabled="dropLoading === payment.id"
                                :aria-label="'Удалить платёж ' + payment.id"
                                @click="dropAction(payment.id)"
                            >
                                <i
                                    v-if="dropLoading === payment.id"
                                    class="fa fa-spinner fa-spin"
                                    aria-hidden="true"
                                ></i>
                                <i v-else class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!payments.length">
                    <td colspan="11" class="text-center py-3 text-muted">
                        <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
                        Платежи не найдены
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    computed,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions.js';
import HistoryBtn           from '@common/HistoryBtn.vue';
import FileItem             from '@common/files/FileItem.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminPaymentManageList,
}                           from '@api';

const props = defineProps({
    invoiceId : {
        type   : Number,
        default: null,
    },
    accountId : {
        type   : Number,
        default: null,
    },
    selectedId: {
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
});

const emit = defineEmits(['update:reload', 'update:selectedId', 'update:count', 'delete']);

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatMoney }                               = useFormat();
const { has }                                       = usePermissions();

const canDrop     = computed(() => has('payments', 'drop'));
const listParams  = computed(() => {
    const params = {};
    if (props.invoiceId) params.invoice_id = props.invoiceId;
    if (props.accountId) params.account_id = props.accountId;
    return params;
});

const canViewAccounts = computed(() => has('accounts', 'view'));

const payments    = ref([]);
const isLoading   = ref(false);
const dropLoading = ref(null);

const loadList = async () => {
    isLoading.value = true;
    try {
        const response = await ApiAdminPaymentManageList(listParams.value);
        payments.value = response.data.payments || [];
        emit('update:count', payments.value.length);
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

const editAction = (id) => {
    emit('update:selectedId', id);
};

const dropAction = async (id) => {
    emit('delete', id);
};

const onFileUpdated = () => {
    loadList();
};

watch([() => props.invoiceId, () => props.accountId, () => props.reload], () => {
    loadList();
}, { immediate: true });
</script>
