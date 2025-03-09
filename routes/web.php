<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';
include __DIR__ . '/web/session.php';

Route::get('/', [Controllers\Pages\PagesController::class, 'index'])->name(RouteNames::INDEX);

Route::get('/contacts', [Controllers\Pages\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);
Route::get('/contacts/proposal', [Controllers\Pages\PagesController::class, 'proposal'])->name(RouteNames::PROPOSAL);
Route::post('/contacts/proposal', [Controllers\Proposal\ProposalController::class, 'create'])->name(RouteNames::PROPOSAL_CREATE);

Route::get('/garbage', [Controllers\Pages\PagesController::class, 'garbage'])->name(RouteNames::GARBAGE);
Route::get('/privacy', [Controllers\Pages\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);
Route::get('/regulation', [Controllers\Pages\PagesController::class, 'regulation'])->name(RouteNames::REGULATION);
Route::get('/rubrics', [Controllers\Pages\PagesController::class, 'rubrics'])->name(RouteNames::RUBRICS);
Route::get('/search', [Controllers\Pages\PagesController::class, 'search'])->name(RouteNames::SEARCH);

Route::group(['prefix' => 'pages'], function () {
    Route::group(['prefix' => 'json'], function () {
        Route::post('/edit', [Controllers\Pages\TemplateController::class, 'get'])->name(RouteNames::TEMPLATE_GET);
        Route::patch('/edit', [Controllers\Pages\TemplateController::class, 'update'])->name(RouteNames::TEMPLATE_UPDATE);
    });
});

Route::group(['prefix' => 'search'], function () {
    Route::group(['prefix' => 'json'], function () {
        Route::post('/search', [Controllers\Pages\SearchController::class, 'search'])->name(RouteNames::SITE_SEARCH);
    });
});

Route::group(['prefix' => 'home'], function () {
    Route::group(['middleware' => MiddlewareNames::AUTH], function () {
        Route::get('/', [Controllers\Account\AccountsController::class, 'index'])->name(RouteNames::HOME);

        Route::group(['prefix' => 'profile'], function () {
            Route::get('/', [Controllers\Account\ProfileController::class, 'show'])->name(RouteNames::PROFILE);
            Route::post('/', [Controllers\Account\ProfileController::class, 'save'])->name(RouteNames::PROFILE_SAVE);
            Route::post('/email', [Controllers\Account\ProfileController::class, 'saveEmail'])->name(RouteNames::PROFILE_SAVE_EMAIL);
            Route::post('/password', [Controllers\Account\ProfileController::class, 'savePassword'])->name(RouteNames::PROFILE_SAVE_PASSWORD);
        });

        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
            Route::get('/register', [Controllers\Account\RegisterController::class, 'index'])->name(RouteNames::ACCOUNT_REGISTER);
            Route::group(['prefix' => 'json'], function () {
                Route::post('/register', [Controllers\Account\RegisterController::class, 'register'])->name(RouteNames::ACCOUNT_REGISTER_SAVE);
                Route::get('/account-info', [Controllers\Account\AccountsController::class, 'info'])->name(RouteNames::ACCOUNT_INFO);
                Route::get('/counter/list', [Controllers\Account\CounterController::class, 'list'])->name(RouteNames::PROFILE_COUNTERS_LIST);
                Route::post('/counter', [Controllers\Account\CounterController::class, 'save'])->name(RouteNames::PROFILE_COUNTER_SAVE);
            });
        });
    });
});

Route::group(['middleware' => MiddlewareNames::AUTH], function () {
    Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
        Route::group(['prefix' => 'options'], function () {
            Route::get('/', [Controllers\Options\OptionsController::class, 'index'])->name(RouteNames::OPTIONS);
            Route::group(['prefix' => 'json'], function () {
                Route::get('/list', [Controllers\Options\OptionsController::class, 'list'])->name(RouteNames::OPTIONS_LIST);
                Route::post('/save', [Controllers\Options\OptionsController::class, 'save'])->name(RouteNames::OPTIONS_SAVE);
                Route::get('/edit/{id}', [Controllers\Options\OptionsController::class, 'edit'])->name(RouteNames::OPTIONS_EDIT);
            });
        });
    });
});

Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [Controllers\Reports\ReportsController::class, 'index'])->name(RouteNames::REPORTS);
        Route::group(['prefix' => 'json'], function () {
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

Route::group(['prefix' => 'news'], function () {
    Route::get('/', [Controllers\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::get('/{id}', [Controllers\News\NewsController::class, 'show'])->name(RouteNames::NEWS_SHOW);
    Route::group(['prefix' => 'json'], function () {
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
Route::group(['prefix' => 'announcements'], function () {
    Route::get('/', [Controllers\News\AnnouncementController::class, 'index'])->name(RouteNames::ANNOUNCEMENTS);
    Route::get('/{id}', [Controllers\News\AnnouncementController::class, 'show'])->name(RouteNames::ANNOUNCEMENTS_SHOW);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\News\AnnouncementController::class, 'list'])->name(RouteNames::ANNOUNCEMENTS_LIST);
    });
});

Route::group(['prefix' => 'files'], function () {
    Route::get('/{folder?}', [Controllers\Files\FolderController::class, 'index'])->name(RouteNames::FILES);

    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\Files\FileController::class, 'list'])->name(RouteNames::FILES_LIST);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
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

Route::group(['prefix' => 'folders'], function () {
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\Files\FolderController::class, 'list'])->name(RouteNames::FOLDERS_LIST);
        Route::get('/show/{id}', [Controllers\Files\FolderController::class, 'show'])->name(RouteNames::FOLDERS_SHOW);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
            Route::post('/save', [Controllers\Files\FolderController::class, 'save'])->name(RouteNames::FOLDERS_SAVE);
            Route::delete('/delete/{id}', [Controllers\Files\FolderController::class, 'delete'])->name(RouteNames::FOLDERS_DELETE);
        });
    });
});

Route::get('/history/changes', Controllers\Infra\HistoryChangesViewController::class)->name(RouteNames::HISTORY_CHANGES);

Route::group(['prefix' => 'admin'], static function () {
    Route::get('/', [Controllers\Admin\PagesController::class, 'index'])->name(RouteNames::ADMIN);
    Route::group(['prefix' => 'periods'], static function () {
        Route::get('/', [Controllers\Admin\PagesController::class, 'periods'])->name(RouteNames::ADMIN_PERIOD_INDEX);
        Route::get('/create', [Controllers\Admin\PeriodsController::class, 'create'])->name(RouteNames::ADMIN_PERIOD_CREATE);
        Route::get('/list', [Controllers\Admin\PeriodsController::class, 'list'])->name(RouteNames::ADMIN_PERIOD_LIST);
        Route::post('/save', [Controllers\Admin\PeriodsController::class, 'save'])->name(RouteNames::ADMIN_PERIOD_SAVE);
        Route::delete('/{id}', [Controllers\Admin\PeriodsController::class, 'delete'])->name(RouteNames::ADMIN_PERIOD_DELETE);
    });
    Route::group(['prefix' => 'accounts'], static function () {
        Route::get('/', [Controllers\Admin\PagesController::class, 'accounts'])->name(RouteNames::ADMIN_ACCOUNT_INDEX);
        Route::get('/create', [Controllers\Admin\AccountsController::class, 'create'])->name(RouteNames::ADMIN_ACCOUNT_CREATE);
        Route::get('/list', [Controllers\Admin\AccountsController::class, 'list'])->name(RouteNames::ADMIN_ACCOUNT_LIST);
        Route::post('/save', [Controllers\Admin\AccountsController::class, 'save'])->name(RouteNames::ADMIN_ACCOUNT_SAVE);
        Route::delete('/{id}', [Controllers\Admin\AccountsController::class, 'delete'])->name(RouteNames::ADMIN_ACCOUNT_DELETE);
    });
    Route::group(['prefix' => 'services'], static function () {
        Route::get('/', [Controllers\Admin\PagesController::class, 'services'])->name(RouteNames::ADMIN_SERVICE_INDEX);
        Route::get('/create', [Controllers\Admin\ServicesController::class, 'create'])->name(RouteNames::ADMIN_SERVICE_CREATE);
        Route::get('/list', [Controllers\Admin\ServicesController::class, 'list'])->name(RouteNames::ADMIN_SERVICE_LIST);
        Route::post('/save', [Controllers\Admin\ServicesController::class, 'save'])->name(RouteNames::ADMIN_SERVICE_SAVE);
        Route::delete('/{id}', [Controllers\Admin\ServicesController::class, 'delete'])->name(RouteNames::ADMIN_SERVICE_DELETE);
    });

    Route::group(['prefix' => 'invoices'], static function () {
        Route::get('/', [Controllers\Admin\PagesController::class, 'invoices'])->name(RouteNames::ADMIN_INVOICE_INDEX);
        Route::get('/view/{id}', [Controllers\Admin\InvoiceController::class, 'view'])->name(RouteNames::ADMIN_INVOICE_VIEW);
        Route::group(['prefix' => 'json'], static function () {
            Route::get('/create', [Controllers\Admin\InvoiceController::class, 'create'])->name(RouteNames::ADMIN_INVOICE_CREATE);
            Route::get('/list', [Controllers\Admin\InvoiceController::class, 'list'])->name(RouteNames::ADMIN_INVOICE_LIST);
            Route::post('/save', [Controllers\Admin\InvoiceController::class, 'save'])->name(RouteNames::ADMIN_INVOICE_SAVE);
            Route::delete('/delete/{id}', [Controllers\Admin\InvoiceController::class, 'delete'])->name(RouteNames::ADMIN_INVOICE_DELETE);
            Route::get('/get/{id}', [Controllers\Admin\InvoiceController::class, 'get'])->name(RouteNames::ADMIN_INVOICE_GET);

            Route::group(['prefix' => '{invoiceId}'], static function () {
                Route::group(['prefix' => 'transactions'], static function () {
                    Route::get('/create', [Controllers\Admin\TransactionController::class, 'create'])->name(RouteNames::ADMIN_TRANSACTION_CREATE);
                    Route::get('/list', [Controllers\Admin\TransactionController::class, 'list'])->name(RouteNames::ADMIN_TRANSACTION_LIST);
                    Route::post('/save', [Controllers\Admin\TransactionController::class, 'save'])->name(RouteNames::ADMIN_TRANSACTION_SAVE);
                    Route::delete('/delete/{id}', [Controllers\Admin\TransactionController::class, 'delete'])->name(RouteNames::ADMIN_TRANSACTION_DELETE);
                    Route::get('/get/{transactionId}', [Controllers\Admin\TransactionController::class, 'get'])->name(RouteNames::ADMIN_TRANSACTION_VIEW);
                });
                Route::group(['prefix' => 'payments'], static function () {
                    Route::get('/create', [Controllers\Admin\PaymentController::class, 'create'])->name(RouteNames::ADMIN_PAYMENT_CREATE);
                    Route::get('/list', [Controllers\Admin\PaymentController::class, 'list'])->name(RouteNames::ADMIN_PAYMENT_LIST);
                    Route::post('/save', [Controllers\Admin\PaymentController::class, 'save'])->name(RouteNames::ADMIN_PAYMENT_SAVE);
                    Route::delete('/delete/{id}', [Controllers\Admin\PaymentController::class, 'delete'])->name(RouteNames::ADMIN_PAYMENT_DELETE);
                    Route::get('/get/{paymentId}', [Controllers\Admin\PaymentController::class, 'get'])->name(RouteNames::ADMIN_PAYMENT_VIEW);
                });
            });
        });
    });
});