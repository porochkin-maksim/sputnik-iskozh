<?php declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SetPasswordController;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Auth::routes(
    [
        'verify' => true,
        'logout' => false,
        'login'  => false,
    ],
);

Route::get('/login', static fn() => redirect()->route(RouteNames::INDEX));
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', LogoutController::class)->name('logout');
Route::get('/password/set', [SetPasswordController::class, 'index'])->name(RouteNames::PASSWORD_SET);
Route::post('/password/set', [SetPasswordController::class, 'set'])->name(RouteNames::PASSWORD_SAVE);
Route::post('/register', RegisterController::class);
Route::get('/register', static function () {
    abort(404);
});
