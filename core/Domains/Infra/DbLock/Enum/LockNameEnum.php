<?php declare(strict_types=1);

namespace Core\Domains\Infra\DbLock\Enum;

enum LockNameEnum: string
{
    case DELETE_FILES_JOB                                   = 'delete-files-job';
    case SAVE_IMPORT_PAYMENTS_JOB                           = 'save-import-payments-job';
    case RECALC_CLAIMS_PAID_JOB                             = 'recalc-claims-paid-job';
    case CREATE_REGULAR_PERIOD_INVOICES_JOB                 = 'create-regular-period-invoices-job';
    case CREATE_CLAIMS_AND_PAYMENTS_FOR_REGULAR_INVOICE_JOB = 'create-claims-and-payments-for-regular-invoice-job';
    case AUTO_INCREMENTING_COUNTER_HISTORIES_JOB            = 'auto-incrementing-counter-histories-job';
    case REWATCH_COUNTER_HISTORY_CHAIN_JOB                  = 'rewatch-counter-history-chain-job';
    case CHECK_CLAIM_FOR_COUNTER_CHANGE_JOB                 = 'check-claim-for-counter-change-job';
    case NOTIFY_ABOUT_NEW_UNVERIFIED_COUNTER_HISTORY_JOB    = 'notify-about-new-unverified-counter-history-job';
}
