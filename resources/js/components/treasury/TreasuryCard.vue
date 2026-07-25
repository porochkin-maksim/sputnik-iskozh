<template>
    <div
        class="treasury-card"
        :class="{ 'treasury-card--expanded': expanded }"
    >
        <div class="treasury-card__main" @click="$emit('toggle')">
            <div class="treasury-card__number">{{ account.number }}</div>
            <div class="treasury-card__right">
                <div class="treasury-card__balance"
                     :class="account.balance >= 0 ? 'treasury-card__balance--positive' : 'treasury-card__balance--negative'">
                    {{ formatBalance(account.balance) }}
                </div>
                <div class="treasury-card__arrow">
                    <i :class="expanded ? 'fa fa-chevron-up' : 'fa fa-chevron-down'"></i>
                </div>
            </div>
        </div>

        <div v-if="expanded" class="treasury-card__detail" @click.stop>
            <div v-if="loading" class="treasury-card__detail-loading">Загрузка...</div>

            <template v-else-if="invoices?.length > 0">
                <div v-for="inv in invoices" :key="inv.id" class="treasury-invoice">
                    <div class="treasury-invoice__period">{{ inv.periodName }}</div>
                    <div class="treasury-invoice__amounts">
                        <div class="treasury-invoice__amount">
                            <span class="treasury-invoice__amount-label">Начислено</span>
                            <span class="treasury-invoice__amount-value">{{ formatBalance(inv.cost) }}</span>
                        </div>
                        <div class="treasury-invoice__amount">
                            <span class="treasury-invoice__amount-label">Оплачено</span>
                            <span class="treasury-invoice__amount-value">{{ formatBalance(inv.paid) }}</span>
                        </div>
                        <div class="treasury-invoice__amount">
                            <span class="treasury-invoice__amount-label">Долг</span>
                            <span class="treasury-invoice__amount-value"
                                  :class="debtClass(inv.delta)">{{ formatBalance(inv.delta) }}</span>
                        </div>
                    </div>
                    <div class="treasury-invoice__actions">
                        <button class="treasury-invoice__btn treasury-invoice__btn--details"
                                @click.stop="toggleClaims(inv.id)">
                            <i class="fa fa-list-ul"></i>
                            {{ expandedClaims.has(inv.id) ? 'Скрыть детали' : 'Детализация' }}
                        </button>
                        <button class="treasury-invoice__btn treasury-invoice__btn--pay"
                                @click.stop="$emit('pay', inv, account.number, account.id, account.balance)">
                            <i class="fa fa-credit-card"></i> Оплатить
                        </button>
                    </div>

                    <div v-if="expandedClaims.has(inv.id) && inv.claims && inv.claims.length > 0"
                         class="treasury-claims">
                        <div class="treasury-claim treasury-claim--header">
                            <div class="treasury-claim__name">Услуга</div>
                            <div class="treasury-claim__tariff">Тариф</div>
                            <div class="treasury-claim__quantity">Кол-во</div>
                            <div class="treasury-claim__cost">Начислено</div>
                            <div class="treasury-claim__paid">Оплачено</div>
                            <div class="treasury-claim__delta">Остаток</div>
                        </div>
                        <div v-for="claim in inv.claims" :key="claim.id" class="treasury-claim">
                            <div class="treasury-claim__name">{{ claim.name }}</div>
                            <div class="treasury-claim__tariff">{{ formatBalance(claim.tariff) }}</div>
                            <div class="treasury-claim__quantity">{{ claim.quantity }}</div>
                            <div class="treasury-claim__cost">{{ formatBalance(claim.cost) }}</div>
                            <div class="treasury-claim__paid">{{ formatBalance(claim.paid) }}</div>
                            <div class="treasury-claim__delta"
                                 :class="deltaClass(claim.delta)">{{ formatBalance(claim.delta) }}
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div v-else-if="!countersLoading" class="treasury-card__detail-empty">Нет начислений</div>

            <treasury-counters :account-id="account.id" @counter-added="$emit('counter-added')" />

            <div v-if="countersLoading" class="treasury-card__detail-loading">Загрузка...</div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
}                       from 'vue';
import TreasuryCounters from './TreasuryCounters.vue';

const props = defineProps({
    account        : { type: Object, required: true },
    expanded       : { type: Boolean, default: false },
    invoices       : { type: Array, default: () => [] },
    loading        : { type: Boolean, default: false },
    countersLoading: { type: Boolean, default: false },
});

defineEmits(['toggle', 'pay', 'counter-added']);

const expandedClaims = ref(new Set());

function toggleClaims (invoiceId) {
    const set = new Set(expandedClaims.value);
    if (set.has(invoiceId)) {
        set.delete(invoiceId);
    }
    else {
        set.add(invoiceId);
    }
    expandedClaims.value = set;
}

function formatBalance (balance) {
    if (balance === null || balance === undefined) {
        return '—';
    }
    return Number(balance).toLocaleString('ru-RU', { minimumFractionDigits: 2 }) + ' ₽';
}

function debtClass (debt) {
    if (debt === null || debt === undefined) {
        return '';
    }
    return debt > 0 ? 'treasury-invoice__debt--negative' : 'treasury-invoice__debt--positive';
}

function deltaClass (delta) {
    if (delta === null || delta === undefined) {
        return '';
    }
    return delta > 0 ? 'treasury-claim__delta--negative' : 'treasury-claim__delta--positive';
}
</script>
