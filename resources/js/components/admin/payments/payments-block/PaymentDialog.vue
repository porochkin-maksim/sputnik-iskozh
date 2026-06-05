<template>
    <view-dialog
        v-model:show="showProxy"
        v-model:hide="hideProxy"
        @hidden="$emit('hidden')"
    >
        <template #title>
            {{ canEdit && payment.actions.edit ? 'Привязка платёжа' : 'Просмотр платёжа' }}
        </template>

        <template #body>
            <div class="container-fluid">
                <template v-if="canEdit && payment.actions.edit">
                    <div class="mb-2">
                        <search-select
                            v-model="payment.accountId"
                            :items="accounts"
                            label="Участок"
                            :disabled="invoicesLoading"
                            @update:modelValue="$emit('account-changed')"
                        />
                    </div>

                    <div
                        v-if="periods.length"
                        class="mb-2"
                    >
                        <search-select
                            v-model="periodProxy"
                            :items="periods"
                            label="Период"
                            :disabled="invoicesLoading"
                            @update:modelValue="$emit('account-changed')"
                        />
                    </div>

                    <div
                        v-if="invoices.length"
                        class="mb-2"
                    >
                        <search-select
                            v-model="payment.invoiceId"
                            :items="invoices"
                            label="Счёт"
                            :disabled="invoicesLoading"
                        />
                    </div>

                    <loading-spinner
                        v-if="invoicesLoading"
                        size="sm"
                        color="primary"
                        :show-text="false"
                        wrapper-class="py-2"
                    />
                </template>

                <div class="mb-2">
                    <custom-input
                        v-model="payment.cost"
                        :errors="errors?.cost"
                        label="Стоимость"
                        type="number"
                        step="0.01"
                        :disabled="!canEdit || !payment.actions.edit"
                        @update:modelValue="$emit('clear-error', 'cost')"
                    />
                </div>

                <div class="mb-2">
                    <custom-input
                        v-model="payment.name"
                        :errors="errors?.name"
                        label="Название платежа"
                        type="text"
                        :disabled="!canEdit || !payment.actions.edit"
                        @update:modelValue="$emit('clear-error', 'name')"
                    />
                </div>

                <div class="mb-2">
                    <custom-textarea
                        v-model="payment.comment"
                        :errors="errors?.comment"
                        label="Комментарий"
                        :rows="4"
                        :disabled="!canEdit || !payment.actions.edit"
                        @update:modelValue="$emit('clear-error', 'comment')"
                    />
                </div>

                <template v-if="payment.files?.length">
                    <div class="mb-2">
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
                            @updated="$emit('file-updated')"
                        />
                    </div>
                </template>
            </div>
        </template>

        <template #footer>
            <button
                v-if="canEdit && payment.actions.edit"
                class="btn btn-success"
                :disabled="!canSave || saving"
                @click="$emit('save')"
            >
                <i class="fa" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ payment.id ? 'Сохранить' : 'Создать' }} платёж
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import { computed } from 'vue';

import CustomInput        from '@common/form/CustomInput.vue';
import CustomTextarea     from '@common/form/CustomTextarea.vue';
import FileItem           from '@common/files/FileItem.vue';
import LoadingSpinner     from '@common/LoadingSpinner.vue';
import SearchSelect       from '@common/form/SearchSelect.vue';
import ViewDialog         from '@common/ViewDialog.vue';
import { usePermissions } from '@composables/usePermissions.js';

const props = defineProps({
    accounts       : {
        type    : Array,
        required: true,
    },
    canSave        : {
        type    : Boolean,
        required: true,
    },
    errors         : {
        type    : Object,
        required: true,
    },
    invoices       : {
        type    : Array,
        required: true,
    },
    invoicesLoading: {
        type    : Boolean,
        required: true,
    },
    periodId       : {
        type   : [Number, String, null],
        default: null,
    },
    periods        : {
        type    : Array,
        required: true,
    },
    payment        : {
        type    : Object,
        required: true,
    },
    saving         : {
        type    : Boolean,
        required: true,
    },
    showDialog     : {
        type    : Boolean,
        required: true,
    },
    hideDialog     : {
        type    : Boolean,
        required: true,
    },
});

const emit    = defineEmits([
    'account-changed',
    'clear-error',
    'file-updated',
    'hidden',
    'save',
    'update:periodId',
    'update:showDialog',
    'update:hideDialog',
]);
const { has } = usePermissions();
const canEdit = computed(() => has('payments', 'edit'));

const showProxy = computed({
    get: () => props.showDialog,
    set: (value) => emit('update:showDialog', value),
});

const hideProxy = computed({
    get: () => props.hideDialog,
    set: (value) => emit('update:hideDialog', value),
});

const periodProxy = computed({
    get: () => props.periodId,
    set: (value) => emit('update:periodId', value),
});
</script>
