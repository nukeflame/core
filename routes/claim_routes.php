<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ClaimNotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'claim', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::any('/claim-form', [ClaimController::class, 'ClaimForm'])->name('claim.form');
    Route::post('/claim-register', [ClaimController::class, 'ClaimRegister'])->name('claim.register');
    Route::get('/claim-datatable', [ClaimController::class, 'ClaimDatatable'])->name('claim.datatable');
    Route::get('/get-loss-endorsements', [ClaimController::class, 'GetLossEndorsements'])->name('claim.get_loss_endorsements');
    Route::get('/get-endorsement-info', [ClaimController::class, 'GetEndorsementInfo'])->name('claim.get_endorsement_info');
    // Route::get('/claim-datatable', [ClaimController::class, 'ClaimDatatable'])->name('claim.datatable');
    Route::get('/claim-peril-datatable', [ClaimController::class, 'ClaimPerilDatatable'])->name('claim.peril_datatable');
    Route::get('/claim-reinsurer-datatable', [ClaimController::class, 'ClaimReinsurerDatatable'])->name('claim.reinsurersDatatable');
    Route::get('/claims-enquiry-datatable', [ClaimController::class, 'ClaimsEnquiryDatatable'])->name('claims.enquiry.datatable');
    Route::get('/claims-enquiry', [ClaimController::class, 'showClaimEnquiry'])->name('claim.enquiry');
    // Route::get('/details/{claim_no}', [ClaimController::class, 'ClaimDetails'])->name('claim.detail');
    Route::any('/details', [ClaimController::class, 'ClaimDetails'])->name('claim.detail');

    Route::post('/claim-save-peril', [ClaimController::class, 'savePeril'])->name('claim.saveperil');
    Route::post('/generate-debit', [ClaimController::class, 'generateDebit'])->name('claim.generate-debit');
    Route::get('/debits_datatable', [ClaimController::class, 'debits_datatable'])->name('claim.debits_datatable');

    Route::get('/attachments_datatable', [ClaimController::class, 'attachments_datatable'])->name('claim.attachments_datatable');
    Route::post('/add_attachment', [ClaimController::class, 'saveAttachment'])->name('claim.save_attachment');
    Route::match(['put', 'post'], '/amend_attachment', [ClaimController::class, 'amendAttachment'])->name('claim.amend_attachment');
    Route::post('/delete_attachment', [ClaimController::class, 'deleteAttachment'])->name('claim.delete_attachment');

    Route::get('/ack-docs-datatable', [ClaimController::class, 'ackDocsDatatable'])->name('claim.ack_docs_datatable');
    Route::post('/save-doc-acknowledgement', [ClaimController::class, 'saveDocAcknowledgement'])->name('claim.save_doc_acknowledgement');
    Route::post('/delete-doc-acknowledgement', [ClaimController::class, 'deleteDocAcknowledgement'])->name('claim.delete_doc_acknowledgement');

    Route::get('/claimStatusDatatable', [ClaimController::class, 'claimStatusDatatable'])->name('claim.claimStatusDatatable');
    Route::post('/saveClaimStatus', [ClaimController::class, 'saveClaimStatus'])->name('claim.saveClaimStatus');

    Route::prefix('notification')->name('claim.notification.')->group(function () {
        Route::get('/claims-enquiry', function () {
            return view('/claim/claims_notification_enquiry');
        })->name('enquiry');
        Route::get('/get-active-customers', [ClaimNotificationController::class, 'getCustomers'])->name('get-customers');
        Route::get('/claims-enquiry-datatable', [ClaimNotificationController::class, 'ClaimsEnquiryDatatable'])->name('enquiry.datatable');
        Route::any('/claim-form', [ClaimNotificationController::class, 'ClaimForm'])->name('form');
        Route::post('/claim-register', [ClaimNotificationController::class, 'ClaimRegister'])->name('register');
        Route::get('/claim-datatable', [ClaimNotificationController::class, 'ClaimDatatable'])->name('datatable');
        Route::get('/get-loss-endorsements', [ClaimNotificationController::class, 'GetLossEndorsements'])->name('get_loss_endorsements');
        Route::get('/get-endorsement-info', [ClaimNotificationController::class, 'GetEndorsementInfo'])->name('get_endorsement_info');
        Route::get('/claim-peril-datatable', [ClaimNotificationController::class, 'ClaimPerilDatatable'])->name('peril_datatable');
        Route::get('/claim-reinsurer-datatable', [ClaimNotificationController::class, 'ClaimReinsurerDatatable'])->name('reinsurers_datatable');
        Route::any('/notification_details', [ClaimNotificationController::class, 'ClaimDetails'])->name('claim.detail');
        Route::get('/claim-debit-datatable', [ClaimNotificationController::class, 'ClaimCedantDatatable'])->name('debit_datatable');

        Route::post('/claim-save-peril', [ClaimNotificationController::class, 'savePeril'])->name('saveperil');
        Route::post('/claim-save-reserve', [ClaimNotificationController::class, 'saveReserve'])->name('saveReserve');
        Route::post('/generate-debit', [ClaimNotificationController::class, 'generateDebit'])->name('generate-debit');
        Route::get('/debits_datatable', [ClaimNotificationController::class, 'debits_datatable'])->name('debits_datatable');

        Route::get('/attachments_datatable', [ClaimNotificationController::class, 'attachments_datatable'])->name('attachments_datatable');
        Route::post('/add_attachment', [ClaimNotificationController::class, 'saveAttachment'])->name('save_attachment');
        Route::match(['put', 'post'], '/amend_attachment', [ClaimNotificationController::class, 'amendAttachment'])->name('amend_attachment');
        Route::post('/delete_attachment', [ClaimNotificationController::class, 'deleteAttachment'])->name('delete_attachment');

        Route::get('/ack-docs-datatable', [ClaimNotificationController::class, 'ackDocsDatatable'])->name('ack_docs_datatable');
        Route::post('/save-doc-acknowledgement', [ClaimNotificationController::class, 'saveDocAcknowledgement'])->name('save_doc_acknowledgement');
        Route::post('/delete-doc-acknowledgement', [ClaimNotificationController::class, 'deleteDocAcknowledgement'])->name('delete_doc_acknowledgement');

        Route::get('/claimStatusDatatable', [ClaimNotificationController::class, 'claimStatusDatatable'])->name('claimStatusDatatable');
        Route::post('/saveClaimStatus', [ClaimNotificationController::class, 'saveClaimStatus'])->name('saveClaimStatus');
        Route::post('/preNotificationVerification', [ClaimNotificationController::class, 'preNotificationVerification'])->name('preNotificationVerification');
        Route::post('/convertNotificationToClaim', [ClaimNotificationController::class, 'convertNotificationToClaim'])->name('convertNotificationToClaim');

        Route::post('/sendDocumentEmail', [ClaimNotificationController::class, 'sendDocumentEmail'])->name('sendDocumentEmail');
        Route::delete('/delete-claim', [ClaimNotificationController::class, 'deleteClaim'])->name('delete');
        Route::delete('/bulk-delete-claims', [ClaimNotificationController::class, 'bulkDeleteClaims'])->name('bulk-delete');
        Route::patch('/cancel-claim', [ClaimNotificationController::class, 'cancelClaim'])->name('cancel');

        Route::get('/dashboard-stats', [ClaimNotificationController::class, 'getDashboardStats'])->name('dashboard-stats');
        Route::get('/card-details', [ClaimNotificationController::class, 'getCardDetails'])->name('card-details');
    });
});
