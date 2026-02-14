<?php

use App\Http\Controllers\BdController\LeadsOnboardingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CoverController;
use App\Http\Controllers\CoverRiskController;
use App\Http\Controllers\BdController\BdScheduleController;
use App\Http\Controllers\CoverTransactionController;
use App\Http\Controllers\QuarterlyDebitController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'customer', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::get('/customer-info', [CustomerController::class, 'customer_info'])->name('customer.info');
    Route::get('/{customerId}/edit', [CustomerController::class, 'customerEdit'])->name('customer.edit');
    Route::put('/{customerId}/update', [CustomerController::class, 'customerUpdate'])->name('customer.update');

    Route::get('/cedants', [CustomerController::class, 'cedant_info'])->name('cedant.info');
    Route::redirect('/cedant-info', '/customer/cedants', 301);
    Route::get('/reinsurer-info', [CustomerController::class, 'reinsurer_info'])->name('reinsurer.info');
    Route::get('/insured-info', [CustomerController::class, 'insured_info'])->name('insured.info');
    Route::get('/customer-data', [CustomerController::class, 'getCustomerData'])->name('customer.data');
    Route::get('/cedant-data', [CustomerController::class, 'getCedantData'])->name('cedant.data');
    Route::get('/reinsurer-data', [CustomerController::class, 'getReinsurerData'])->name('reinsurer.data');
    Route::get('/insured-data', [CustomerController::class, 'getInsuredData'])->name('insured.data');
    Route::get('/customer-new', [CustomerController::class, 'CustomerAddForm'])->name('customer.form');
    Route::post('/customer-store', [CustomerController::class, 'storeCustomer'])->name('customer.store');
    Route::any('/details', [CustomerController::class, 'CustomerDtl'])->name('customer.dtl');
    Route::any('/customer-dtl', [CustomerController::class, 'CustomerDtl']);
    Route::get('/statement-datatable', [CustomerController::class, 'StatementDatatable'])->name('statement.datatable');

    //enquiry
    Route::get('/treaty-proportional-enquiry', [CustomerController::class, 'TreatyPropEnquiry'])
        ->name('trtpropenquiry.info');
    Route::redirect('/trtpropenquiry-info', '/customer/treaty-proportional-enquiry', 301);
    Route::get('/treaty-non-proportional-enquiry', [CustomerController::class, 'TreatyNonPropEnquiry'])
        ->name('trtnonpropenquiry.info');
    Route::redirect('/trtnonpropenquiry-info', '/customer/treaty-non-proportional-enquiry', 301);
    Route::get('/facultative-proportional-enquiry', [CustomerController::class, 'TreatyFACPropEnquiry'])
        ->name('trtfacpropenquiry.info');
    Route::redirect('/trtfacpropenquiry-info', '/customer/facultative-proportional-enquiry', 301);
    Route::get('/facultative-non-proportional-enquiry', [CustomerController::class, 'TreatyFACNonPropEnquiry'])
        ->name('trtfacnonpropenquiry.info');
    Route::redirect('/trtfacnonpropenquiry-info', '/customer/treaty-facultative-non-proportional-enquiry', 301);
    Route::get('/treatyenquiry-data', [CustomerController::class, 'TypeOfBusCoverDatatable'])->name('treatyenquiry.data');

    //Leads Onboard Enquiry
    Route::get('/trtpropenquirypipe-info', [LeadsOnboardingController::class, 'TreatyPropEnquiry'])->name('trtpropenquiryLbc.info');
    Route::get('/trtnonpropenquirypipe-info', [LeadsOnboardingController::class, 'TreatyNonPropEnquiry'])->name('trtnonpropenquiryLbc.info');
    Route::get('/trtfacpropenquirypipe-info', [LeadsOnboardingController::class, 'TreatyFACPropEnquiry'])->name('trtfacpropenquiryLbc.info');
    Route::get('/trtfacnonpropenquirypipe-info', [LeadsOnboardingController::class, 'TreatyFACNonPropEnquiry'])->name('trtfacnonpropenquiryLbc.info');
    Route::get('/treatyenquirypipe-data', [LeadsOnboardingController::class, 'TypeOfBusCoverDatatable'])->name('treatyenquiryLbc.data');

    Route::post('/cover/clear_cedant_data', [CustomerController::class, 'clearCedantData'])->name('customer.clear_cedant_data');
});

Route::group(['prefix' => 'cover', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::get('/get-active-customers', [CoverController::class, 'getCustomers'])->name('cover.get-customers');
    Route::any('/cover-form', [CoverController::class, 'CoverForm'])->name('cover.form');
    // Route::any('/editCoverRegister', [CoverController::class, 'editCoverRegister'])->name('cover.editCoverRegister');
    Route::any('/editCoverForm', [CoverController::class, 'CoverForm'])->name('cover.editCoverForm');
    Route::any('/cover-register', [CoverController::class, 'CoverRegister'])->name('cover.register');
    Route::get('/cover-datatable', [CoverController::class, 'CoverDatatable'])->name('cover.datatable');
    Route::get('/endorse-datatable', [CoverController::class, 'EndorseDatatable'])->name('endorse.datatable');
    Route::any('/cover-home', [CoverController::class, 'coverHome'])->name('cover.CoverHome');
    Route::get('/get-class', [CoverController::class, 'GetSpecificClasses'])->name('get_class');
    Route::get('/get-binders', [CoverController::class, 'GetBinderCovers'])->name('get_binder_covers');
    Route::get('/get_todays_rate', [CoverController::class, 'get_todays_rate'])->name('get_todays_rate');
    Route::get('/yesterdayRate', [CoverController::class, 'yesterdayRate'])->name('yesterdayRate');
    Route::any('/endorsements', [CoverController::class, 'endorse_functions'])->name('endorsements_list');
    Route::any('/endorsements_list', [CoverController::class, 'endorse_functions']);
    Route::post('/save-reinsurer-data', [CoverController::class, 'saveReinsurerData'])->name('cover.save_reinsurance_data');
    Route::post('/edit-reinsurer-data', [CoverController::class, 'editReinsurerData'])->name('cover.edit_reinsurance_data');
    Route::post('/delete-reinsurer-data', [CoverController::class, 'deleteReinsurerData'])->name('cover.delete_reinsurance_data');
    Route::get('/schedules_datatable', [CoverController::class, 'schedules_datatable'])->name('cover.schedules_datatable');
    Route::get('/installments_datatable', [CoverController::class, 'installments_datatable'])->name('cover.installments_datatable');
    Route::get('/policy_renewal_datatable', [CoverController::class, 'policy_renewal_datatable'])->name('cover.policy_renewal_datatable');
    Route::get(
        '/attachments_datatable',
        [CoverController::class, 'attachments_datatable']
    )->name('cover.attachments_datatable');
    Route::get('/reinsurers_datatable', [CoverController::class, 'reinsurers_datatable'])->name('cover.reinsurers_datatable');
    Route::get('/clauses_datatable', [CoverController::class, 'clauses_datatable'])->name('cover.clauses_datatable');
    Route::get('/debits_datatable', [CoverController::class, 'debits_datatable'])->name('cover.debits_datatable');
    Route::get('/approvals_datatable', [CoverController::class, 'approvals_datatable'])->name('cover.approvals_datatable');
    Route::post('/add_schedule', [CoverRiskController::class, 'add_schedule'])->name('cover.add_schedule');
    Route::put('/amend_schedule', [CoverRiskController::class, 'amend_schedule'])->name('cover.amend_schedule');
    Route::post('/delete_schedule', [CoverRiskController::class, 'delete_schedule'])->name('cover.delete_schedule');
    Route::post('/add_attachment', [CoverController::class, 'saveAttachment'])->name('cover.save_attachment');
    Route::match(['put', 'post'], '/amend_attachment', [CoverController::class, 'amendAttachment'])->name('cover.amend_attachment');
    Route::post('/delete_attachment', [CoverController::class, 'deleteAttachment'])->name('cover.delete_attachment');
    Route::post('/add_clauses', [CoverController::class, 'saveClauses'])->name('cover.save_clauses');
    Route::match(['put', 'post'], '/amend_clauses', [CoverController::class, 'amendClauses'])->name('cover.amend_clauses');
    Route::post('/delete_clauses', [CoverController::class, 'deleteClause'])->name('cover.delete_clause');
    Route::post('/generate-debit', [CoverController::class, 'generateDebitAndCredit'])->name('cover.generate-debit');
    Route::post('/save_insurance_class', [CoverController::class, 'saveInsuranceClasses'])->name('cover.save_insurance_class');
    Route::get('/classes_datatable', [CoverController::class, 'classes_datatable'])->name('cover.classes_datatable');
    Route::get('/get_reinprem_type', [CoverController::class, 'getReinpremType'])->name('cover.get_reinprem_type');
    Route::get('/get_treatyperbustype', [CoverController::class, 'getTreatyPerBusType'])->name('cover.get_treatyperbustype');
    Route::post('/commit-cover', [CoverController::class, 'commitCover'])->name('cover.commit_cover');
    //leads onboard
    Route::any('/cover-form1', [LeadsOnboardingController::class, 'CoverForm'])->name('cover.formLbc');

    Route::post('/save_quaterly_figures', [CoverController::class, 'StoreQuaterlyFigures'])->name('cover.save_quaterly_figures');
    Route::get('/get_quaterly_figures', [CoverController::class, 'getQuaterlyFigures'])->name('cover.get_quarterly_figures');
    Route::get('/get_quarterly_by_quarter', [CoverController::class, 'getQuarterlyFiguresByQuarter'])->name('cover.get_quarterly_by_quarter');

    Route::post('/save_profit_commission', [CoverController::class, 'StoreProfitCommission'])->name('cover.save_profit_commission');

    Route::post('/save_mdp_installments', [CoverController::class, 'saveMdpInstallments'])->name('cover.save_mdp_installments');
    Route::post('/delete_mdp_installments', [CoverController::class, 'deleteMdpInstallments'])->name('cover.delete_mdp_installments');
    Route::post('/mdp_installment_endorsement', [CoverController::class, 'mdpInstallmentEndorsement'])->name('cover.mdp_installment_endorsement');

    Route::post('/save_fac_installments', [CoverController::class, 'saveFacInstallments'])->name('cover.save_fac_installments');
    // Route::post('/delete_fac_installments', [CoverController::class, 'deleteFacInstallments'])->name('cover.delete_fac_installments');

    Route::post('/process_cover_endorsement', [CoverController::class, 'processCoverEndorsement'])->name('cover.process_cover_endorsement');

    Route::post('/save_portfolio', [CoverController::class, 'StorePropPortfolio'])->name('cover.save_portfolio');
    Route::get('/get_treaty_year_cover', [CoverController::class, 'getTreatyCover'])->name('cover.get_treaty_year_cover');
    Route::get('/get_reinsurers_orig_endorsement', [CoverController::class, 'getReinsurersOrigEndorsement'])->name('cover.get_reinsurers_orig_endorsement');

    Route::post('/pre_cover_verification', [CoverController::class, 'preCoverVerification'])->name('cover.pre_cover_verification');
    Route::any('/renewal_notice', [CoverController::class, 'policyRenewal'])->name('cover.renewal_notice');
    Route::any('/cover/renewal_notice/generate', [CoverController::class, 'generatePolicyRenewal'])->name('cover.renewal_notice.generate');
    Route::post('/cover/sendrenewal/email', [CoverController::class, 'sendPolicyRenewal'])->name('cover.sendrenewal.email');
    Route::post('/cover/delete_renewal_notice', [CoverController::class, 'deleteRenewalNotice'])->name('cover.delete_renewal_notice');
    Route::post('/cover/delete_cover', [CoverController::class, 'deletePolicyCover'])->name('cover.delete_cover');

    Route::get('/endorse_narration_datatable', [CoverController::class, 'endorseNarrationDatatable'])->name('cover.endorse_narration_datatable');
    Route::post('/cover/sendreinsurer/email', [CoverController::class, 'sendReinsurerEmail'])->name('cover.sendreinsurer.email');

    //Bd Crud
    Route::get('/bd-schedule-info', [BdScheduleController::class, 'bd_schedule_info'])->name('bd.schedule.info');
    Route::get('/schedule-header-form', [BdScheduleController::class, 'bd_schedule_add_form'])->name('schedule.header.form');
    Route::post('/bd-schedule-header-store', [BdScheduleController::class, 'bd_schedule_header_add'])->name('bd.schedule.header.store');
    Route::get('bd-schedule-header-data', [BdScheduleController::class, 'bd_schedule_header_data'])->name('bd.schedule.header.data');
    Route::post('/delete-schedule-header', [BdScheduleController::class, 'delete_schedule_header'])->name('delete.schedule.header');

    Route::get('/bd-Lead-status-info', [BdScheduleController::class, 'bd_lead_status_info'])->name('lead.status.info');
    Route::get('/bd-lead-status-form', [BdScheduleController::class, 'bd_lead_status_add_form'])->name('lead.status.form');
    Route::post('/bd-lead-status-store', [BdScheduleController::class, 'bd_lead_status_add'])->name('lead.status.store');
    Route::get('bd-lead-status-data', [BdScheduleController::class, 'bd_lead_status_data'])->name('lead.status.data');
    Route::post('/bd-delete-lead-status', [BdScheduleController::class, 'delete_lead_status'])->name('delete.lead.status');


    Route::get('/bd-stage-doc-info', [BdScheduleController::class, 'bd_stage_doc_info'])->name('stage.doc.info');
    Route::get('/bd-stage-doc-form', [BdScheduleController::class, 'stage_doc_form'])->name('stage.doc.form');
    Route::post('/bd-stage-doc-store', [BdScheduleController::class, 'bd_stage_doc_add'])->name('stage.doc.store');
    Route::get('/bd-stage-doc-data', [BdScheduleController::class, 'bd_stage_doc_data'])->name('stage.doc.data');
    Route::post('/bd-delete-stage-doc', [BdScheduleController::class, 'delete_stage_doc'])->name('delete.stage.doc');

    Route::get('/bd-doc-type-info', [BdScheduleController::class, 'bd_doc_type_info'])->name('doc.type.info');
    Route::get('/bd-doc-type-form', [BdScheduleController::class, 'doc_type_form'])->name('doc.type.form');
    Route::post('/bd-doc-type-store', [BdScheduleController::class, 'bd_doc_type_add'])->name('doc.type.store');
    Route::get('/bd-doc-type-data', [BdScheduleController::class, 'bd_doc_type_data'])->name('doc.type.data');
    Route::post('/bd-doc-type', [BdScheduleController::class, 'delete_doc_type'])->name('delete.doc.type');

    Route::get('/bd-treaty-operation-checklist-info', [BdScheduleController::class, 'operationchecklist_info'])->name('operationchecklist.info');
    Route::get('/treaty-operation-checklist-form', [BdScheduleController::class, 'operationchecklist_form'])->name('operationchecklist.form');
    Route::post('/treaty-operation-checklist-store', [BdScheduleController::class, 'operationchecklist_add'])->name('operationchecklist.store');
    Route::get('/treaty-operation-checklist-data', [BdScheduleController::class, 'operationchecklist_data'])->name('operationchecklist.data');
    Route::post('/treaty-operation-checklist', [BdScheduleController::class, 'delete_operationchecklist'])->name('delete.operationchecklist');

    Route::get('prospect-data/{prospectId}', [CoverController::class, 'getProspectData'])->name('pipeline.get_prospect_data');

    Route::prefix('treaty/{coverNo}')->name('cover.')->group(function () {
        Route::get('transactions', [CoverTransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{refNo}/quarterly-figures', [CoverTransactionController::class, 'quarterlyFigures'])->name('transactions.quarterly-figures');
        Route::get('transactions/{refNo}/profit-commission', [CoverTransactionController::class, 'profitCommission'])->name('transactions.profit-commission');
    });

    Route::prefix('treaty')->name('treaty.')->group(function () {

        Route::post('quarterly-figures', [CoverTransactionController::class, 'storeQuarterlyFigures'])
            ->name('quarterly-figures.store');
        Route::post('profit-commission', [CoverTransactionController::class, 'storeProfitCommission'])
            ->name('profit-commission.store');
        Route::put('commission/adjust', [CoverTransactionController::class, 'adjustCommission'])
            ->name('commission.adjust');


        Route::get('/quarterly-debit/{cover}', [QuarterlyDebitController::class, 'index'])
            ->name('quarterly-debit.index');

        Route::prefix('debit-items')->name('debit-items.')->group(function () {
            Route::get('/', [QuarterlyDebitController::class, 'getDebitItems'])
                ->name('index');
            Route::post('/', [QuarterlyDebitController::class, 'storeDebitItem'])
                ->name('store');

            Route::get('/{id}', [QuarterlyDebitController::class, 'showDebitItem'])
                ->name('show');
            Route::put('/{id}', [QuarterlyDebitController::class, 'updateDebitItem'])
                ->name('update');
            Route::delete('/{id}', [QuarterlyDebitController::class, 'destroyDebitItem'])
                ->name('destroy');
        });

        Route::prefix('credit-items')->name('credit-items.')->group(function () {
            Route::get('/', [QuarterlyDebitController::class, 'getCreditItems'])
                ->name('index');
        });

        Route::prefix('reinsurers')->name('reinsurers.')->group(function () {
            Route::get('/', [QuarterlyDebitController::class, 'getReinsurers'])
                ->name('index');

            Route::get('/list', [QuarterlyDebitController::class, 'listReinsurers'])
                ->name('list');

            Route::get('/credit-note/view', [QuarterlyDebitController::class, 'viewReinsurerCreditNote'])
                ->name('credit-note.view');
        });

        Route::get('/cedant/{cover}', [QuarterlyDebitController::class, 'getCedantDetailsApi'])
            ->name('cedant.show');

        Route::get('/cedant/debit-note/view', [QuarterlyDebitController::class, 'viewCedantDebitNote'])
            ->name('cedant.debit-note.view');

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [QuarterlyDebitController::class, 'getDocuments'])
                ->name('index');

            Route::post('/generate', [QuarterlyDebitController::class, 'generateDocument'])
                ->name('generate');

            Route::get('/{id}/download', [QuarterlyDebitController::class, 'downloadDocument'])
                ->name('download');
        });

        Route::get('/slip/{cover}/preview', [QuarterlyDebitController::class, 'previewSlip'])
            ->name('slip.preview');
        Route::post('/statement/{cover}/generate', [QuarterlyDebitController::class, 'generateStatement'])
            ->name('statement.generate');

        Route::get('/export/{cover}', [QuarterlyDebitController::class, 'exportData'])
            ->name('export');

        // Real-time summary statistics endpoint
        Route::get('/summary-stats', [QuarterlyDebitController::class, 'getSummaryStats'])
            ->name('summary-stats');
    });

    Route::prefix('cedants')->name('cedant.')->group(function () {
        // Route::get('/data', [CustomerController::class, 'getData'])->name('data');
        Route::get('/statistics', [CustomerController::class, 'getStatistics'])->name('statistics');
    });

    Route::get('/{cover}/edit', [CoverController::class, 'editCoverRegister'])
        ->name('cover.edit');


    Route::get('cover/reinsurers/fetch', [CoverController::class, 'fetchReinsurers'])
        ->name('cover.reinsurers.fetch');

    Route::get('/claims-datatable', [CoverController::class, 'claims_datatable'])->name('cover.claims_datatable');
    Route::get('/statements-datatable', [CoverController::class, 'statements_datatable'])->name('cover.statements_datatable');
});
