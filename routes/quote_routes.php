<?php

use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'quote', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::get('/quotation-info', [QuotationController::class, 'index'])->name('quote.info');
});
