<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

if (config('app.env') === 'local') {
    usleep(800);
}

Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
Route::post('password/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkResponse'])->name('passwords.sent');
Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'sendResetResponse'])->name('passwords.reset');

Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::group([
        'prefix' => 'user',
        'as' => 'auth.',
    ], function () {
        Route::post('me', [AuthController::class, 'me'])->name('me');
        Route::put('update', [AuthController::class, 'update'])->name('update');
        Route::post('update-password', [AuthController::class, 'updatePassword'])->name('updatePassword');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::apiResources([
    ]);
});

// not found fallback
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'status' => 'not_found',
        'message' => 'Route tidak ditemukan !',
        'data' => null,
    ], 404);
});
