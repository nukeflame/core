<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'security', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::post('/authenticate_access_code', [AuthController::class, 'verifyAccessCode'])->name('security.authenticate_access_code');
    Route::post('/reset/sidebar', [AuthController::class, 'resetSidebar'])->name('reset.sidebar');
});
