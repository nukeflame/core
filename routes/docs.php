<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::prefix('docs')->middleware(['auth', 'check.first.login'])->name('docs.')->group(function () {
    Route::get('coverslip', [PrintoutController::class, 'coverSlip'])->name('coverslip');
    Route::get('endorsementslip', [PrintoutController::class, 'endorsementNoticeSlip'])->name('endorsementslip');
    Route::post('bd-coverslip', [PrintoutController::class, 'bdCoverSlip'])->name('bdCoverSlip');
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
