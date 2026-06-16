<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Period\PeriodEntity;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Route::group(['prefix' => 'invoices'], static function () {
    Route::get('/', [Controllers\Admin\Billing\InvoiceController::class, 'index'])->name(RouteNames::ADMIN_INVOICE_INDEX);

    Breadcrumbs::for(RouteNames::ADMIN_INVOICE_INDEX, static function (BreadcrumbTrail $trail) {
        $previousUrl = url()->previous();
        $routeName   = Route::getRoutes()->match(Request::create($previousUrl))->getName();
        $route       = route(RouteNames::ADMIN_INVOICE_INDEX);
        if ($routeName === RouteNames::ADMIN_INVOICE_INDEX) {
            $route = $previousUrl;
        }

        $trail->parent(RouteNames::ADMIN);
        $trail->push(RouteNames::name(RouteNames::ADMIN_INVOICE_INDEX), $route);
    });

    Route::group(['prefix' => '/view/{id}'], static function () {
        Route::get('/', [Controllers\Admin\Billing\InvoiceController::class, 'view'])->name(RouteNames::ADMIN_INVOICE_VIEW);
        Route::get('/invoice-receipt', [Controllers\Common\Documents\ReceiptController::class, 'makeByInvoiceId'])->name(RouteNames::ADMIN_DOCUMENT_RECEIPT_INVOICE);
    })->whereNumber('id');

    Breadcrumbs::for(RouteNames::ADMIN_INVOICE_VIEW, static function (BreadcrumbTrail $trail, InvoiceEntity $invoice) {
        $trail->parent(RouteNames::ADMIN_INVOICE_INDEX);
        $trail->push('Счёт №' . $invoice->getId(), route(RouteNames::ADMIN_INVOICE_VIEW, $invoice->getId()));
    });

    Route::get('/export', [Controllers\Admin\Billing\InvoiceController::class, 'export'])->name(RouteNames::ADMIN_INVOICE_EXPORT);

    Route::group(['prefix' => '/import-payments/period-{periodId}'], static function () {
        Route::get('/', [Controllers\Admin\Billing\InvoiceImportController::class, 'index'])->name(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX);
        Route::post('/parse-file', [Controllers\Admin\Billing\InvoiceImportController::class, 'parseFile'])->name(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_PARSE_FILE);
        Route::post('/save', [Controllers\Admin\Billing\InvoiceImportController::class, 'save'])->name(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_SAVE);
    })->whereNumber('periodId');

    Breadcrumbs::for(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX, static function (BreadcrumbTrail $trail, PeriodEntity $period) {
        $trail->parent(RouteNames::ADMIN_INVOICE_INDEX, ['period' => $period->getId()]);
        $trail->push('Импорт платежей в счета периода ' . $period->getName(), route(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX, ['periodId' => $period->getId()]));
    });

    Route::group(['prefix' => 'json'], static function () {
        Route::get('/get-without-regular/{periodId}', [Controllers\Admin\Billing\InvoiceController::class, 'getAccountCountWithoutRegular'])
            ->name(RouteNames::ADMIN_INVOICE_GET_ACCOUNTS_COUNT_WITHOUT_REGULAR)
            ->whereNumber('periodId')
        ;
        Route::post('/create-regular-invoices/{periodId}', [Controllers\Admin\Billing\InvoiceController::class, 'createRegularInvoices'])
            ->name(RouteNames::ADMIN_INVOICE_CREATE_REGULAR_INVOICES)
            ->whereNumber('periodId')
        ;
        Route::get('/create', [Controllers\Admin\Billing\InvoiceController::class, 'create'])->name(RouteNames::ADMIN_INVOICE_CREATE);
        Route::get('/list', [Controllers\Admin\Billing\InvoiceController::class, 'list'])->name(RouteNames::ADMIN_INVOICE_LIST);
        Route::post('/save', [Controllers\Admin\Billing\InvoiceController::class, 'save'])->name(RouteNames::ADMIN_INVOICE_SAVE);
        Route::post('/recalc/{id}', [Controllers\Admin\Billing\InvoiceController::class, 'recalc'])
            ->name(RouteNames::ADMIN_INVOICE_RECALC)
            ->whereNumber('id')
        ;
        Route::post('/recalc-period/{periodId}', [Controllers\Admin\Billing\InvoiceController::class, 'recalcPeriod'])
            ->name(RouteNames::ADMIN_INVOICE_RECALC_PERIOD)
            ->whereNumber('periodId')
        ;
        Route::delete('/delete/{id}', [Controllers\Admin\Billing\InvoiceController::class, 'delete'])
            ->name(RouteNames::ADMIN_INVOICE_DELETE)
            ->whereNumber('id')
        ;
        Route::get('/get/{id}', [Controllers\Admin\Billing\InvoiceController::class, 'get'])
            ->name(RouteNames::ADMIN_INVOICE_GET)
            ->whereNumber('id')
        ;

        Route::group(['prefix' => '{invoiceId}'], static function () {
            Route::group(['prefix' => 'claims'], static function () {
                Route::get('/create', [Controllers\Admin\Billing\ClaimController::class, 'create'])
                    ->name(RouteNames::ADMIN_CLAIM_CREATE)
                ;
                Route::get('/list', [Controllers\Admin\Billing\ClaimController::class, 'list'])
                    ->name(RouteNames::ADMIN_CLAIM_LIST)
                ;
                Route::post('/save', [Controllers\Admin\Billing\ClaimController::class, 'save'])
                    ->name(RouteNames::ADMIN_CLAIM_SAVE)
                ;
                Route::delete('/delete/{id}', [Controllers\Admin\Billing\ClaimController::class, 'delete'])
                    ->name(RouteNames::ADMIN_CLAIM_DELETE)
                    ->whereNumber('id')
                ;
                Route::get('/get/{claimId}', [Controllers\Admin\Billing\ClaimController::class, 'get'])
                    ->name(RouteNames::ADMIN_CLAIM_VIEW)
                    ->whereNumber('claimId')
                ;
            });
            Route::group(['prefix' => 'payments'], static function () {
                Route::get('/create', [Controllers\Admin\Billing\PaymentManageController::class, 'create'])
                    ->name(RouteNames::ADMIN_PAYMENT_CREATE)
                ;
                Route::get('/list', [Controllers\Admin\Billing\PaymentManageController::class, 'list'])
                    ->name(RouteNames::ADMIN_PAYMENT_LIST)
                ;
                Route::post('/save', [Controllers\Admin\Billing\PaymentManageController::class, 'save'])
                    ->name(RouteNames::ADMIN_PAYMENT_SAVE)
                ;
                Route::delete('/delete/{id}', [Controllers\Admin\Billing\PaymentManageController::class, 'delete'])
                    ->name(RouteNames::ADMIN_PAYMENT_DELETE)
                    ->whereNumber('id')
                ;
                Route::get('/get/{paymentId}', [Controllers\Admin\Billing\PaymentManageController::class, 'get'])
                    ->name(RouteNames::ADMIN_PAYMENT_VIEW)
                    ->whereNumber('paymentId')
                ;
            });
        })->whereNumber('invoiceId');
    });

    // необработанные платежи
    Route::group(['prefix' => 'payments'], static function () {
        Route::get('/', [Controllers\Admin\Requests\NewPaymentController::class, 'index'])->name(RouteNames::ADMIN_NEW_PAYMENT_INDEX);
        Route::group(['prefix' => 'json'], static function () {
            Route::get('/list', [Controllers\Admin\Billing\PaymentManageController::class, 'list'])->name(RouteNames::ADMIN_NEW_PAYMENT_LIST);
            Route::get('/get-invoices/{accountId}/{periodId}', [Controllers\Admin\Billing\PaymentManageController::class, 'getInvoices'])
                ->name(RouteNames::ADMIN_NEW_PAYMENT_INVOICES)
                ->whereNumber('accountId')
                ->whereNumber('periodId')
            ;
            Route::post('/save', [Controllers\Admin\Billing\PaymentManageController::class, 'save'])->name(RouteNames::ADMIN_NEW_PAYMENT_SAVE);
            Route::delete('/delete/{id}', [Controllers\Admin\Billing\PaymentManageController::class, 'delete'])
                ->name(RouteNames::ADMIN_NEW_PAYMENT_DELETE)
                ->whereNumber('id')
            ;
            Route::get('/get/{paymentId}', [Controllers\Admin\Billing\PaymentManageController::class, 'get'])
                ->name(RouteNames::ADMIN_NEW_PAYMENT_VIEW)
                ->whereNumber('paymentId')
            ;
        });
    });

    // универсальное управление платежами
    Route::group(['prefix' => 'payments/manage'], static function () {
        Route::group(['prefix' => 'json'], static function () {
            Route::get('/list', [Controllers\Admin\Billing\PaymentManageController::class, 'list'])->name(RouteNames::ADMIN_PAYMENT_MANAGE_LIST);
            Route::get('/create', [Controllers\Admin\Billing\PaymentManageController::class, 'create'])->name(RouteNames::ADMIN_PAYMENT_MANAGE_CREATE);
            Route::get('/get/{paymentId}', [Controllers\Admin\Billing\PaymentManageController::class, 'get'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_VIEW)
                ->whereNumber('paymentId')
            ;
            Route::post('/save', [Controllers\Admin\Billing\PaymentManageController::class, 'save'])->name(RouteNames::ADMIN_PAYMENT_MANAGE_SAVE);
            Route::delete('/delete/{id}', [Controllers\Admin\Billing\PaymentManageController::class, 'delete'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_DELETE)
                ->whereNumber('id')
            ;
            Route::get('/get-invoices/{accountId}/{periodId}', [Controllers\Admin\Billing\PaymentManageController::class, 'getInvoices'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_INVOICES)
                ->whereNumber('accountId')
                ->whereNumber('periodId')
            ;
            Route::get('/can-pay-all/{invoiceId}', [Controllers\Admin\Billing\PaymentManageController::class, 'canPayAll'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_CAN_PAY_ALL)
                ->whereNumber('invoiceId')
            ;
            Route::post('/pay-all/{invoiceId}', [Controllers\Admin\Billing\PaymentManageController::class, 'payAll'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_PAY_ALL)
                ->whereNumber('invoiceId')
            ;
            Route::get('/unallocated-transactions/{accountId}', [Controllers\Admin\Billing\PaymentManageController::class, 'unallocatedTransactions'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_UNALLOCATED_TRANSACTIONS)
                ->whereNumber('accountId')
            ;
            Route::get('/account-balance/{accountId}', [Controllers\Admin\Billing\PaymentManageController::class, 'accountBalance'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_ACCOUNT_BALANCE)
                ->whereNumber('accountId')
            ;
            Route::post('/pay-claim', [Controllers\Admin\Billing\PaymentManageController::class, 'payClaim'])
                ->name(RouteNames::ADMIN_PAYMENT_MANAGE_PAY_CLAIM)
            ;
        });
    });
});
