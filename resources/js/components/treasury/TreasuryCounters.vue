<template>
    <div class="treasury-counters">
        <div class="treasury-counters__header">
            <span class="treasury-counters__title">Счётчики</span>
            <button class="btn btn-sm btn-success" @click.stop="showAddForm = true">
                Добавить счётчик
            </button>
        </div>

        <div v-if="showAddForm" class="treasury-counter-form">
            <div class="treasury-counter-form__row">
                <input v-model="formNumber" class="treasury-counter-form__input" placeholder="Номер счётчика" />
            </div>
            <div class="treasury-counter-form__row">
                <input v-model="formValue" type="number" class="treasury-counter-form__input"
                       placeholder="Текущие показания" min="0" />
            </div>
            <div class="treasury-counter-form__row">
                <input v-model="formPrevValue" type="number" class="treasury-counter-form__input"
                       placeholder="Предыдущие показания (необязательно)" min="0" />
            </div>
            <div class="treasury-counter-form__actions">
                <button class="btn btn-success" @click.stop="submitCreate">
                    <i class="fa fa-check"></i> Сохранить
                </button>
                <button class="btn btn-outline-secondary" @click.stop="cancelCreate">Отмена</button>
            </div>
        </div>

        <template v-if="!countersLoading">
            <treasury-counter-row
                v-for="counter in countersList"
                :key="counter.id"
                :counter="counter"
                :account-id="accountId"
                @counter-added="onCounterAdded"
            />

            <div v-if="countersList.length === 0" class="treasury-counters__empty">
                Нет счётчиков
            </div>
        </template>

        <div v-if="countersLoading" class="treasury-counters__loading">Загрузка...</div>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
} from 'vue';
import TreasuryCounterRow from './TreasuryCounterRow.vue';

const props = defineProps({
    accountId: { type: Number, default: null },
});

const emit = defineEmits(['counter-added']);

const countersList    = ref([]);
const countersLoading = ref(false);
const showAddForm     = ref(false);
const formNumber      = ref('');
const formValue       = ref(null);
const formPrevValue   = ref(null);

watch(() => props.accountId, (id) => {
    if (id) {
        loadCounters();
    }
    else {
        countersList.value = [];
    }
}, { immediate: true });

async function loadCounters () {
    if (!props.accountId) {
        return;
    }
    countersLoading.value = true;
    try {
        const { ApiTreasuryCounters } = await import('@api');
        const res                     = await ApiTreasuryCounters(props.accountId);
        countersList.value            = res.data.counters ?? [];
    }
    catch {
        countersList.value = [];
    }
    finally {
        countersLoading.value = false;
    }
}

function onCounterAdded () {
    loadCounters();
    emit('counter-added');
}

function cancelCreate () {
    showAddForm.value   = false;
    formNumber.value    = '';
    formValue.value     = null;
    formPrevValue.value = null;
}

async function submitCreate () {
    if (!formNumber.value || !formValue.value) {
        return;
    }
    try {
        const { ApiTreasuryCounterCreate } = await import('@api');
        await ApiTreasuryCounterCreate({}, {
            account_id    : props.accountId,
            number        : formNumber.value,
            value         : parseInt(formValue.value),
            previous_value: parseInt(formPrevValue.value) || null,
        });
        await loadCounters();
        emit('counter-added');
    }
    catch {
        // handled
    }
    finally {
        cancelCreate();
    }
}
</script>
