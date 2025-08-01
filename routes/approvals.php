
<?php

use App\Http\Controllers\ApprovalsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'approvals', 'middleware' => ['auth', 'check.first.login'], 'as' => 'approvals.'], function () {
    Route::get('index', [ApprovalsController::class, 'index'])->name('index');
    Route::post('send-for-approval', [ApprovalsController::class, 'sendForApproval'])->name('send-for-approval');
    Route::post('approval-action', [ApprovalsController::class, 'approvalAction'])->name('approval-action');
    Route::get('approval-data', [ApprovalsController::class, 'approvalDatatable'])->name('approval-data');

    Route::post('bd-approval-action', [ApprovalsController::class, 'bdApprovalAction'])->name('bd-approval-action');
});
