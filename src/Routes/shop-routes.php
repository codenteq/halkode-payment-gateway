<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Webkul\Halkode\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web']], function () {

    /**
     * Halk Öde payment routes
     */
    Route::get('/halkode-redirect', [PaymentController::class, 'redirect'])->name('halkode.redirect');

    Route::get('/halkode-success', [PaymentController::class, 'success'])->name('halkode.success');

    Route::get('/halkode-cancel', [PaymentController::class, 'failure'])->name('halkode.cancel');

    Route::post('/halkode-callback', [PaymentController::class, 'callback'])->name('halkode.callback')
        ->withoutMiddleware([VerifyCsrfToken::class]);
});
