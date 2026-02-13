<?php

use App\Http\Controllers\ApprovalsController;
use Illuminate\Support\Facades\Route;

/**
 * Admin Approvals Routes
 * 
 * Handles all approval-related functionality including:
 * @pk305
 */
Route::prefix('admin')->group(function () {

    Route::controller(ApprovalsController::class)->group(function () {
        Route::get('approvals', 'index')->name('approvals.index');
        Route::get('approvals/data', 'approvalDatatable')->name('admin.approvals.data');

        Route::post('approvals/send', 'sendForApproval')->name('admin.approvals.send');
        Route::post('approvals/action', 'approvalAction')->name('admin.approvals.action');
        Route::post('approvals/bd-action', 'bdApprovalAction')->name('admin.approvals.bd-action');
        Route::get('approvals/{id}/details', 'getApprovalDetails')->name('admin.approvals.details');
    });
});
