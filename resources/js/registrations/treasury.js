import TreasuryPage          from '@components/treasury/TreasuryPage.vue';
import TreasuryPaymentModal  from '@components/treasury/TreasuryPaymentModal.vue';

export function registerTreasuryComponents(app) {
    app.component('treasury-page', TreasuryPage);
    app.component('treasury-payment-modal', TreasuryPaymentModal);
}
