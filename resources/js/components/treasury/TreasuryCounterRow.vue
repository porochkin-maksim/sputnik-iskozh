<template>
    <div class="treasury-counter">
        <template v-if="editing">
            <div class="treasury-counter__edit">
                <input v-model="editNumber" class="treasury-counter__edit-input" placeholder="Номер" />
                <label class="treasury-counter__toggle">
                    <input v-model="editIsInvoicing" type="checkbox" />
                    <span class="treasury-counter__toggle-label">Выставление счетов</span>
                </label>
                <div class="treasury-counter__edit-actions">
                    <button class="btn btn-sm btn-success" @click.stop="submitEdit">
                        <i class="fa fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" @click.stop="cancelEdit">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </template>
        <template v-else-if="addingValue">
            <div class="treasury-counter-add w-100">
                <input
                    v-model="addValueInput"
                    :min="minValueInput"
                    type="number"
                    class="treasury-counter-add__input"
                    placeholder="Показание"
                    min="0"
                    @keyup.enter="submitAddValue"
                />
                <button class="btn btn-sm btn-success" @click.stop="submitAddValue">
                    <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" @click.stop="cancelAddValue">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </template>
        <template v-else>
            <div class="treasury-counter__number">
                <span :class="counter.isInvoicing ? '' : 'opacity-0'" class="text-success me-2">
                    <i class="fa fa-file-text"></i> счета
                </span>
                <span>{{ counter.number }}</span>
            </div>
            <div class="treasury-counter__right">
                <div class="treasury-counter__value">
                    {{ counter.lastValue ?? '—' }} кВт
                    <span v-if="counter.lastDate" class="treasury-counter__date">{{ counter.lastDate }}</span>
                </div>
                <button class="btn btn-sm btn-outline-secondary" @click.stop="startEdit" title="Редактировать">
                    <i class="fa fa-pencil"></i>
                </button>
                <button class="btn btn-sm btn-success" @click.stop="startAddValue(counter.lastValue)"
                        title="Внести показание">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    counter  : { type: Object, required: true },
    accountId: { type: Number, default: null },
});

const emit = defineEmits(['counter-added']);

const editing         = ref(false);
const editNumber      = ref('');
const editIsInvoicing = ref(false);
const addingValue     = ref(false);
const addValueInput   = ref(null);
const minValueInput   = ref(null);

function startEdit () {
    editing.value         = true;
    editNumber.value      = props.counter.number;
    editIsInvoicing.value = !!props.counter.isInvoicing;
}

function cancelEdit () {
    editing.value         = false;
    editNumber.value      = '';
    editIsInvoicing.value = false;
}

async function submitEdit () {
    if (!editNumber.value) {
        return;
    }
    try {
        const { ApiTreasuryCounterUpdate } = await import('@api');
        await ApiTreasuryCounterUpdate({}, {
            counter_id  : props.counter.id,
            number      : editNumber.value,
            is_invoicing: editIsInvoicing.value,
        });
        emit('counter-added');
    }
    catch {
        // handled
    }
    finally {
        cancelEdit();
    }
}

function startAddValue (value) {
    addingValue.value   = true;
    addValueInput.value = value;
    minValueInput.value = value ? value : 0;
}

function cancelAddValue () {
    addingValue.value   = false;
    addValueInput.value = null;
    minValueInput.value = 0;
}

async function submitAddValue () {
    const val = parseInt(addValueInput.value);
    if (!val || val <= 0) {
        return;
    }
    try {
        const { ApiTreasuryCounterAddValue } = await import('@api');
        await ApiTreasuryCounterAddValue({}, {
            counter_id: props.counter.id,
            value     : val,
        });
        emit('counter-added');
    }
    catch {
        // handled
    }
    finally {
        cancelAddValue();
    }
}
</script>
