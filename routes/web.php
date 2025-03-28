<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';
include __DIR__ . '/web/session.php';

Route::get('/', [Controllers\Pages\PagesController::class, 'index'])->name(RouteNames::INDEX);

Route::get('/contacts', [Controllers\Pages\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);
Route::group(['prefix' => 'contacts/requests'], static function () {
    Route::get('/', [Controllers\Pages\RequestsPagesController::class, 'index'])->name(RouteNames::REQUESTS);
    Route::get('/proposal', [Controllers\Pages\RequestsPagesController::class, 'proposal'])->name(RouteNames::PROPOSAL);
    Route::post('/proposal', [Controllers\Pages\Requests\ProposalController::class, 'create'])->name(RouteNames::PROPOSAL_CREATE);
    Route::get('/payment', [Controllers\Pages\RequestsPagesController::class, 'payment'])->name(RouteNames::PAYMENT);
    Route::post('/payment', [Controllers\Pages\Requests\PaymentsController::class, 'create'])->name(RouteNames::PAYMENT_CREATE);
    Route::get('/counter', [Controllers\Pages\RequestsPagesController::class, 'counter'])->name(RouteNames::COUNTER);
    Route::post('/counter', [Controllers\Pages\Requests\CounterController::class, 'create'])->name(RouteNames::COUNTER_CREATE);
});

Route::get('/garbage', [Controllers\Pages\PagesController::class, 'garbage'])->name(RouteNames::GARBAGE);
Route::get('/privacy', [Controllers\Pages\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);
Route::get('/regulation', [Controllers\Pages\PagesController::class, 'regulation'])->name(RouteNames::REGULATION);
Route::get('/rubrics', [Controllers\Pages\PagesController::class, 'rubrics'])->name(RouteNames::RUBRICS);
Route::get('/search', [Controllers\Pages\PagesController::class, 'search'])->name(RouteNames::SEARCH);

Route::group(['prefix' => 'pages'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/edit', [Controllers\Pages\TemplateController::class, 'get'])->name(RouteNames::TEMPLATE_GET);
        Route::patch('/edit', [Controllers\Pages\TemplateController::class, 'update'])->name(RouteNames::TEMPLATE_UPDATE);
    });
});

Route::group(['prefix' => 'search'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/search', [Controllers\Pages\SearchController::class, 'search'])->name(RouteNames::SITE_SEARCH);
    });
});

Route::group(['prefix' => 'home'], static function () {
    Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
        Route::get('/', [Controllers\Account\AccountsController::class, 'index'])->name(RouteNames::HOME);

        Route::group(['prefix' => 'profile'], static function () {
            Route::get('/', [Controllers\Account\ProfileController::class, 'show'])->name(RouteNames::PROFILE);
            Route::post('/', [Controllers\Account\ProfileController::class, 'save'])->name(RouteNames::PROFILE_SAVE);
            Route::post('/email', [Controllers\Account\ProfileController::class, 'saveEmail'])->name(RouteNames::PROFILE_SAVE_EMAIL);
            Route::post('/password', [Controllers\Account\ProfileController::class, 'savePassword'])->name(RouteNames::PROFILE_SAVE_PASSWORD);
        });

        Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
            Route::get('/register', [Controllers\Account\RegisterController::class, 'index'])->name(RouteNames::ACCOUNT_REGISTER);
            Route::group(['prefix' => 'json'], static function () {
                Route::post('/register', [Controllers\Account\RegisterController::class, 'register'])->name(RouteNames::ACCOUNT_REGISTER_SAVE);
            });
            Route::group(['prefix' => 'counters'], static function () {
                Route::get('/', [Controllers\Account\CounterController::class, 'index'])->name(RouteNames::PROFILE_COUNTERS);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/list', [Controllers\Account\CounterController::class, 'list'])->name(RouteNames::PROFILE_COUNTERS_LIST);
                    Route::post('/create', [Controllers\Account\CounterController::class, 'create'])->name(RouteNames::PROFILE_COUNTER_CREATE);
                    Route::post('/add-value', [Controllers\Account\CounterController::class, 'addValue'])->name(RouteNames::PROFILE_COUNTER_ADD_VALUE);
                });
            });
            Route::group(['prefix' => 'payments'], static function () {
                Route::get('/', [Controllers\Account\PaymentController::class, 'index'])->name(RouteNames::PROFILE_PAYMENTS);
            });
        });
    });
});

Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
    Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
        Route::group(['prefix' => 'options'], static function () {
            Route::get('/', [Controllers\Options\OptionsController::class, 'index'])->name(RouteNames::OPTIONS);
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/list', [Controllers\Options\OptionsController::class, 'list'])->name(RouteNames::OPTIONS_LIST);
                Route::post('/save', [Controllers\Options\OptionsController::class, 'save'])->name(RouteNames::OPTIONS_SAVE);
                Route::get('/edit/{id}', [Controllers\Options\OptionsController::class, 'edit'])->name(RouteNames::OPTIONS_EDIT);
            });
        });
    });
});

Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
    Route::group(['prefix' => 'reports'], static function () {
        Route::get('/', [Controllers\Reports\ReportsController::class, 'index'])->name(RouteNames::REPORTS);
        Route::group(['prefix' => 'json'], static function () {
            Route::get('/list', [Controllers\Reports\ReportsController::class, 'list'])->name(RouteNames::REPORTS_LIST);
            Route::get('/create', [Controllers\Reports\ReportsController::class, 'create'])->name(RouteNames::REPORTS_CREATE);
            Route::post('/save', [Controllers\Reports\ReportsController::class, 'save'])->name(RouteNames::REPORTS_SAVE);
            Route::get('/edit/{id}', [Controllers\Reports\ReportsController::class, 'edit'])->name(RouteNames::REPORTS_EDIT);
            Route::delete('/delete/{id}', [Controllers\Reports\ReportsController::class, 'delete'])->name(RouteNames::REPORTS_DELETE);
            Route::post('/file/upload/{id}', [Controllers\Reports\ReportsController::class, 'uploadFile'])->name(RouteNames::REPORTS_FILE_UPLOAD);
            Route::post('/file/delete/{id}', [Controllers\Files\FileController::class, 'delete'])->name(RouteNames::REPORTS_FILE_DELETE);
        });
    });
});

Route::group(['prefix' => 'news'], static function () {
    Route::get('/', [Controllers\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::get('/{id}', [Controllers\News\NewsController::class, 'show'])->name(RouteNames::NEWS_SHOW);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\News\NewsController::class, 'list'])->name(RouteNames::NEWS_LIST);
        Route::get('/list/all', [Controllers\News\NewsController::class, 'listAll'])->name(RouteNames::NEWS_LIST_ALL);
        Route::get('/list/locked', [Controllers\News\NewsController::class, 'lockedNews'])->name(RouteNames::NEWS_LIST_LOCKED);
        Route::get('/create', [Controllers\News\NewsController::class, 'create'])->name(RouteNames::NEWS_CREATE);
        Route::post('/save', [Controllers\News\NewsController::class, 'save'])->name(RouteNames::NEWS_SAVE);
        Route::get('/edit/{id}', [Controllers\News\NewsController::class, 'edit'])->name(RouteNames::NEWS_EDIT);
        Route::delete('/delete/{id}', [Controllers\News\NewsController::class, 'delete'])->name(RouteNames::NEWS_DELETE);
        Route::post('/file/save', [Controllers\News\NewsController::class, 'saveFile'])->name(RouteNames::NEWS_FILE_SAVE);
        Route::post('/file/upload/{id}', [Controllers\News\NewsController::class, 'uploadFile'])->name(RouteNames::NEWS_FILE_UPLOAD);
        Route::delete('/file/delete/{id}', [Controllers\News\NewsController::class, 'deleteFile'])->name(RouteNames::NEWS_FILE_DELETE);
    });
});
Route::group(['prefix' => 'announcements'], static function () {
    Route::get('/', [Controllers\News\AnnouncementController::class, 'index'])->name(RouteNames::ANNOUNCEMENTS);
    Route::get('/{id}', [Controllers\News\AnnouncementController::class, 'show'])->name(RouteNames::ANNOUNCEMENTS_SHOW);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\News\AnnouncementController::class, 'list'])->name(RouteNames::ANNOUNCEMENTS_LIST);
    });
});

Route::group(['prefix' => 'files'], static function () {
    Route::get('/{folder?}', [Controllers\Files\FolderController::class, 'index'])->name(RouteNames::FILES);

    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Files\FileController::class, 'list'])->name(RouteNames::FILES_LIST);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
            Route::post('/store', [Controllers\Files\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
            Route::post('/save', [Controllers\Files\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
            Route::post('/replace', [Controllers\Files\FileController::class, 'replace'])->name(RouteNames::FILES_REPLACE);
            Route::post('/up/{id}', [Controllers\Files\FileController::class, 'up'])->name(RouteNames::FILES_UP);
            Route::post('/down/{id}', [Controllers\Files\FileController::class, 'down'])->name(RouteNames::FILES_DOWN);
            Route::post('/move', [Controllers\Files\FileController::class, 'move'])->name(RouteNames::FILES_MOVE);
            Route::get('/edit/{id}', [Controllers\Files\FileController::class, 'edit'])->name(RouteNames::FILES_EDIT);
            Route::delete('/delete/{id}', [Controllers\Files\FileController::class, 'delete'])->name(RouteNames::FILES_DELETE);
        });
    });
});

Route::group(['prefix' => 'folders'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Files\FolderController::class, 'list'])->name(RouteNames::FOLDERS_LIST);
        Route::get('/show/{id}', [Controllers\Files\FolderController::class, 'show'])->name(RouteNames::FOLDERS_SHOW);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
            Route::post('/save', [Controllers\Files\FolderController::class, 'save'])->name(RouteNames::FOLDERS_SAVE);
            Route::delete('/delete/{id}', [Controllers\Files\FolderController::class, 'delete'])->name(RouteNames::FOLDERS_DELETE);
        });
    });
});

Route::get('/storage/{filePath}', [App\Http\Controllers\FileController::class, 'download'])->where('filePath', '.*');

Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
    Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
        Route::get('/history/changes', Controllers\Infra\HistoryChangesViewController::class)->name(RouteNames::HISTORY_CHANGES);
        Route::group(['prefix' => 'admin'], static function () {
            Route::group(['prefix' => 'json'], static function () {
                Route::group(['prefix' => 'selects'], static function () {
                    Route::get('/accounts', [Controllers\Admin\SelectCollectionsController::class, 'accounts'])->name(RouteNames::ADMIN_SELECTS_ACCOUNTS);
                    Route::get('/counters/{accountId?}', [Controllers\Admin\SelectCollectionsController::class, 'counters'])->name(RouteNames::ADMIN_SELECTS_COUNTERS);
                });
            });
            Route::get('/', [Controllers\Admin\PagesController::class, 'index'])->name(RouteNames::ADMIN);
            Route::group(['prefix' => 'roles'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'roles'])->name(RouteNames::ADMIN_ROLE_INDEX);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/create', [Controllers\Admin\System\RolesController::class, 'create'])->name(RouteNames::ADMIN_ROLE_CREATE);
                    Route::get('/list', [Controllers\Admin\System\RolesController::class, 'list'])->name(RouteNames::ADMIN_ROLE_LIST);
                    Route::post('/save', [Controllers\Admin\System\RolesController::class, 'save'])->name(RouteNames::ADMIN_ROLE_SAVE);
                    Route::delete('/{id}', [Controllers\Admin\System\RolesController::class, 'delete'])->name(RouteNames::ADMIN_ROLE_DELETE);
                });
            });
            Route::group(['prefix' => 'users'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'users'])->name(RouteNames::ADMIN_USER_INDEX);
                Route::get('/view/{id?}', [Controllers\Admin\System\UsersController::class, 'view'])->name(RouteNames::ADMIN_USER_VIEW);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/create', [Controllers\Admin\System\UsersController::class, 'create'])->name(RouteNames::ADMIN_USER_CREATE);
                    Route::get('/list', [Controllers\Admin\System\UsersController::class, 'list'])->name(RouteNames::ADMIN_USER_LIST);
                    Route::post('/save', [Controllers\Admin\System\UsersController::class, 'save'])->name(RouteNames::ADMIN_USER_SAVE);
                    Route::delete('/{id}', [Controllers\Admin\System\UsersController::class, 'delete'])->name(RouteNames::ADMIN_USER_DELETE);
                    Route::post('/sendRestorePassword', [Controllers\Admin\System\UsersController::class, 'sendRestorePassword'])->name(RouteNames::ADMIN_USER_SEND_RESTORE_PASSWORD);
                    Route::post('/send-invite-password', [Controllers\Admin\System\UsersController::class, 'sendInviteWithPassword'])->name(RouteNames::ADMIN_USER_SEND_INVITE_WITH_PASSWORD);
                });
            });
            Route::group(['prefix' => 'periods'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'periods'])->name(RouteNames::ADMIN_PERIOD_INDEX);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/create', [Controllers\Admin\System\PeriodsController::class, 'create'])->name(RouteNames::ADMIN_PERIOD_CREATE);
                    Route::get('/list', [Controllers\Admin\System\PeriodsController::class, 'list'])->name(RouteNames::ADMIN_PERIOD_LIST);
                    Route::post('/save', [Controllers\Admin\System\PeriodsController::class, 'save'])->name(RouteNames::ADMIN_PERIOD_SAVE);
                    Route::delete('/{id}', [Controllers\Admin\System\PeriodsController::class, 'delete'])->name(RouteNames::ADMIN_PERIOD_DELETE);
                });
            });
            Route::group(['prefix' => 'accounts'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'accounts'])->name(RouteNames::ADMIN_ACCOUNT_INDEX);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/view/{accountId}', [Controllers\Admin\Account\AccountsController::class, 'get'])->name(RouteNames::ADMIN_ACCOUNT_GET);
                    Route::get('/create', [Controllers\Admin\Account\AccountsController::class, 'create'])->name(RouteNames::ADMIN_ACCOUNT_CREATE);
                    Route::get('/list', [Controllers\Admin\Account\AccountsController::class, 'list'])->name(RouteNames::ADMIN_ACCOUNT_LIST);
                    Route::post('/save', [Controllers\Admin\Account\AccountsController::class, 'save'])->name(RouteNames::ADMIN_ACCOUNT_SAVE);
                    Route::delete('/{id}', [Controllers\Admin\Account\AccountsController::class, 'delete'])->name(RouteNames::ADMIN_ACCOUNT_DELETE);
                });
                Route::group(['prefix' => 'view/{accountId}'], static function () {
                    Route::get('/', [Controllers\Admin\Account\AccountsController::class, 'view'])->name(RouteNames::ADMIN_ACCOUNT_VIEW);
                    Route::group(['prefix' => 'json'], static function () {
                        Route::post('/create', [Controllers\Admin\Account\CounterController::class, 'create'])->name(RouteNames::ADMIN_COUNTER_CREATE);
                        Route::get('/list', [Controllers\Admin\Account\CounterController::class, 'list'])->name(RouteNames::ADMIN_COUNTER_LIST);
                        Route::post('/save', [Controllers\Admin\Account\CounterController::class, 'save'])->name(RouteNames::ADMIN_COUNTER_SAVE);
                        Route::delete('/{id}', [Controllers\Admin\Account\CounterController::class, 'delete'])->name(RouteNames::ADMIN_COUNTER_DELETE);
                        Route::post('/add-value', [Controllers\Admin\Account\CounterController::class, 'addValue'])->name(RouteNames::ADMIN_COUNTER_ADD_VALUE);
                    });
                });
            });
            Route::group(['prefix' => 'services'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'services'])->name(RouteNames::ADMIN_SERVICE_INDEX);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/create', [Controllers\Admin\System\ServicesController::class, 'create'])->name(RouteNames::ADMIN_SERVICE_CREATE);
                    Route::get('/list', [Controllers\Admin\System\ServicesController::class, 'list'])->name(RouteNames::ADMIN_SERVICE_LIST);
                    Route::post('/save', [Controllers\Admin\System\ServicesController::class, 'save'])->name(RouteNames::ADMIN_SERVICE_SAVE);
                    Route::delete('/{id}', [Controllers\Admin\System\ServicesController::class, 'delete'])->name(RouteNames::ADMIN_SERVICE_DELETE);
                });
            });
            Route::group(['prefix' => 'invoices'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'invoices'])->name(RouteNames::ADMIN_INVOICE_INDEX);
                Route::get('/view/{id}', [Controllers\Admin\Billing\InvoiceController::class, 'view'])->name(RouteNames::ADMIN_INVOICE_VIEW);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/summary', [Controllers\Admin\Billing\InvoiceController::class, 'summary'])->name(RouteNames::ADMIN_INVOICE_SUMMARY);
                    Route::get('/get-without-regular/{periodId}', [Controllers\Admin\Billing\InvoiceController::class, 'getAccountCountWithoutRegular'])->name(RouteNames::ADMIN_INVOICE_GET_ACCOUNTS_COUNT_WITHOUT_REGULAR);
                    Route::post('/create-regular-invoices/{periodId}', [Controllers\Admin\Billing\InvoiceController::class, 'createRegularInvoices'])->name(RouteNames::ADMIN_INVOICE_CREATE_REGULAR_INVOICES);
                    Route::get('/create', [Controllers\Admin\Billing\InvoiceController::class, 'create'])->name(RouteNames::ADMIN_INVOICE_CREATE);
                    Route::get('/list', [Controllers\Admin\Billing\InvoiceController::class, 'list'])->name(RouteNames::ADMIN_INVOICE_LIST);
                    Route::post('/save', [Controllers\Admin\Billing\InvoiceController::class, 'save'])->name(RouteNames::ADMIN_INVOICE_SAVE);
                    Route::delete('/delete/{id}', [Controllers\Admin\Billing\InvoiceController::class, 'delete'])->name(RouteNames::ADMIN_INVOICE_DELETE);
                    Route::get('/get/{id}', [Controllers\Admin\Billing\InvoiceController::class, 'get'])->name(RouteNames::ADMIN_INVOICE_GET);

                    Route::group(['prefix' => '{invoiceId}'], static function () {
                        Route::group(['prefix' => 'transactions'], static function () {
                            Route::get('/create', [Controllers\Admin\Billing\TransactionController::class, 'create'])->name(RouteNames::ADMIN_TRANSACTION_CREATE);
                            Route::get('/list', [Controllers\Admin\Billing\TransactionController::class, 'list'])->name(RouteNames::ADMIN_TRANSACTION_LIST);
                            Route::post('/save', [Controllers\Admin\Billing\TransactionController::class, 'save'])->name(RouteNames::ADMIN_TRANSACTION_SAVE);
                            Route::delete('/delete/{id}', [Controllers\Admin\Billing\TransactionController::class, 'delete'])->name(RouteNames::ADMIN_TRANSACTION_DELETE);
                            Route::get('/get/{transactionId}', [Controllers\Admin\Billing\TransactionController::class, 'get'])->name(RouteNames::ADMIN_TRANSACTION_VIEW);
                        });
                        Route::group(['prefix' => 'payments'], static function () {
                            Route::get('/create', [Controllers\Admin\Billing\PaymentController::class, 'create'])->name(RouteNames::ADMIN_PAYMENT_CREATE);
                            Route::get('/list', [Controllers\Admin\Billing\PaymentController::class, 'list'])->name(RouteNames::ADMIN_PAYMENT_LIST);
                            Route::post('/save', [Controllers\Admin\Billing\PaymentController::class, 'save'])->name(RouteNames::ADMIN_PAYMENT_SAVE);
                            Route::delete('/delete/{id}', [Controllers\Admin\Billing\PaymentController::class, 'delete'])->name(RouteNames::ADMIN_PAYMENT_DELETE);
                            Route::get('/get/{paymentId}', [Controllers\Admin\Billing\PaymentController::class, 'get'])->name(RouteNames::ADMIN_PAYMENT_VIEW);
                        });
                    });
                });
                Route::group(['prefix' => 'payments'], static function () {
                    Route::get('/', [Controllers\Admin\PagesController::class, 'payments'])->name(RouteNames::ADMIN_NEW_PAYMENT_INDEX);
                    Route::group(['prefix' => 'json'], static function () {
                        Route::get('/list', [Controllers\Admin\Requests\NewPaymentController::class, 'list'])->name(RouteNames::ADMIN_NEW_PAYMENT_LIST);
                        Route::get('/get-invoices/{accountId}/{periodId}', [Controllers\Admin\Requests\NewPaymentController::class, 'getInvoices'])->name(RouteNames::ADMIN_NEW_PAYMENT_INVOICES);
                        Route::post('/save', [Controllers\Admin\Requests\NewPaymentController::class, 'save'])->name(RouteNames::ADMIN_NEW_PAYMENT_SAVE);
                        Route::delete('/delete/{id}', [Controllers\Admin\Requests\NewPaymentController::class, 'delete'])->name(RouteNames::ADMIN_NEW_PAYMENT_DELETE);
                        Route::get('/get/{paymentId}', [Controllers\Admin\Requests\NewPaymentController::class, 'get'])->name(RouteNames::ADMIN_NEW_PAYMENT_VIEW);
                    });
                });
            });
            Route::group(['prefix' => 'counter-history'], static function () {
                Route::get('/', [Controllers\Admin\PagesController::class, 'counterHistory'])->name(RouteNames::ADMIN_COUNTER_HISTORY_INDEX);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/list', [Controllers\Admin\Requests\NewCounterController::class, 'list'])->name(RouteNames::ADMIN_COUNTER_HISTORY_LIST);
                    Route::post('/link', [Controllers\Admin\Requests\NewCounterController::class, 'link'])->name(RouteNames::ADMIN_COUNTER_HISTORY_LINK);
                    Route::delete('/delete/{id}', [Controllers\Admin\Requests\NewCounterController::class, 'delete'])->name(RouteNames::ADMIN_COUNTER_HISTORY_DELETE);
                    Route::post('/confirm', [Controllers\Admin\Requests\NewCounterController::class, 'confirm'])->name(RouteNames::ADMIN_COUNTER_HISTORY_CONFIRM);
                });
            });
        });
    });
});