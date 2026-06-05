<template>
    <div class="card mb-2">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Информация</h5>
                <div class="d-flex">
                    <history-btn
                        v-if="account?.historyUrl"
                        class="btn-link underline-none"
                        :url="account?.historyUrl"
                    />
                </div>
            </div>
        </div>
        <div class="card-body">
            <template v-if="account.actions?.edit">
                <div class="row">
                    <div class="col-6">
                        <custom-input
                            v-model="numberModel"
                            :required="true"
                            :disabled="loading"
                            :label="'Номер участка'"
                        />
                    </div>
                    <div class="col-6">
                        <custom-input
                            v-model="sizeModel"
                            :required="true"
                            :disabled="loading"
                            :label="'Площадь (м²)'"
                            :type="'number'"
                            :min="0"
                            :step="1"
                        />
                    </div>
                </div>
                <div>
                    <div class="mt-2">
                        <custom-checkbox
                            v-model="isInvoicingModel"
                            :disabled="loading"
                            :label="'Выставлять счета'"
                            switch-style
                        />
                    </div>
                    <div>
                        <custom-input
                            v-model="cadastreNumberModel"
                            :disabled="loading"
                            :label="'Кадастровый номер'"
                        />
                    </div>
                </div>
            </template>
            <template v-else>
                <h6>Данные участка</h6>
                <account-info-list :account="account" />
            </template>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex">
                    <button
                        v-if="canEdit"
                        class="btn btn-success me-2"
                        :disabled="!canSave || loading"
                        @click="$emit('save')"
                    >
                        <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

import AccountInfoList from '../AccountInfoList.vue';
import CustomCheckbox  from '@common/form/CustomCheckbox.vue';
import CustomInput     from '@common/form/CustomInput.vue';
import HistoryBtn      from '@common/HistoryBtn.vue';

const props = defineProps({
    account: { type: Object, required: true },
    canEdit: { type: Boolean, required: true },
    canSave: { type: Boolean, required: true },
    loading: { type: Boolean, required: true },
});

const emit = defineEmits(['save', 'update:cadastreNumber', 'update:isInvoicing', 'update:number', 'update:size']);

const numberModel = computed({
    get: () => props.account.number,
    set: (value) => emit('update:number', value),
});

const sizeModel = computed({
    get: () => props.account.size,
    set: (value) => emit('update:size', value),
});

const isInvoicingModel = computed({
    get: () => props.account.isInvoicing,
    set: (value) => emit('update:isInvoicing', value),
});

const cadastreNumberModel = computed({
    get: () => props.account.cadastreNumber,
    set: (value) => emit('update:cadastreNumber', value),
});
</script>
