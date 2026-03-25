<?php declare(strict_types=1);

namespace Core\Domains\Infra\DbLock\Enum;

enum LockNameEnum: string
{
    case SAVE_IMPORT_PAYMENTS_JOB                           = 'save-import-payments-job';
    case RECALC_CLAIMS_PAID_JOB                             = 'recalc-claims-paid-job';
    case CREATE_REGULAR_PERIOD_INVOICES_JOB                 = 'create-regular-period-invoices-job';
    case CREATE_CLAIMS_AND_PAYMENTS_FOR_REGULAR_INVOICE_JOB = 'create-claims-and-payments-for-regular-invoice-job';
}
