<?php

use App\Http\Controllers\BdController\BdScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BdController\LeadsOnboardingController;
use App\Http\Controllers\BdController\PipelineController;
use App\Http\Controllers\BdController\TenderController;
use App\Http\Controllers\BdHandoverController;
use App\Http\Controllers\OutlookOAuthController;
use App\Http\Controllers\PrintoutController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use PHPJasper\PHPJasper;

Route::middleware(['auth', 'check.first.login'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::redirect('/dashboard', '/', 301);

    Route::get('/search/results', [SearchController::class, 'searchResults'])->name('search.results');

    //cover routes
    require_once('cover_routes.php');

    //approvals routes
    require_once('approvals.php');

    //claim routes
    require_once('claim_routes.php');

    //settings routes
    require_once('settings/settings_routes.php');


    //quote routes group
    require_once('quote_routes.php');

    // docs routes group
    require_once('docs.php');

    // docs notifications group
    require_once('notifications.php');

    // securtiy routes
    require_once('security.php');

    // dashboard routes
    require_once('dashboard.php');

    // admin routes
    require_once('admin.php');

    // mail routes
    require_once('mail.php');

    // report routes
    require_once('report.php');

    Route::get('/generate-report', function () {
        try {
            $jasperFile = storage_path('jasperreports/coa_listing.jasper');
            $outputDir = storage_path('reports/output');

            $jasper = new PHPJasper();
            $jasper->process(
                $jasperFile,
                $outputDir,
                ['format' => ['pdf']],
                ['parameter1' => 'value']
            )->execute();
            $generatedFile = $outputDir . '/your_report.pdf';
            $pdfContent = file_get_contents($generatedFile);

            unlink($generatedFile);

            return view('reports.preview', ['pdfContent' => $pdfContent]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
    Route::redirect('pipeline/leads_listing', 'leads_listing', 301);
    Route::redirect('intermediary/leads_listing', 'leads_listing', 301);
    Route::get('leads_get', [LeadsOnboardingController::class, 'leads_get'])->name('leads.get');
    Route::get('prequalifications_get', [LeadsOnboardingController::class, 'prequalifications_get'])->name('prequalifications.get');

    Route::get('/tenders/list', [TenderController::class, 'listTenders'])->name('tender.list');
    Route::get('/tenders/docsparam', [TenderController::class, 'listTenderDocsParam'])->name('tender.docsparam');
    Route::post('/tenders/add', [TenderController::class, 'AddTender'])->name('tender.add');
    Route::post('/tenders/edit', [TenderController::class, 'editTender'])->name('editTender');
    Route::post('/tenders/doc_add', [TenderController::class, 'AddTenderDocParam'])->name('tender.doc_add');
    Route::post('/tenders/addToc', [TenderController::class, 'AddTenderToc'])->name('tender.Tocadd');
    Route::post('/tenders/addTocItem', [TenderController::class, 'AddTenderTocItem'])->name('tender.TocItemadd');
    Route::post('/tenders/details', [TenderController::class, 'TenderDetails'])->name('tender.tenderdetails');
    Route::get('/tenders/getTenderSectcheckeditems', [TenderController::class, 'getTenderSectcheckeditems'])->name('tenders.getcheckitems');
    Route::post('/TenderPrint', [TenderController::class, 'TenderPrint'])->name('tender-printout');
    Route::get('/subcat/doc', [TenderController::class, 'getSubcatDoc'])->name('get_subcat_doc');
    Route::post('/doc/preview', [TenderController::class, 'previewDoc'])->name('doc_preview');

    Route::get('pipelines/facultative/view', [PipelineController::class, 'pipeline_view'])->name('pipeline.view');
    Route::get('pipelines/treaty/view', [PipelineController::class, 'treaty_pipeline_view'])->name('treaty.pipeline.view');
    Route::redirect('pipelines/view', 'pipelines/facultative/view', 301);

    Route::redirect('pipelines_view', 'pipelines/facultative/view', 301);
    Route::redirect('treaty_pipelines_view', 'pipelines/treaty/view', 301);
    Route::get('pipelines_onboarding', [PipelineController::class, 'index'])->name('pipelines.onboarding');
    Route::post('pipelines_save', [PipelineController::class, 'save'])->name('pipelines.save');
    Route::get('pipelines', [PipelineController::class, 'listing'])->name('pipelines.listing');
    Route::get('pipelines_get', [PipelineController::class, 'pipelines_get'])->name('pipelines.get');
    Route::get('pipelines_edit', [PipelineController::class, 'pipelines_edit'])->name('pipelines.edit');
    Route::post('pipelines/{pipeline}/edit', [PipelineController::class, 'edit_pipeline'])->name('edit_pipeline');
    Route::get('get_pipelines', [PipelineController::class, 'get_pipelines'])->name('get_pipelines');
    Route::get('pipelines/{pipeline}', [PipelineController::class, 'getPipelineDetails'])->whereNumber('pipeline')->name('getpipelineDetails');
    Route::post('pipelines_create_opportunity', [PipelineController::class, 'pipeline_create_opportunity'])->name('pipeline.create.opportunity');
    Route::get('pipelines_activity_treaty', [PipelineController::class, 'pipeline_activity_treaty'])->name('pipeline.activity.treaty');

    Route::post('update_category_type', [PipelineController::class, 'update_category'])->name('update.category_type');
    Route::post('bd/notification/send', [PipelineController::class, 'sendBDNotification'])->name('bd.notification.send');
    Route::get('get/bd_terms', [PipelineController::class, 'getBdTerms'])->name('get.bd_terms');
    Route::post('bd/contacts/update', [PipelineController::class, 'updateReinContacts'])->name('rein.contacts.update');
    Route::get('bd/selected_reinsurers', [PipelineController::class, 'getSelectedBdRein'])->name('get.selected_bd_reinsurers');
    Route::post('bd/bd_email_data', [PipelineController::class, 'getBdEmailData'])->name('fetch.bd_email_data');

    Route::get('get-pipeline-data', [PipelineController::class, 'getPipelineData'])->name('pipeline.sales.get_pipeline_data');
    Route::get('get-pipeline-chart-data', [PipelineController::class, 'getPipelineChartData'])->name('pipeline.sales.get_pipeline_chart_data');
    Route::get('reinsurers/search', [PipelineController::class, 'getPipelineReinsurers'])->name('pipeline.search_reinsurers');
    Route::post('/reinsurers/{reinsurerID}/contacts', [PipelineController::class, 'getPipelineReinContacts'])->name('pipeline.getPipelineReinContacts');
    Route::post('/schedule-headers', [PipelineController::class, 'getHeaders'])
        ->name('schedule.headers.get');
    Route::post('/cedant/{cedantID}/contacts', [PipelineController::class, 'getPipelineCedContacts'])->name('pipeline.getPipelineCedContacts');

    Route::get('pipelines_activityq1_treaty', [PipelineController::class, 'pipeline_activity_q1_treaty'])->name('pipeline.activity.q1.treaty');
    Route::get('pipelines_activityq2_treaty', [PipelineController::class, 'pipeline_activity_q2_treaty'])->name('pipeline.activity.q2.treaty');
    Route::get('pipelines_activityq3_treaty', [PipelineController::class, 'pipeline_activity_q3_treaty'])->name('pipeline.activity.q3.treaty');
    Route::get('pipelines_activityq4_treaty', [PipelineController::class, 'pipeline_activity_q4_treaty'])->name('pipeline.activity.q4.treaty');
    Route::post('prospect-revert', [PipelineController::class, 'revertProspect'])->name('prospect.revert');

    Route::get('Report_data', [PipelineController::class, 'Report_data'])->name('report.data');
    Route::get('decline_Report_data', [PipelineController::class, 'decline_report_data'])->name('decline.report.data');
    Route::post('sales_report_filter', [PipelineController::class, 'sales_report_filter'])->name('sales_report_filter');
    Route::get('sales_report_data', [PipelineController::class, 'SalesReportData'])->name('sales.report.data');

    Route::get('get_prospect_details', [PipelineController::class, 'get_prospect_details'])->name('get_prospect_details');
    Route::get('search-prospect-fullnames', [PipelineController::class, 'search_prospect_fullnames'])->name('search-prospect-fullnames');
    Route::get('search-insured-names', [PipelineController::class, 'search_insured_names'])->name('search-insured-names');
    Route::get('search-lead-names', [PipelineController::class, 'search_lead_names'])->name('search-lead-names');
    Route::post('reinsurer/decline', [PipelineController::class, 'declineReinsurer'])->name('reinsurer.decline');
    Route::post('proposal/reset-to-lead', [PipelineController::class, 'resetProposalToLead'])->name('proposal.reset_to_lead');
    Route::post('reinsurer/update-share', [PipelineController::class, 'updateReinsurerShare'])->name('reinsurer.update_share');
    Route::get('customer/contact-info', [PipelineController::class, 'getCustomerContactInfo'])->name('customer.contact_info');

    Route::post('stage/documents', [PipelineController::class, 'stageDocuments'])->name('schedule.get_stage_documents');
    Route::get('cfac-offer', [PipelineController::class, 'facultativeOffer'])->name('get-fac-data');
    Route::post('edit-data', [PipelineController::class, 'editData'])->name('get-edit-data');
    Route::get('/opportunities/{opportunityId}/pdfs', [PrintoutController::class, 'getOppPdfData'])->name('get_opp_data');

    // Route::get('search-prospect-fullnames', [PipelineController::class, 'search_prospect_fullnames'])->name('search-prospect-fullnames');
    Route::post('check_user_exists', [PipelineController::class, 'confirmUserExists'])->name('check_user_exists');
    Route::get('prospects_won', [PipelineController::class, 'prospects_won'])->name('prospects.won');
    Route::get('get_prospect_details', [PipelineController::class, 'get_prospect_details'])->name('get_prospect_details');
    Route::post('prospect/add/pipeline', [PipelineController::class, 'prospectAddToPipeline'])->name('prospect.add.pipeline');
    Route::post('/prospectVerify', [PipelineController::class, 'prospectsVerify'])->name('prospectVerify');
    Route::post('update_opp_status', [PipelineController::class, 'updateLeadStatus'])->name('update.opp.status');
    Route::get('division/classes', [PipelineController::class, 'divisionClasses'])->name('get_division_classes');
    // Route::get('search-prospect-fullnames', [PipelineController::class, 'search_prospect_fullnames'])->name('search-prospect-fullnames');
    Route::post('lead/handover/save', [PipelineController::class, 'handoverSave'])->name('client.stage');
    Route::get('/prospect-documents', [PipelineController::class, 'handoverStageDocs'])
        ->name('prospect.documents');

    // Leads Routes
    Route::get('leads_get', [LeadsOnboardingController::class, 'leads_get'])->name('leads.get');
    Route::get('prequalifications_get', [LeadsOnboardingController::class, 'prequalifications_get'])->name('prequalifications.get');
    Route::get('leads_edit', [LeadsOnboardingController::class, 'leads_edit'])->name('leads.edit');
    Route::post('leads/{lead}/edit', [LeadsOnboardingController::class, 'edit_lead'])->name('edit_lead');
    Route::get('get_leads', [LeadsOnboardingController::class, 'get_leads'])->name('get_leads');
    Route::get('leads/{lead}', [LeadsOnboardingController::class, 'getLeadDetails'])->name('getLeadDetails');
    Route::get('leads_view', [LeadsOnboardingController::class, 'lead_view'])->name('lead.view');
    Route::post('leads_create_activity', [LeadsOnboardingController::class, 'lead_create_activity'])->name('lead.create.activity');
    Route::get('leads_activity', [LeadsOnboardingController::class, 'lead_activity'])->name('lead.activity');
    Route::post('update_lead_status', [LeadsOnboardingController::class, 'updateLeadStatus'])->name('update.lead.status');
    Route::get('lead/handover', [PipelineController::class, 'handoverToCR'])->name('lead.handover');

    // Leads Routes
    Route::get('leads_onboarding', [LeadsOnboardingController::class, 'index'])->name('leads.onboarding');
    Route::get('leads_PQ_Process', [LeadsOnboardingController::class, 'leads_PQ_Process'])->name('leads_PQ_Process');
    Route::post('PQ_proposal_documents', [LeadsOnboardingController::class, 'PQ_proposal_documents'])->name('PQ_proposal_documents');
    Route::post('leads_save', [LeadsOnboardingController::class, 'save'])->name('leads.save');
    Route::get('/leads_listing', [LeadsOnboardingController::class, 'listing'])->name('leads.listing');
    Route::post('/leads_listing/import-pipeline-opportunities', [LeadsOnboardingController::class, 'importPipelineOpportunities'])
        ->name('leads.import.pipeline_opportunities');
    Route::get('/leads_listing/import-pipeline-opportunities/sample', [LeadsOnboardingController::class, 'downloadPipelineOpportunitySample'])
        ->name('leads.import.pipeline_opportunities.sample');

    //Prospect Repository
    Route::post('ProspectRepository', [LeadsOnboardingController::class, 'save'])->name('ProspectRepository');

    //customer route
    Route::get('customer-data', [LeadsOnboardingController::class, 'customer_data'])->name('get-customer-data');
    // Route::post('schedules', [PipelineController::class, 'schedules'])->name('get-schedule-header');
    Route::get('/returnImportView', [PipelineController::class, 'returnImportExcelView']);
    Route::post('/import', [PipelineController::class, 'importExcel'])->name('import');
    Route::get('/cedant', [PipelineController::class, 'cedantDetails'])->name('cedant_details');
    Route::get('/quote_schedules', [PipelineController::class, 'get_quote_schedules'])->name('get_quote_schedules');
    Route::get('/get_schedules_data', [PipelineController::class, 'get_schedules_data'])->name('get.schedules.data');
    Route::get('/get_bd_schedules_data', [BdScheduleController::class, 'bd_schedule_data'])->name('bd.schedule.data');

    Route::get('/bd_schedule_create', [BdScheduleController::class, 'bd_schedule_data_create'])->name('bd.schedule.data.create');

    //treaty
    Route::get('treaty_leads_listing', [LeadsOnboardingController::class, 'treaty_listing'])->name('treaty.leads.listing');
    Route::get('treaty_leads_onboarding', [LeadsOnboardingController::class, 'treaty_index'])->name('treaty.leads.onboarding');
    Route::get('treaty_leads_get', [LeadsOnboardingController::class, 'treaty_leads_get'])->name('treaty.leads.get');
    Route::get('treaty_leads_kpis', [LeadsOnboardingController::class, 'getTreatyKPIsApi'])->name('treaty.leads.kpis');
    Route::post('treaty_pipelines_create_opportunity', [PipelineController::class, 'treaty_pipeline_create_opportunity'])->name('treaty.pipeline.create.opportunity');
    Route::get('reinsurers_declined', [PipelineController::class, 'reinsurers_declined'])->name('reinsurer.declined');
    Route::get('decline_report', [PipelineController::class, 'decline_report'])->name('decline.report');
    Route::get('bus_type', [PipelineController::class, 'bus_type'])->name('bus_type');

    // handover filter reinsurers
    Route::get('/filter-reinsurers', [PipelineController::class, 'filterReinsurers'])->name('reinsurers.filter');
    //tender routes
    Route::get('/tenders/list', [TenderController::class, 'listTenders'])->name('tender.list');
    Route::post('/tenders/details', [TenderController::class, 'TenderDetails'])->name('tender.tenderdetails');
    Route::post('/save_tendor_color', [TenderController::class, 'saveTendorColors'])->name('save_tendor_color');
    Route::get('/tenders/list', [TenderController::class, 'listTenders'])->name('tender.list');
    Route::get('/tenders/docsparam', [TenderController::class, 'listTenderDocsParam'])->name('tender.docsparam');
    Route::post('/tenders/add', [TenderController::class, 'AddTender'])->name('tender.add');
    Route::post('/tenders/edit', [TenderController::class, 'editTender'])->name('editTender');
    Route::post('/tenders/doc_add', [TenderController::class, 'AddTenderDocParam'])->name('tender.doc_add');
    Route::get('/tender-document-details/{docId}', [TenderController::class, 'viewDocumentDetails'])->name('tender.document.details');
    Route::post('/tenders/addToc', [TenderController::class, 'AddTenderToc'])->name('tender.Tocadd');
    Route::get('/search.tocCategory', [TenderController::class, 'SearchTocCatgery'])->name('search.tocCategory');
    Route::post('/tenders/addTocItem', [TenderController::class, 'AddTenderTocItem'])->name('tender.TocItemadd');
    Route::get('/document/{id}', [TenderController::class, 'viewDocument'])->name('view.document');
    // Route::post('/tenders/details', [TenderController::class,'TenderDetails'])->name('tender.tenderdetails');
    Route::get('/tenders/getTenderSectcheckeditems', [TenderController::class, 'getTenderSectcheckeditems'])->name('tenders.getcheckitems');

    Route::post('/TenderPrint', [TenderController::class, 'TenderPrint'])->name('tender-printout');
    Route::get('/subcat/doc', [TenderController::class, 'getSubcatDoc'])->name('get_subcat_doc');
    Route::post('/doc/preview', [TenderController::class, 'previewDoc'])->name('doc_preview');
    Route::post('/save_tendor_color', [TenderController::class, 'saveTendorColors'])->name('save_tendor_color');
    Route::post('send_tender_email', [TenderController::class, 'sendTenderEmail'])->name('send.tender.email');
    Route::post('/tender/edit-subcat', [TenderController::class, 'editSubcat'])->name('tender.editSubcat');
    Route::post('/tender/edit-toc', [TenderController::class, 'editTocSection'])->name('tender.editToc');

    Route::post('/tender/submit-for-approval', [TenderController::class, 'submitForApproval'])->name('tender.submitForApproval');
    Route::get('/tender/approvals', [TenderController::class, 'listApprovals'])->name('tender.approvals');
    Route::post('/tender/approve', [TenderController::class, 'approveTender'])->name('tender.approve');
    Route::post('/tender/reject', [TenderController::class, 'rejectTender'])->name('tender.rejectApproval');
    Route::post('/preview-tender-letter', [TenderController::class, 'previewTenderLetter'])->name('tender.letter.preview');

    Route::post('/search', [TenderController::class, 'search'])->name('search_tender_emails');
    Route::get('/tender/cedant/contact', [PipelineController::class, 'TenderCedantContactPerson'])->name('get_cedant_contact');
    Route::post('/search', [TenderController::class, 'search'])->name('search_tender_emails');
    Route::any('/doc-attachment', [PipelineController::class, 'TenderDocAttachement'])->name('tender.docs');

    // Canonical BD handovers URL (kebab-case), with legacy underscore redirects.
    Route::redirect('/bd_handovers', '/bd-handovers', 301);
    Route::redirect('/bd_handovers/statistics', '/bd-handovers/statistics', 301);
    Route::redirect('/bd_handovers/datatable', '/bd-handovers/datatable', 301);
    Route::redirect('/bd_handovers/export', '/bd-handovers/export', 301);
    Route::get('/bd_handovers/{id}', function ($id) {
        return redirect()->route('pipeline.bd_handover_details', ['id' => $id], 301);
    })->whereNumber('id');

    Route::get('/business-development/handovers', [BdHandoverController::class, 'index'])
        ->name('pipeline.bd_handovers');

    Route::get('/business-development/statistics', [BdHandoverController::class, 'getStatistics'])
        ->name('pipeline.bd_handovers_stats');

    Route::get('/business-development/datatable', [BdHandoverController::class, 'getDataTableData'])
        ->name('pipeline.bd_handovers_datatable');

    Route::post('/business-development/create-cover', [BdHandoverController::class, 'createCover'])
        ->name('pipeline.create_cover');

    Route::get('/business-development/export', [BdHandoverController::class, 'export'])
        ->name('pipeline.bd_handovers_export');

    Route::get('/business-development/{id}', [BdHandoverController::class, 'show'])
        ->name('pipeline.bd_handover_details');
});

Route::get('/auth/outlook/callback', [OutlookOAuthController::class, 'callback'])->name('admin.outlook.callback');

Route::get('/reverb-health', function () {
    try {
        // Try to connect to Reverb port
        $connection = @fsockopen('127.0.0.1', 8080, $errno, $errstr, 1);

        if ($connection) {
            fclose($connection);
            return response()->json([
                'status' => 'healthy',
                'reverb' => 'running',
                'port' => 8080,
                'timestamp' => now()
            ]);
        }

        return response()->json([
            'status' => 'unhealthy',
            'reverb' => 'not accessible',
            'error' => $errstr,
            'timestamp' => now()
        ], 503);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

require __DIR__ . '/auth.php';
