<template>
    <div class="treasury-page">
        <div class="treasury-search">
            <input
                v-model="query"
                type="text"
                class="treasury-search__input"
                placeholder="Поиск участка по номеру"
                @input="onSearch"
            />
        </div>

        <div v-if="loading" class="treasury-page__loading">Поиск...</div>

        <div v-else-if="accounts.length === 0 && query.length > 0" class="treasury-page__empty">
            Ничего не найдено
        </div>

        <div v-else class="treasury-cards">
            <treasury-card
                v-for="account in accounts"
                :key="account.id"
                :account="account"
                :expanded="expandedId === account.id"
                :invoices="invoicesFor(account.id)"
                :loading="isLoading(account.id)"
                :counters-loading="isCountersLoading(account.id)"
                @toggle="toggleExpand(account)"
                @pay="openPay"
                @counter-added="onCounterAdded(account.id)"
            />
        </div>
    </div>

    <treasury-payment-modal
        :visible="showPaymentModal"
        :invoice="selectedInvoice"
        :account-number="selectedAccountNumber"
        :account-id="selectedAccountId"
        :debt="selectedDebt"
        :account-balance="selectedBalance"
        @close="showPaymentModal = false"
        @paid="onPaid"
    />
</template>

<script setup>
import { ref }              from 'vue';
import TreasuryCard         from './TreasuryCard.vue';
import TreasuryPaymentModal from '@components/treasury/TreasuryPaymentModal.vue';

const query              = ref('');
const accounts           = ref([]);
const loading            = ref(false);
const expandedId         = ref(null);
const chargesMap         = ref({});
const loadingMap         = ref({});
const countersLoadingMap = ref({});

const showPaymentModal      = ref(false);
const selectedInvoice       = ref(null);
const selectedAccountNumber = ref('');
const selectedAccountId     = ref(0);
const selectedDebt          = ref(0);
const selectedBalance       = ref(0);

let debounceTimer = null;

function onSearch () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(performSearch, 300);
}

async function performSearch () {
    if (query.value.length < 1) {
        accounts.value = [];
        return;
    }
    loading.value = true;
    try {
        const { ApiTreasurySearch } = await import('@api');
        const response              = await ApiTreasurySearch({}, { query: query.value });
        accounts.value              = response.data.accounts ?? [];
    }
    catch {
        accounts.value = [];
    }
    finally {
        loading.value = false;
    }
}

function invoicesFor (accountId) {
    return chargesMap.value[accountId] ?? [];
}

function isLoading (accountId) {
    return loadingMap.value[accountId] ?? false;
}

function isCountersLoading (accountId) {
    return countersLoadingMap.value[accountId] ?? false;
}

async function toggleExpand (account) {
    if (expandedId.value === account.id) {
        expandedId.value = null;
        return;
    }
    expandedId.value = account.id;

    if (chargesMap.value[account.id]) {
        return;
    }

    loadingMap.value         = { ...loadingMap.value, [account.id]: true };
    countersLoadingMap.value = { ...countersLoadingMap.value, [account.id]: true };

    try {
        const { ApiTreasuryCharges } = await import('@api');
        const response               = await ApiTreasuryCharges(account.id);
        chargesMap.value             = { ...chargesMap.value, [account.id]: response.data.invoices ?? [] };
    }
    catch {
        chargesMap.value = { ...chargesMap.value, [account.id]: [] };
    }
    finally {
        loadingMap.value         = { ...loadingMap.value, [account.id]: false };
        countersLoadingMap.value = { ...countersLoadingMap.value, [account.id]: false };
    }
}

function openPay (invoice, accountNumber, accountId, accountBalance) {
    selectedInvoice.value       = invoice;
    selectedAccountNumber.value = accountNumber;
    selectedAccountId.value     = accountId;
    selectedDebt.value          = invoice.delta;
    selectedBalance.value       = accountBalance ?? 0;
    showPaymentModal.value      = true;
}

async function onCounterAdded (accountId) {
    loadingMap.value = { ...loadingMap.value, [accountId]: true };
    try {
        const { ApiTreasuryCharges } = await import('@api');
        const response               = await ApiTreasuryCharges(accountId);
        chargesMap.value             = { ...chargesMap.value, [accountId]: response.data.invoices ?? [] };
    }
    catch {
        chargesMap.value = { ...chargesMap.value, [accountId]: [] };
    }
    finally {
        loadingMap.value = { ...loadingMap.value, [accountId]: false };
    }
}

async function onPaid () {
    showPaymentModal.value = false;
    selectedInvoice.value  = null;
    const id               = expandedId.value;
    if (!id) {
        return;
    }
    loadingMap.value = { ...loadingMap.value, [id]: true };
    try {
        const { ApiTreasuryCharges, ApiTreasurySearch } = await import('@api');
        const [chargesRes, searchRes]                   = await Promise.all([
            ApiTreasuryCharges(id),
            ApiTreasurySearch({}, { query: query.value }),
        ]);
        chargesMap.value                                = {
            ...chargesMap.value,
            [id]: chargesRes.data.invoices ?? [],
        };
        if (searchRes.data.accounts) {
            accounts.value = searchRes.data.accounts;
        }
    }
    catch {
        chargesMap.value = { ...chargesMap.value, [id]: [] };
    }
    finally {
        loadingMap.value = { ...loadingMap.value, [id]: false };
    }
}
</script>
