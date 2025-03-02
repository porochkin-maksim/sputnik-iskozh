<?php declare(strict_types=1);

use App\Http\Controllers\CookieController;
use App\Http\Controllers\SessionController;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::post('session', [SessionController::class, 'store'])->name(RouteNames::SESSION_STORE);
Route::post('cookie-agreement', [CookieController::class, 'cookieAgreement'])->name(RouteNames::COOKIE_AGREEMENT);
