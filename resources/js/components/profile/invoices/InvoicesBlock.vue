<template>
    <div class="profile-invoices-view">
        <div class="page-card profile-period-card">
            <div class="profile-period-card__label">Период</div>
            <form class="profile-period-card__form"
                  :action="periodRoute">
                <select name="period"
                        class="form-select profile-period-select"
                        :value="selectedPeriodId"
                        @change="submitPeriod">
                    <option v-for="period in periods"
                            :key="period.id"
                            :value="period.id">
                        {{ period.name }}
                    </option>
                </select>
            </form>
        </div>

        <div v-if="invoices.length" class="profile-invoices-list">
            <details v-for="invoice in invoices"
                     :key="invoice.id"
                     class="page-card profile-invoice-card">
                <summary class="profile-invoice-card__summary">
                    <div class="profile-invoice-card__title">
                        <span class="profile-invoice-card__label">Счёт</span>
                        <span class="profile-invoice-card__value">{{ invoice.title }}</span>
                    </div>
                    <div class="profile-invoice-card__meta">
                        <div class="profile-invoice-chip">
                            <span class="profile-invoice-chip__label">Тариф</span>
                            <span class="profile-invoice-chip__value">{{ invoice.cost }}</span>
                        </div>
                        <div class="profile-invoice-chip">
                            <span class="profile-invoice-chip__label">Оплачено</span>
                            <span class="profile-invoice-chip__value">{{ invoice.paid }}</span>
                        </div>
                        <div class="profile-invoice-chip">
                            <span class="profile-invoice-chip__label">Долг</span>
                            <span class="profile-invoice-chip__value" :class="invoice.deltaNumeric > 0 ? 'debt' : 'paid'">{{ invoice.delta }}</span>
                        </div>
                    </div>
                </summary>

                <div class="profile-invoice-card__body">
                    <div class="profile-invoice-claims">
                        <div class="profile-invoice-claims__list">
                            <div v-for="claim in invoice.claims"
                                 :key="claim.id"
                                 class="profile-invoice-claim-item">
                                <div class="profile-invoice-claim-item__name">{{ claim.name }}</div>
                                <div class="profile-invoice-claim-item__meta">
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Тариф</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.tariff }}</span>
                                    </span>
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Стоимость</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.cost }}</span>
                                    </span>
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Оплачено</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.paid }}</span>
                                    </span>
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Долг</span>
                                        <span class="profile-invoice-claim-item__value" :class="claim.deltaNumeric > 0 ? 'debt' : 'paid'">{{ claim.delta }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="invoice.payments.length" class="profile-invoice-payments">
                        <div class="profile-invoice-payments__title">Платежи</div>
                        <div class="profile-invoice-payments__list">
                            <div v-for="payment in invoice.payments"
                                 :key="payment.id"
                                 class="profile-invoice-payment-item">
                                <span class="profile-invoice-payment-item__date">{{ payment.date }}</span>
                                <span class="profile-invoice-payment-item__cost">{{ payment.cost }}</span>
                            </div>
                        </div>
                    </div>

                        <div class="profile-invoice-actions">
                        <template v-if="acquiringAvailable && invoice.acquiring.length">
                            <form v-for="amount in invoice.acquiring"
                                  :key="`${invoice.id}-${amount.label}`"
                                  method="POST"
                                  :action="amount.url"
                                  target="_blank">
                                <input type="hidden"
                                       name="_token"
                                       :value="csrfToken">
                                <button class="btn btn-sm btn-success profile-invoice-action-btn">
                                    <i class="fa fa-credit-card"></i>
                                    Оплатить {{ amount.label }}
                                </button>
                            </form>
                        </template>
                        <a class="btn btn-sm btn-outline-primary profile-invoice-action-btn"
                           :href="invoice.paymentUrl">
                            <i class="fa fa-envelope"></i>
                            Сообщить о совершённом платеже
                        </a>
                        <a class="btn btn-sm btn-outline-danger profile-invoice-action-btn"
                           target="_blank"
                           :href="invoice.receiptUrl">
                            <i class="fa fa-file-pdf-o text-danger"></i>
                            Получить квитанцию
                        </a>
                    </div>
                </div>
            </details>
        </div>

        <div v-else class="profile-empty-state">
            <hr>
            <h6 class="text-center text-secondary m-0"><i>счетов нет...</i></h6>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    periods           : {
        type   : Array,
        default: () => [],
    },
    selectedPeriodId  : {
        type   : [String, Number],
        default: null,
    },
    periodRoute       : {
        type    : String,
        required: true,
    },
    invoices          : {
        type   : Array,
        default: () => [],
    },
    acquiringAvailable: {
        type   : Boolean,
        default: false,
    },
    csrfToken         : {
        type   : String,
        default: '',
    },
});

const submitPeriod = (event) => {
    event.target.form?.submit();
};
</script>
