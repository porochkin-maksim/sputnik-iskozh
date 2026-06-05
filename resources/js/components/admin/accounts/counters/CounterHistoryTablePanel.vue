<template>
    <div>
        <loading-spinner
            v-if="loading && histories.length === 0"
            size="lg"
            color="primary"
            text="Загрузка показаний..."
            wrapper-class="py-5"
        />

        <template v-else>
            <counter-item-chart-block
                v-if="histories.length > 0"
                :histories="histories"
                class="mb-3"
            />

            <table class="table table-sm text-center align-middle admin-table-firm">
                <thead>
                <tr class="text-center">
                    <th class="table-thin-column">#</th>
                    <th>Дата</th>
                    <th>Показания</th>
                    <th>Дней</th>
                    <th>Дельта</th>
                    <th>Статус</th>
                    <th class="table-thin-column">Файл</th>
                    <th>Оплачено</th>
                    <th>Стоимость</th>
                    <th>Тариф</th>
                    <th class="table-thin-column"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="history in histories" :key="history.id">
                    <td class="table-thin-column text-center">{{ history.id }}</td>
                    <td class="text-center">{{ formatDate(history.date) }}</td>
                    <td class="text-center">{{ history.value }}</td>
                    <td class="text-center">{{ history.days === null ? '' : history.days }}</td>
                    <td class="text-center">{{ history.delta === null ? '' : history.delta }}</td>
                    <td class="table-thin-column text-center">
                        <template v-if="history.isVerified">
                            <b class="text-success">Подтверждено</b>
                        </template>
                        <template v-else>
                            <button class="btn btn-sm btn-outline-success admin-action-btn" @click="confimAction">
                                <i class="fa fa-check"></i>&nbsp;Подтвердить
                            </button>
                        </template>
                    </td>
                    <td class="table-thin-column text-center">
                        <template v-if="history.file">
                            <file-item
                                :file="history.file"
                                :edit="false"
                            />
                        </template>
                    </td>
                    <template v-if="history.claim">
                        <template v-if="history.invoiceUrl">
                            <td class="text-center">
                                <a
                                    :href="history.invoiceUrl"
                                    class="link-firm"
                                >{{ formatMoney(history.claim.tariff) }}</a>
                            </td>
                            <td class="text-center">
                                <a
                                    :href="history.invoiceUrl"
                                    class="link-firm"
                                >{{ formatMoney(history.claim.cost) }}</a>
                            </td>
                            <td class="text-center">
                                <a
                                    :href="history.invoiceUrl"
                                    class="link-firm"
                                >{{ formatMoney(history.claim.paid) }}</a>
                            </td>
                        </template>
                        <template v-else>
                            <td class="text-center">{{ formatMoney(history.claim.tariff) }}</td>
                            <td class="text-center">{{ formatMoney(history.claim.cost) }}</td>
                            <td class="text-center">{{ formatMoney(history.claim.paid) }}</td>
                        </template>
                    </template>
                    <template v-else-if="history.delta && canEdit && counter.isInvoicing">
                        <td colspan="3">
                            <button
                                class="btn btn-sm btn-success"
                                @click="$emit('add-claim', history)"
                            >
                                Добавить услугу
                            </button>
                        </td>
                    </template>
                    <template v-else>
                        <td colspan="3"></td>
                    </template>
                    <td class="table-thin-column">
                        <div class="d-flex gap-1">
                            <a
                                v-if="canEdit"
                                class="btn btn-sm btn-outline-success admin-action-btn"
                                @click="$emit('edit-history', history)"
                            >
                                <i class="fa fa-edit"></i>
                            </a>
                            <a
                                v-if="canDrop"
                                class="btn btn-sm btn-outline-danger admin-action-btn"
                                @click="$emit('drop-history', history)"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr v-if="histories?.length !== 0 && histories?.length < total">
                    <td colspan="11">
                        <div class="d-flex justify-content-center">
                            <button
                                v-if="!loading"
                                class="btn btn-link link-firm"
                                @click="$emit('load-more')"
                            >
                                Показать ещё
                            </button>
                            <button
                                v-else
                                class="btn border-0"
                                disabled
                            >
                                <i class="fa fa-spinner fa-spin"></i> Подгрузка
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
    </div>

    <counter-history-item
        v-if="selectedHistory"
        :counter="counter"
        :history="selectedHistory"
        @history-updated="$emit('history-updated')"
    />
</template>

<script setup>
import CounterItemChartBlock from '@components/shared/counters/CounterItemChartBlock.vue';
import FileItem              from '@common/files/FileItem.vue';
import LoadingSpinner        from '@common/LoadingSpinner.vue';
import CounterHistoryItem    from './CounterHistoryItem.vue';

defineProps({
    counter        : { type: Object, required: true },
    canEdit        : { type: Boolean, required: true },
    canDrop        : { type: Boolean, required: true },
    histories      : { type: Array, required: true },
    loading        : { type: Boolean, required: true },
    selectedHistory: { type: [Object, null], default: null },
    total          : { type: Number, required: true },
    formatDate     : { type: Function, required: true },
    formatMoney    : { type: Function, required: true },
    vueId          : { type: String, required: true },
});

const confimAction = () => {

};

defineEmits(['confirm-action', 'add-claim', 'drop-history', 'edit-history', 'history-updated', 'load-more']);
</script>
