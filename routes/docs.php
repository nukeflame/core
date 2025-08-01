<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BdController\BdScheduleController;
use App\Http\Controllers\BdController\PipelineController;
use App\Http\Controllers\BdController\TenderController;
use Illuminate\Support\Facades\Route;

Route::prefix('docs')->middleware(['auth', 'check.first.login'])->name('docs.')->group(function () {
    Route::get('coverslip', [PrintoutController::class, 'coverSlip'])->name('coverslip');
    Route::get('endorsementslip', [PrintoutController::class, 'endorsementNoticeSlip'])->name('endorsementslip');
    Route::post('quotationcoverslip', [PrintoutController::class, 'QuotationCoverSlip'])->name('quotationCoverSlip');
    Route::post('TreatyBdPrintout', [PrintoutController::class, 'TreatyBdPrintout'])->name('treatyBdPrintout');

    // Route::post('store', 'PrintoutController@store');
    Route::get('coverdebitnote', [PrintoutController::class, 'coverDebitnote'])->name('coverdebitnote');
    Route::get('reincreditnotes', [PrintoutController::class, 'reinCreditNotes'])->name('reincreditnotes');

    Route::get('claimntf-docs-ack-letter', [PrintoutController::class, 'claimNtfDocsAckLetter'])->name('claimntf-docs-ack-letter');
    Route::get('claimntf-docs-notc-letter', [PrintoutController::class, 'claimNtfDocsNotcLetter'])->name('claimntf-docs-notc-letter');
    Route::get('claim-docs-ack-letter', [PrintoutController::class, 'claimDocsAckLetter'])->name('claim-docs-ack-letter');

    Route::get('claimcreditnote', [PrintoutController::class, 'claimCreditnote'])->name('claimcreditnote');
    Route::get('claimdebitnote', [PrintoutController::class, 'reinClmDebitNote'])->name('fac_clm_reindebit_note');

    Route::post('print_receipt', [PrintoutController::class, 'printReceipt'])->name('print_receipt');
    Route::get('print_coa', [PrintoutController::class, 'printCOA'])->name('print_coa');

    Route::post('/payrequestprint', [PrintoutController::class, 'payRequestPrint'])->name('print_payreq');
    Route::post('/payvoucherprint', [PrintoutController::class, 'payVoucherPrint'])->name('print_payvoucher');

    Route::post('/pre_cover_slip_verification', [PrintoutController::class, 'preCoverSlipVerification'])->name(
        'pre_cover_slip_verification'
    );
    Route::get('/view/renewal_notice', [PrintoutController::class, 'viewRenewalNotice'])->name('view.renewal_notice');
    Route::get('/download/renewal_notice', [PrintoutController::class, 'downloadRenewalNotice'])->name('download.renewal_notice');
});

Route::prefix('docs-setup')->middleware(['auth', 'check.first.login'])->name('docs-setup.')->group(function () {
    Route::get('slip-template', [PrintoutSetupController::class, 'slipTemplate'])->name('slip-template');
    Route::get('coverslip_datatable', [PrintoutSetupController::class, 'coverslip_datatable'])->name('coverslip_datatable');
    Route::get('save-slip-template', [PrintoutSetupController::class, 'saveSlipTemplate'])->name('sliptemplate_form');
    Route::get('slip', [PrintoutSetupController::class, 'slip'])->name('slip');
    Route::post('save-slip', [PrintoutSetupController::class, 'saveSlip'])->name('save-slip');
    Route::post('save-clause', [PrintoutSetupController::class, 'saveCluse'])->name('save-clause');
    Route::post('edit-clause', [PrintoutSetupController::class, 'editCluse'])->name('edit-clause');
    Route::post('/delete_clause', [PrintoutSetupController::class, 'deleteClause'])->name('delete-clause');


    Route::get('/schedule_details/openword', [PrintoutSetupController::class, 'schedulesOpenWord'])->name('schedule_details.openword');

    //bd template
    Route::get('bd-schedule-slip-template', [BdScheduleController::class, 'bd_schedule_slip_template'])->name('bd-schedule-slip-template');
    Route::get('bd-schedule-template', [BdScheduleController::class, 'bd_schedule_template_datatable'])->name('bd_schedule_datatable');
    Route::get('schedule-template-form', [BdScheduleController::class, 'save_schedule_template'])->name('bd_schedule_template_form');
    Route::post('save-schedule-template', [BdScheduleController::class, 'save_bd_schedule_template'])->name('save_bd_schedule_template_form');
    Route::post('edit-bd-schedule', [BdScheduleController::class, 'edit_bd_schedule'])->name('edit_bd_schedule_template_form');
    Route::post('/delete-schedule-template', [BdScheduleController::class, 'delete_schedule_template'])->name('delete-schedule-template');


    // Route::get('slip', [PrintoutSetupController::class, 'slip'])->name('slip');
    Route::get('/tenders/docsparam', [TenderController::class,'listTenderDocsParam'])->name('tender.docsparam');
    Route::post('/tenders/doc_add', [TenderController::class,'AddTenderDocParam'])->name('tender.doc_add');
    Route::get('/tender-document-details/{docId}', [TenderController::class,'viewDocumentDetails'])->name('tender.document.details');
    Route::any('save/documents',[PipelineController::class,'saveTenderDocs'])->name('saveTenderDocs');
    
});
