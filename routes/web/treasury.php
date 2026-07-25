<?php declare(strict_types=1);

use App\Http\Controllers\Treasury\TreasuryController;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'treasury'], static function () {
    Route::get('/', [TreasuryController::class, 'index'])->name(RouteNames::TREASURY_INDEX);
    Route::post('/search', [TreasuryController::class, 'search'])->name(RouteNames::TREASURY_SEARCH);
    Route::get('/accounts/{accountId}/charges', [TreasuryController::class, 'charges'])->name(RouteNames::TREASURY_CHARGES);
    Route::post('/pay', [TreasuryController::class, 'pay'])->name(RouteNames::TREASURY_PAY);
    Route::get('/accounts/{accountId}/counters', [TreasuryController::class, 'counters'])->name(RouteNames::TREASURY_COUNTERS);
    Route::post('/counter/create', [TreasuryController::class, 'createCounter'])->name(RouteNames::TREASURY_COUNTER_CREATE);
    Route::post('/counter/add-value', [TreasuryController::class, 'addCounterValue'])->name(RouteNames::TREASURY_COUNTER_ADD_VALUE);
    Route::post('/counter/update', [TreasuryController::class, 'updateCounter'])->name(RouteNames::TREASURY_COUNTER_UPDATE);
});
