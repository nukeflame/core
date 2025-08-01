<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'reports', 'as' => 'reports.', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::get('covers-by-type/data', [CoverReportController::class, 'getCoversByTypeData'])->name('covers_by_type.data');
    Route::get('covers-endings/data', [CoverReportController::class, 'getCoversEndingData'])->name('covers_ending.data');
    Route::get('covers-renewed/data', [CoverReportController::class, 'getCoversRenewdData'])->name('covers_renewed.data');
});
