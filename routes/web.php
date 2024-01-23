<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\ViewNames;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';

Route::get('/', function () {
    // if (Auth::check() || true) {
        return view(ViewNames::PAGES_INDEX);
    // }
    // else {
    //     return view(ViewNames::LAYOUTS_APP);
    // }
})->name(RouteNames::INDEX);

Route::get('/reports', function () {
    return view(ViewNames::PAGES_REPORTS_INDEX);
})->name(RouteNames::REPORTS);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
