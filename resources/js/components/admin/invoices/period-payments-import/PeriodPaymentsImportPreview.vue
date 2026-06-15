<template>
    <div v-if="importData && importData.length">
        <ul class="nav nav-tabs" role="tablist">
            <li v-for="(districtData, idx) in importData"
                :key="districtData.district"
                class="nav-item"
                role="presentation">
                <button
                    class="nav-link"
                    :class="{ active: idx === activeTab }"
                    :id="`tab-${districtData.district}`"
                    data-bs-toggle="tab"
                    :data-bs-target="`#district-${districtData.district}`"
                    type="button"
                    role="tab"
                    @click="$emit('update:activeTab', idx)"
                    :disabled="submitting"
                >
                    Участок {{ districtData.district }} ({{ districtData.items.length }})
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div
                v-for="(districtData, idx) in importData"
                :key="districtData.district"
                class="tab-pane fade"
                :class="{ show: idx === activeTab, active: idx === activeTab }"
                :id="`district-${districtData.district}`"
                role="tabpanel"
            >
                <div class="d-flex justify-content-between align-items-center mb-2 mt-2 admin-toolbar">
                    <div class="w-50">
                        <custom-select
                            v-model="autoFillStrategy[districtData.district]"
                            :options="fillStrategies"
                            label="Автозаполнение"
                            :disabled="submitting"
                        />
                    </div>
                    <button
                        class="btn btn-sm btn-outline-success"
                        @click="$emit('apply-auto-fill', districtData.district)"
                        :disabled="submitting"
                    >
                        Применить к участку
                    </button>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-sm table-bordered table-striped table-hover align-middle sticky-header admin-table-firm">
                        <thead>
                        <tr class="text-center">
                            <th class="text-end">Участок</th>
                            <th>Счёт</th>
                            <th><i class="fa fa-database"></i> Аванс</th>
                            <th><i class="fa fa-database"></i> Основа</th>
                            <th><i class="fa fa-database"></i> К оплате</th>
                            <th class="text-success"><i class="fa fa-file-excel-o"></i> К оплате</th>
                            <th><i class="fa fa-database"></i> Оплачено</th>
                            <th class="text-success"><i class="fa fa-file-excel-o"></i> Оплачено</th>
                            <th><i class="fa fa-database"></i> Долг</th>
                            <th class="text-success"><i class="fa fa-file-excel-o"></i> Долг</th>
                            <th>Сумма платежа</th>
                        </tr>
                        </thead>
                        <tbody>
                        <period-payments-import-row
                            v-for="item in districtData.items"
                            :key="item.invoiceId"
                            :edited-amount="editedAmounts[getKey(districtData.district, item)]"
                            :item="item"
                            :submitting="submitting"
                            @update:edited-amount="$emit('validate-amount', districtData.district, item)"
                        />
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import CustomSelect            from '@common/form/CustomSelect.vue';
import PeriodPaymentsImportRow from './PeriodPaymentsImportRow.vue';

defineProps({
    importData      : {
        type    : Array,
        required: true,
    },
    activeTab       : {
        type    : Number,
        required: true,
    },
    submitting      : {
        type    : Boolean,
        required: true,
    },
    autoFillStrategy: {
        type    : Object,
        required: true,
    },
    fillStrategies  : {
        type    : Array,
        required: true,
    },
    editedAmounts   : {
        type    : Object,
        required: true,
    },
    getKey          : {
        type    : Function,
        required: true,
    },
});

defineEmits(['update:activeTab', 'apply-auto-fill', 'validate-amount']);
</script>
