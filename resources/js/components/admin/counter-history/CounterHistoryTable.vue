<template>
    <div v-if="histories.length">
        <table class="table table-sm table-bordered table-hover align-middle admin-table-firm">
            <thead>
            <tr class="text-center">
                <th v-if="canEdit && canCheckAction && !isVerifiedStatus" class="table-thin-column">
                    <div>
                        <input
                            :checked="allCheck"
                            type="checkbox"
                            class="form-check-input"
                            @change="$emit('check-all')"
                        />
                    </div>
                </th>
                <th>Участок</th>
                <th>Счётчик</th>
                <th>Дата</th>
                <th>Показание</th>
                <th v-if="canCheckAction">Предыдущее</th>
                <th v-if="canCheckAction">Дельта</th>
                <th>Выставлять счета</th>
                <th class="table-thin-column">Файл</th>
                <th class="table-thin-column"></th>
                <th v-if="canDrop" class="table-thin-column"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="history in histories" :key="history.id">
                <td v-if="canEdit && canCheckAction && !isVerifiedStatus" class="table-thin-column text-center">
                    <div>
                        <input
                            :checked="isChecked(history.id)"
                            type="checkbox"
                            class="form-check-input"
                            @change="$emit('toggle-item', history.id)"
                        />
                    </div>
                </td>
                <template v-if="history.accountId && history.counterId">
                    <td v-if="history.accountUrl" class="table-thin-column text-end">
                        <a :href="history.accountUrl" class="link-firm">{{ history.accountNumber }}</a>
                    </td>
                    <td v-else class="table-thin-column text-end">{{ history.accountNumber }}</td>
                    <td>{{ history.counterNumber }}</td>
                </template>
                <template v-else>
                    <td colspan="2" class="text-center table-thin-column">
                        <button
                            v-if="canEdit"
                            class="btn btn-sm border-0"
                            @click="$emit('link', history.id)"
                        >
                            <i class="fa fa-link"></i>&nbsp;привязать
                        </button>
                    </td>
                </template>
                <td class="text-center">{{ formatDate(history.date) }}</td>
                <td class="text-end">{{ history.value }}</td>
                <td v-if="canCheckAction" class="text-end">{{ history.before ? history.before : 'начальное' }}</td>
                <td v-if="canCheckAction" class="text-end">{{ history.delta ? history.delta : '' }}</td>
                <td class="table-thin-column text-center">
                    <i v-if="history.isInvoicing" class="fa fa-check text-success"></i>
                    <i v-else class="fa fa-close text-danger"></i>
                </td>
                <td class="table-thin-column">
                    <div v-if="history.file">
                        <a
                            :href="history.file.url"
                        class="link-firm"
                            :data-lightbox="history.file.name"
                            :data-title="history.file.name"
                            target="_blank"
                        >
                            {{ history.file.name }}
                        </a>
                    </div>
                </td>
                <td class="table-thin-column">
                    <history-btn
                        class="btn-link underline-none"
                        :url="history.historyUrl"
                    />
                </td>
                <td v-if="canDrop" class="table-thin-column">
                    <button
                        class="btn btn-sm btn-outline-danger admin-action-btn"
                        @click="$emit('drop', history.id)"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div v-else class="text-center text-muted py-3">
        Нет показаний для отображения
    </div>
</template>

<script setup>
import HistoryBtn from '@common/HistoryBtn.vue';

defineProps({
    canEdit         : { type: Boolean, required: true },
    canDrop         : { type: Boolean, required: true },
    allCheck        : { type: Boolean, required: true },
    canCheckAction  : { type: Boolean, required: true },
    formatDate      : { type: Function, required: true },
    histories       : { type: Array, required: true },
    isChecked       : { type: Function, required: true },
    isVerifiedStatus: { type: Boolean, required: true },
});

defineEmits(['check-all', 'drop', 'link', 'toggle-item', 'update:allCheck']);
</script>
