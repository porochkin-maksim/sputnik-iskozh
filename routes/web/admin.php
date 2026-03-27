<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Controllers\Admin\System\SentEmailController;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Core\Resources\Views\ViewNames;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => MiddlewareNames::AUTH, 'prefix' => 'admin'], static function () {
    Route::group(['middleware' => MiddlewareNames::ADMIN], static function () {
        // история изменений
        Route::get('/history/changes', Controllers\Admin\HistoryChangesViewController::class)->name(RouteNames::HISTORY_CHANGES);

        Route::group(['prefix' => 'json'], static function () {
            Route::group(['prefix' => 'selects'], static function () {
                Route::get('/accounts', [Controllers\Admin\SelectCollectionsController::class, 'accounts'])->name(RouteNames::ADMIN_SELECTS_ACCOUNTS);
                Route::get('/counters/{accountId?}', [Controllers\Admin\SelectCollectionsController::class, 'counters'])->name(RouteNames::ADMIN_SELECTS_COUNTERS);
            });
            Route::group(['prefix' => 'top-panel'], static function () {
                Route::get('/', [Controllers\Admin\TopPanelController::class, 'index'])->name(RouteNames::ADMIN_TOP_PANEL_INDEX);
                Route::post('/', [Controllers\Admin\TopPanelController::class, 'search'])->name(RouteNames::ADMIN_TOP_PANEL_SEARCH);
            });
        });

        Route::get('/', static function () {
            return view(ViewNames::ADMIN_PAGES_INDEX);
        })->name(RouteNames::ADMIN);

        Route::group(['prefix' => 'roles'], static function () {
            Route::get('/', [Controllers\Admin\System\RolesController::class, 'index'])->name(RouteNames::ADMIN_ROLE_INDEX);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/create', [Controllers\Admin\System\RolesController::class, 'create'])->name(RouteNames::ADMIN_ROLE_CREATE);
                Route::get('/list', [Controllers\Admin\System\RolesController::class, 'list'])->name(RouteNames::ADMIN_ROLE_LIST);
                Route::post('/save', [Controllers\Admin\System\RolesController::class, 'save'])->name(RouteNames::ADMIN_ROLE_SAVE);
                Route::delete('/{id}', [Controllers\Admin\System\RolesController::class, 'delete'])
                    ->name(RouteNames::ADMIN_ROLE_DELETE)
                    ->whereNumber('id')
                ;
            });
        });

        Route::group(['prefix' => 'options'], static function () {
            Route::get('/', [Controllers\Admin\System\OptionsController::class, 'index'])->name(RouteNames::ADMIN_OPTIONS_INDEX);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/list', [Controllers\Admin\System\OptionsController::class, 'list'])->name(RouteNames::ADMIN_OPTIONS_LIST);
                Route::post('/save', [Controllers\Admin\System\OptionsController::class, 'save'])->name(RouteNames::ADMIN_OPTIONS_SAVE);
            });
        });

        Route::group(['prefix' => 'qr'], static function () {
            Route::get('view/{uid}', [Controllers\Admin\System\QrCodeController::class, 'view'])->name(RouteNames::ADMIN_QR_VIEW);
        });

        Route::group(['prefix' => 'users'], static function () {
            Route::get('/', [Controllers\Admin\System\UsersController::class, 'index'])->name(RouteNames::ADMIN_USER_INDEX);
            Route::get('/view/{id?}', [Controllers\Admin\System\UsersController::class, 'view'])->name(RouteNames::ADMIN_USER_VIEW);
            Route::get('/export', [Controllers\Admin\System\UsersController::class, 'export'])->name(RouteNames::ADMIN_USER_EXPORT);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/list', [Controllers\Admin\System\UsersController::class, 'list'])->name(RouteNames::ADMIN_USER_LIST);
                Route::post('/save', [Controllers\Admin\System\UsersController::class, 'save'])->name(RouteNames::ADMIN_USER_SAVE);
                Route::post('/generate-email', [Controllers\Admin\System\UsersController::class, 'generateEmail'])->name(RouteNames::ADMIN_USER_GENERATE_EMAIL);
                Route::delete('/{id}', [Controllers\Admin\System\UsersController::class, 'delete'])
                    ->name(RouteNames::ADMIN_USER_DELETE)
                    ->whereNumber('id')
                ;
                Route::patch('/{id}', [Controllers\Admin\System\UsersController::class, 'restore'])
                    ->name(RouteNames::ADMIN_USER_RESTORE)
                    ->whereNumber('id')
                ;
                Route::post('/sendRestorePassword', [Controllers\Admin\System\UsersController::class, 'sendRestorePassword'])->name(RouteNames::ADMIN_USER_SEND_RESTORE_PASSWORD);
                Route::post('/send-invite-password', [Controllers\Admin\System\UsersController::class, 'sendInviteWithPassword'])->name(RouteNames::ADMIN_USER_SEND_INVITE_WITH_PASSWORD);
                Route::post('/qr/login/{userId}/{pin}', [Controllers\Admin\System\QrCodeController::class, 'makeLoginLink'])
                    ->name(RouteNames::ADMIN_LOGIN_LINK)
                    ->whereNumber('userId')
                ;
            });
        });

        // периоды
        Route::group(['prefix' => 'periods'], static function () {
            Route::get('/', [Controllers\Admin\Billing\PeriodController::class, 'index'])->name(RouteNames::ADMIN_PERIOD_INDEX);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/create', [Controllers\Admin\Billing\PeriodController::class, 'create'])->name(RouteNames::ADMIN_PERIOD_CREATE);
                Route::get('/list', [Controllers\Admin\Billing\PeriodController::class, 'list'])->name(RouteNames::ADMIN_PERIOD_LIST);
                Route::post('/save', [Controllers\Admin\Billing\PeriodController::class, 'save'])->name(RouteNames::ADMIN_PERIOD_SAVE);
                Route::delete('/{id}', [Controllers\Admin\Billing\PeriodController::class, 'delete'])
                    ->name(RouteNames::ADMIN_PERIOD_DELETE)
                    ->whereNumber('id')
                ;
            });
        });

        // услуги периодов
        Route::group(['prefix' => 'services'], static function () {
            Route::get('/', [Controllers\Admin\Billing\ServiceController::class, 'index'])->name(RouteNames::ADMIN_SERVICE_INDEX);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/create', [Controllers\Admin\Billing\ServiceController::class, 'create'])->name(RouteNames::ADMIN_SERVICE_CREATE);
                Route::get('/list', [Controllers\Admin\Billing\ServiceController::class, 'list'])->name(RouteNames::ADMIN_SERVICE_LIST);
                Route::post('/save', [Controllers\Admin\Billing\ServiceController::class, 'save'])->name(RouteNames::ADMIN_SERVICE_SAVE);
                Route::delete('/{id}', [Controllers\Admin\Billing\ServiceController::class, 'delete'])
                    ->name(RouteNames::ADMIN_SERVICE_DELETE)
                    ->whereNumber('id')
                ;
            });
        });

        // участки
        Route::group(['prefix' => 'accounts'], static function () {
            Route::get('/', [Controllers\Admin\Account\AccountsController::class, 'index'])->name(RouteNames::ADMIN_ACCOUNT_INDEX);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/view/{accountId}', [Controllers\Admin\Account\AccountsController::class, 'get'])
                    ->name(RouteNames::ADMIN_ACCOUNT_GET)
                    ->whereNumber('accountId')
                ;
                Route::get('/create', [Controllers\Admin\Account\AccountsController::class, 'create'])->name(RouteNames::ADMIN_ACCOUNT_CREATE);
                Route::get('/list', [Controllers\Admin\Account\AccountsController::class, 'list'])->name(RouteNames::ADMIN_ACCOUNT_LIST);
                Route::post('/save', [Controllers\Admin\Account\AccountsController::class, 'save'])->name(RouteNames::ADMIN_ACCOUNT_SAVE);
                Route::delete('/{id}', [Controllers\Admin\Account\AccountsController::class, 'delete'])
                    ->name(RouteNames::ADMIN_ACCOUNT_DELETE)
                    ->whereNumber('id')
                ;
            });
            Route::group(['prefix' => 'view/{accountId}', 'where' => ['accountId' => '[0-9]+']], static function () {
                Route::get('/', [Controllers\Admin\Account\AccountsController::class, 'view'])->name(RouteNames::ADMIN_ACCOUNT_VIEW);
                Route::group(['prefix' => 'counters'], static function () {
                    Route::get('/view/{counterId}', [Controllers\Admin\Account\CounterController::class, 'view'])
                        ->name(RouteNames::ADMIN_COUNTER_VIEW)
                        ->whereNumber('counterId')
                    ;
                    Route::group(['prefix' => 'json'], static function () {
                        Route::post('/create', [Controllers\Admin\Account\CounterController::class, 'create'])->name(RouteNames::ADMIN_COUNTER_CREATE);
                        Route::get('/list', [Controllers\Admin\Account\CounterController::class, 'list'])->name(RouteNames::ADMIN_COUNTER_LIST);
                        Route::post('/save', [Controllers\Admin\Account\CounterController::class, 'save'])->name(RouteNames::ADMIN_COUNTER_SAVE);
                        Route::post('/delete/{counterId}', [Controllers\Admin\Account\CounterController::class, 'delete'])
                            ->name(RouteNames::ADMIN_COUNTER_DELETE)
                            ->whereNumber('counterId')
                        ;
                        Route::post('/add-value', [Controllers\Admin\Account\CounterController::class, 'addValue'])->name(RouteNames::ADMIN_COUNTER_ADD_VALUE);
                    });
                });
                Route::group(['prefix' => 'invoices'], static function () {
                    Route::group(['prefix' => 'json'], static function () {
                        Route::get('/list', [Controllers\Admin\Account\InvoiceController::class, 'list'])->name(RouteNames::ADMIN_ACCOUNT_INVOICE_LIST);
                    });
                });
            })->whereNumber('accountId');
        });

        Route::group(['prefix' => 'counters'], static function () {
            Route::group(['prefix' => 'json'], static function () {
                Route::group(['prefix' => '{counterId}'], static function () {
                    Route::group(['prefix' => 'history'], static function () {
                        Route::get('/list', [Controllers\Admin\Account\CounterHistoryController::class, 'list'])
                            ->name(RouteNames::ADMIN_COUNTER_HISTORY_LIST)
                            ->whereNumber('counterId')
                        ;
                    });
                });
            });
        });

        Route::group(['prefix' => 'invoices'], static function () {
            Route::get('/', [Controllers\Admin\Billing\InvoiceController::class, 'index'])->name(RouteNames::ADMIN_INVOICE_INDEX);

            Route::group(['prefix' => '/view/{id}'], static function () {
                Route::get('/', [Controllers\Admin\Billing\InvoiceController::class, 'view'])->name(RouteNames::ADMIN_INVOICE_VIEW);
                Route::get('/invoice-receipt', [Controllers\Common\Documents\ReceiptController::class, 'makeByInvoiceId'])->name(RouteNames::ADMIN_DOCUMENT_RECEIPT_INVOICE);
            })->whereNumber('id');

            Route::get('/export', [Controllers\Admin\Billing\InvoiceController::class, 'export'])->name(RouteNames::ADMIN_INVOICE_EXPORT);

            Route::group(['prefix' => '/import-payments/period-{periodId}'], static function () {
                Route::get('/', [Controllers\Admin\Billing\PaymentImportController::class, 'index'])->name(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX);
                Route::post('/parse-file', [Controllers\Admin\Billing\PaymentImportController::class, 'parseFile'])->name(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_PARSE_FILE);
                Route::post('/save', [Controllers\Admin\Billing\PaymentImportController::class, 'save'])->name(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_SAVE);
            })->whereNumber('periodId');

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
                        Route::get('/create', [Controllers\Admin\Billing\PaymentController::class, 'create'])
                            ->name(RouteNames::ADMIN_PAYMENT_CREATE)
                        ;
                        Route::get('/auto-create', [Controllers\Admin\Billing\PaymentController::class, 'autoCreate'])
                            ->name(RouteNames::ADMIN_PAYMENT_AUTO_CREATE)
                        ;
                        Route::get('/list', [Controllers\Admin\Billing\PaymentController::class, 'list'])
                            ->name(RouteNames::ADMIN_PAYMENT_LIST)
                        ;
                        Route::post('/save', [Controllers\Admin\Billing\PaymentController::class, 'save'])
                            ->name(RouteNames::ADMIN_PAYMENT_SAVE)
                        ;
                        Route::delete('/delete/{id}', [Controllers\Admin\Billing\PaymentController::class, 'delete'])
                            ->name(RouteNames::ADMIN_PAYMENT_DELETE)
                            ->whereNumber('id')
                        ;
                        Route::get('/get/{paymentId}', [Controllers\Admin\Billing\PaymentController::class, 'get'])
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
                    Route::get('/list', [Controllers\Admin\Requests\NewPaymentController::class, 'list'])->name(RouteNames::ADMIN_NEW_PAYMENT_LIST);
                    Route::get('/get-invoices/{accountId}/{periodId}', [Controllers\Admin\Requests\NewPaymentController::class, 'getInvoices'])
                        ->name(RouteNames::ADMIN_NEW_PAYMENT_INVOICES)
                        ->whereNumber('accountId')
                        ->whereNumber('periodId')
                    ;
                    Route::post('/save', [Controllers\Admin\Requests\NewPaymentController::class, 'save'])->name(RouteNames::ADMIN_NEW_PAYMENT_SAVE);
                    Route::delete('/delete/{id}', [Controllers\Admin\Requests\NewPaymentController::class, 'delete'])
                        ->name(RouteNames::ADMIN_NEW_PAYMENT_DELETE)
                        ->whereNumber('id')
                    ;
                    Route::get('/get/{paymentId}', [Controllers\Admin\Requests\NewPaymentController::class, 'get'])
                        ->name(RouteNames::ADMIN_NEW_PAYMENT_VIEW)
                        ->whereNumber('paymentId')
                    ;
                });
            });
        });

        // необработанные показания счётчиков
        Route::group(['prefix' => 'counter-history'], static function () {
            Route::get('/', [Controllers\Admin\Account\CounterController::class, 'index'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_INDEX);
            Route::group(['prefix' => 'json'], static function () {
                Route::post('/create-claim/{historyId}', [Controllers\Admin\Account\CounterController::class, 'createClaim'])
                    ->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_CREATE_CLAIM)
                    ->whereNumber('historyId')
                ;
                Route::get('/list', [Controllers\Admin\Requests\CounterController::class, 'list'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_LIST);
                Route::post('/link', [Controllers\Admin\Requests\CounterController::class, 'link'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_LINK);
                Route::delete('/delete/{historyId}', [Controllers\Admin\Requests\CounterController::class, 'delete'])
                    ->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_DELETE)
                    ->whereNumber('historyId')
                ;
                Route::post('/confirm', [Controllers\Admin\Requests\CounterController::class, 'confirm'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_CONFIRM);
                Route::post('/confirm-delete', [Controllers\Admin\Requests\CounterController::class, 'confirmDelete'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_CONFIRM_DELETE);
            });
        });

        // просмотр истории отправки писем
        Route::resource('emails', SentEmailController::class)
            ->only(['index', 'show', 'destroy'])
            ->names([
                'index'   => 'admin.emails.index',
                'show'    => 'admin.emails.show',
                'destroy' => 'admin.emails.destroy',
            ])
        ;

        // просмотр ошибок
        Route::get('error-logs', [Controllers\Admin\System\ErrorLogsController::class, 'index'])->name('admin.error-logs.index');
        Route::get('error-logs/{filename}', [Controllers\Admin\System\ErrorLogsController::class, 'show'])->name('admin.error-logs.show');
        Route::get('error-logs/{filename}/details/{index}', [Controllers\Admin\System\ErrorLogsController::class, 'details'])->name('admin.error-logs.details');

        // Управление очередями
        Route::group(['prefix' => 'queue'], static function () {
            Route::get('/', [Controllers\Admin\QueueController::class, 'index'])->name(RouteNames::ADMIN_QUEUE);
            Route::get('/status', [Controllers\Admin\QueueController::class, 'status'])->name(RouteNames::ADMIN_QUEUE_STATUS);
            Route::post('/start', [Controllers\Admin\QueueController::class, 'start'])->name(RouteNames::ADMIN_QUEUE_START);
            Route::post('/stop', [Controllers\Admin\QueueController::class, 'stop'])->name(RouteNames::ADMIN_QUEUE_STOP);
            Route::post('/clear', [Controllers\Admin\QueueController::class, 'clear'])->name(RouteNames::ADMIN_QUEUE_CLEAR);
        });
    });
});