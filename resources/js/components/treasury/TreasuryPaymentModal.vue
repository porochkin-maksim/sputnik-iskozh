<template>
    <div v-if="visible" class="treasury-modal-overlay" @click.self="close">
        <div class="treasury-modal">
            <div v-if="success" class="treasury-modal__success">
                <div class="treasury-modal__success-icon">
                    <i class="fa fa-check-circle fa-4x" style="color: #28a745;"></i>
                </div>
                <div class="treasury-modal__success-text">Платёж принят</div>
                <div class="treasury-modal__success-amount">{{ formatAmount(paidAmount) }}</div>
                <button class="treasury-modal__btn treasury-modal__btn--primary" @click="close">
                    Готово
                </button>
            </div>

            <div v-else class="treasury-modal__body">
                <div class="treasury-modal__header">
                    <span class="treasury-modal__header-title">Приём платежа</span>
                    <button class="treasury-modal__header-close" @click="close">&times;</button>
                </div>

                <div class="treasury-modal__info">
                    <div class="treasury-modal__info-row">
                        <span class="treasury-modal__info-label">Участок</span>
                        <span class="treasury-modal__info-value">{{ invoice?.accountNumber || accountNumber }}</span>
                    </div>
                    <div class="treasury-modal__info-row">
                        <span class="treasury-modal__info-label">Период</span>
                        <span class="treasury-modal__info-value">{{ invoice?.periodName }}</span>
                    </div>
                </div>

                <div class="alert p-3 mb-3" :class="statusAlertClass">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa"
                           :class="isPaid ? 'fa-check-circle text-success' : 'fa-warning text-secondary'"></i>
                        <div>
                            <strong>Оплачено:</strong>
                            {{ formatAmount(invoice?.paid || 0) }} / {{ formatAmount(invoice?.cost || 0) }}
                            <br>
                            <span v-if="debt > 0" class="text-danger fw-bold">
                                Долг {{ formatAmount(debt) }}
                            </span>
                            <span v-else-if="debt < 0" class="text-success">
                                Переплата {{ formatAmount(Math.abs(debt)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="treasury-modal__balance mb-3">
                    <div class="treasury-modal__balance-row">
                        <span class="treasury-modal__balance-label">Баланс счёта</span>
                        <span class="treasury-modal__balance-value"
                              :class="accountBalance >= 0 ? 'text-success' : 'text-danger'">
                            {{ formatAmount(Math.abs(accountBalance)) }}
                            <span v-if="accountBalance >= 0"> (переплата)</span>
                            <span v-else> (долг)</span>
                        </span>
                    </div>
                    <div v-if="accountBalance > 0 && amountToPay < debt" class="treasury-modal__balance-row">
                        <span class="treasury-modal__balance-label">Покрывается балансом</span>
                        <span class="treasury-modal__balance-value text-success">
                            {{ formatAmount(debt - amountToPay) }}
                        </span>
                    </div>
                    <div class="treasury-modal__balance-row treasury-modal__balance-row--total">
                        <span class="treasury-modal__balance-label">К оплате</span>
                        <span class="treasury-modal__balance-value fw-bold">{{ formatAmount(amountToPay) }}</span>
                    </div>
                </div>

                <div class="treasury-modal__amount">
                    <div class="treasury-modal__amount-display">
                        <span class="treasury-modal__amount-currency">₽</span>
                        <span class="treasury-modal__amount-value">{{ displayAmount }}</span>
                    </div>
                    <div class="treasury-modal__amount-presets">
                        <button
                            v-for="preset in presets"
                            :key="preset"
                            class="treasury-modal__preset-btn"
                            @click="addAmount(preset)"
                        >
                            +{{ formatAmount(preset) }}
                        </button>
                    </div>
                </div>

                <div class="treasury-keypad">
                    <div class="treasury-keypad__row">
                        <button class="treasury-keypad__key" @click="appendDigit(1)">1</button>
                        <button class="treasury-keypad__key" @click="appendDigit(2)">2</button>
                        <button class="treasury-keypad__key" @click="appendDigit(3)">3</button>
                    </div>
                    <div class="treasury-keypad__row">
                        <button class="treasury-keypad__key" @click="appendDigit(4)">4</button>
                        <button class="treasury-keypad__key" @click="appendDigit(5)">5</button>
                        <button class="treasury-keypad__key" @click="appendDigit(6)">6</button>
                    </div>
                    <div class="treasury-keypad__row">
                        <button class="treasury-keypad__key" @click="appendDigit(7)">7</button>
                        <button class="treasury-keypad__key" @click="appendDigit(8)">8</button>
                        <button class="treasury-keypad__key" @click="appendDigit(9)">9</button>
                    </div>
                    <div class="treasury-keypad__row">
                        <button class="treasury-keypad__key treasury-keypad__key--clear" @click="clearAmount">C</button>
                        <button class="treasury-keypad__key treasury-keypad__key--zero" @click="appendDigit(0)">0
                        </button>
                        <button class="treasury-keypad__key treasury-keypad__key--zero"
                                @click="appendDigit(0),appendDigit(0)">00
                        </button>
                        <button class="treasury-keypad__key treasury-keypad__key--backspace" @click="backspace"><
                        </button>
                    </div>
                </div>

                <div v-if="unpaidClaims.length > 0" class="treasury-allocation">
                    <div class="treasury-allocation__header">Распределение платежа</div>
                    <div class="treasury-allocation__total">
                        Распределено: <strong>{{ formatAmount(totalAllocated) }}</strong>
                        из {{ formatAmount(totalAvailable) }}
                        <span v-if="accountBalance > 0"> (включая {{ formatAmount(accountBalance) }} баланса)</span>
                    </div>
                    <div v-if="hasUnselectedClaims" class="treasury-allocation__hint">
                        <i class="fa fa-info-circle"></i>
                        Нужно выбрать и распределить все услуги
                    </div>
                    <div class="treasury-allocation__list">
                        <div
                            v-for="claim in unpaidClaims"
                            :key="claim.id"
                            class="treasury-allocation__row"
                            :class="{ 'treasury-allocation__row--checked': allocationMap[claim.id]?.checked }"
                        >
                            <label class="treasury-allocation__checkbox">
                                <input
                                    type="checkbox"
                                    :checked="!!allocationMap[claim.id]?.checked"
                                    @change="toggleClaim(claim.id)"
                                />
                            </label>
                            <div class="treasury-allocation__info">
                                <div class="treasury-allocation__name">{{ claim.name }}</div>
                                <div class="treasury-allocation__remainder">остаток {{ formatAmount(claim.delta) }}
                                </div>
                            </div>
                            <div class="treasury-allocation__input-wrap">
                                <input
                                    v-if="allocationMap[claim.id]?.checked"
                                    type="number"
                                    class="treasury-allocation__input"
                                    :value="allocationMap[claim.id]?.amount"
                                    min="0"
                                    :max="claim.delta"
                                    step="0.01"
                                    @input="setAllocation(claim.id, $event)"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    class="treasury-modal__btn treasury-modal__btn--pay"
                    :disabled="totalAvailable <= 0 || totalAllocated > totalAvailable"
                    @click="submitPayment"
                >
                    Принять платёж
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    onBeforeUnmount,
} from 'vue';

const props = defineProps({
    visible        : Boolean,
    invoice        : Object,
    accountNumber  : String,
    accountId      : Number,
    debt           : Number,
    accountBalance : { type: Number, default: 0 },
});

const emit = defineEmits(['close', 'paid']);

const amount     = ref(0);
const success    = ref(false);
const pending    = ref(false);
const paidAmount = ref(0);

const presets = [100, 1000, 5000];

const allocationMap = ref({});

const unpaidClaims = computed(() => {
    return (props.invoice?.claims ?? []).filter(c => c.delta > 0);
});

const totalAvailable = computed(() => {
    return amount.value + Math.max(0, props.accountBalance);
});

const isPaid = computed(() => {
    return props.invoice?.paid >= props.invoice?.cost;
});

const amountToPay = computed(() => {
    if (props.accountBalance > 0) {
        return Math.max(0, props.debt - props.accountBalance);
    }
    return props.debt;
});

const statusAlertClass = computed(() => {
    if (isPaid.value) {
        return 'alert-success';
    }
    if (!props.invoice?.cost) {
        return 'alert-secondary';
    }
    return 'alert-warning';
});

const totalAllocated = computed(() => {
    const entries = Object.values(allocationMap.value);
    return entries.reduce((sum, a) => sum + (a.checked ? (a.amount || 0) : 0), 0);
});

const hasUnselectedClaims = computed(() => {
    if (totalAllocated.value >= totalAvailable.value) {
        return false;
    }
    return unpaidClaims.value.some(c => !allocationMap.value[c.id]?.checked);
});

function toggleClaim (claimId) {
    const claim = unpaidClaims.value.find(c => c.id === claimId);
    if (!claim) {
        return;
    }
    const entry = allocationMap.value[claimId];

    if (entry?.checked) {
        const freedAmount = entry.amount || 0;
        const newMap      = { ...allocationMap.value, [claimId]: { checked: false, amount: 0 } };

        let remainingFreed = freedAmount;
        for (const c of unpaidClaims.value) {
            if (remainingFreed <= 0) {
                break;
            }
            const a = newMap[c.id];
            if (!a?.checked || c.id === claimId) {
                continue;
            }
            const capacity = c.delta - (a.amount || 0);
            if (capacity <= 0) {
                continue;
            }
            const add      = Math.min(remainingFreed, capacity);
            newMap[c.id]   = { checked: true, amount: Math.round(((a.amount || 0) + add) * 100) / 100 };
            remainingFreed = Math.round((remainingFreed - add) * 100) / 100;
        }

        allocationMap.value = newMap;
    }
    else {
        const available = Math.round((totalAvailable.value - totalAllocated.value) * 100) / 100;
        if (available <= 0) {
            return;
        }
        const remaining = Math.min(claim.delta, available);
        if (remaining <= 0) {
            return;
        }
        allocationMap.value = { ...allocationMap.value, [claimId]: { checked: true, amount: remaining } };
    }
}

function setAllocation (claimId, event) {
    const val   = parseFloat(event.target.value);
    const claim = unpaidClaims.value.find(c => c.id === claimId);
    if (!claim) {
        return;
    }
    const othersTotal   = totalAllocated.value - (allocationMap.value[claimId]?.amount || 0);
    const maxAvailable  = Math.min(claim.delta, Math.max(0, totalAvailable.value - othersTotal));
    const clamped       = Math.min(Math.max(0, val || 0), maxAvailable);
    allocationMap.value = {
        ...allocationMap.value,
        [claimId]: { checked: true, amount: Math.round(clamped * 100) / 100 },
    };
}

const displayAmount = computed(() => {
    return amount.value.toLocaleString('ru-RU', { minimumFractionDigits: 2 });
});

function formatAmount (value) {
    if (value === null || value === undefined) {
        return '—';
    }
    return Number(value).toLocaleString('ru-RU', { minimumFractionDigits: 2 }) + ' ₽';
}

function appendDigit (digit) {
    const current = Math.round(amount.value * 100);
    const next    = current * 10 + digit;
    amount.value  = next / 100;
}

function backspace () {
    const current = Math.round(amount.value * 100);
    const next    = Math.floor(current / 10);
    amount.value  = next / 100;
}

function clearAmount () {
    amount.value = 0;
}

function addAmount (value) {
    amount.value = Math.round((amount.value + value) * 100) / 100;
}

watch(() => props.visible, (val) => {
    if (val) {
        amount.value        = Math.max(0, amountToPay.value);
        success.value       = false;
        allocationMap.value = {};
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
});

watch(amount, () => {
    reconcileAllocations();
});

function reconcileAllocations () {
    const checked = Object.entries(allocationMap.value)
        .filter(([, v]) => v.checked && v.amount > 0);

    if (checked.length === 0) {
        return;
    }

    let newMap = { ...allocationMap.value };
    let total  = checked.reduce((s, [, v]) => s + (v.amount || 0), 0);

    if (total > totalAvailable.value) {
        const sorted = [...checked].reverse();
        for (const [claimId, alloc] of sorted) {
            if (total <= totalAvailable.value) {
                break;
            }
            const excess    = total - totalAvailable.value;
            const reduction = Math.min(alloc.amount, excess);
            const newAmount = Math.round((alloc.amount - reduction) * 100) / 100;
            if (newAmount <= 0) {
                newMap[claimId] = { checked: false, amount: 0 };
            }
            else {
                newMap[claimId] = { checked: true, amount: newAmount };
            }
            total = Math.round((total - reduction) * 100) / 100;
        }
    }

    for (const c of unpaidClaims.value) {
        const a = newMap[c.id];
        if (a?.checked) {
            if (a.amount <= 0 || a.amount > c.delta) {
                newMap[c.id] = { checked: false, amount: 0 };
            }
        }
    }

    allocationMap.value = newMap;
}

async function submitPayment () {
    if (totalAvailable.value <= 0 || pending.value) {
        return;
    }
    pending.value = true;

    const allocations = Object.entries(allocationMap.value)
        .filter(([, v]) => v.checked && v.amount > 0)
        .map(([claimId, v]) => ({ claim_id: parseInt(claimId), amount: v.amount }));

    const payAmount = Math.round(amount.value * 100) / 100;

    try {
        const { ApiTreasuryPay } = await import('@api');
        await ApiTreasuryPay({}, {
            account_id: props.accountId,
            amount    : payAmount,
            allocations,
        });
        paidAmount.value = payAmount;
        success.value    = true;
        emit('paid');
    }
    catch {
        // handled by the modal staying open
    }
    finally {
        pending.value = false;
    }
}

function close () {
    amount.value        = 0;
    success.value       = false;
    paidAmount.value    = 0;
    allocationMap.value = {};
    emit('close');
}
</script>
