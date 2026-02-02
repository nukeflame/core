<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BdController\PipelineController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'sidebar.cookie', 'check.first.login']], function () {
    Route::get('/analytics', [AnalyticsController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/business-intelligence/budget_tracker', [AnalyticsController::class, 'budgetTracker'])->name('bi.budget_tracker');
    Route::get('/business-intelligence/cover', [AnalyticsController::class, 'coverAdministration'])->name('bi.cover');
    Route::get('/business-intelligence/claims', [AnalyticsController::class, 'claimsAdministration'])->name('bi.claims');
    Route::get('/business-intelligence/debtors', [AnalyticsController::class, 'debtors'])->name('bi.debtors');
    Route::get('/business-intelligence/bd', [AnalyticsController::class, 'businessDevelopment'])->name('bi.business_development');

    Route::get('/getBudegetAchievedGWPData', [AnalyticsController::class, 'getBudegetAchievedGWPData'])->name('budegetAchievedGWPData.data');
    Route::get('/kpis/list', [AnalyticsController::class, 'getKpis'])->name('kpis.getKpis');

    Route::get('/reports/facultative', [AnalyticsController::class, 'facultativeReport'])->name('reports.facultative');

    Route::get('appointments/data', [DashboardController::class, 'appointmentsDatatable'])->name('dashboard.appointments.data');
    Route::get('/metrics', [DashboardController::class, 'getMetrics'])->name('dashboard.metrics');
    Route::post('appointments/store', [AppointmentController::class, 'store'])->name('dashboard.appointments.store');

    Route::get('/todos/load', [TodoController::class, 'load'])->name('todos.load');
    Route::post('/todos/save', [TodoController::class, 'save'])->name('todos.save');
    Route::post('/todos/add', [TodoController::class, 'add'])->name('todos.add');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
});

Route::group(['prefix' => 'reports', 'middleware' => ['auth', 'sidebar.cookie', 'check.first.login']], function () {
    Route::get('cover-reports', [CoverReportController::class, 'index'])->name('cover-reports.index');
    Route::get('cover-reports/export', [CoverReportController::class, 'export'])->name('cover-reports.export');
    Route::get('cover-reports/print', [CoverReportController::class, 'print'])->name('cover-reports.print');

    Route::get('/cover-reports/cover-placement/data', [CoverReportController::class, 'getCoverPlacementData'])
        ->name('cover-reports.cover_placement.data');
    Route::get('/cover-reports/filter-options', [CoverReportController::class, 'getFilterOptions'])
        ->name('cover-reports.filter-options');

    Route::get('production-reports', [ProductionReportController::class, 'summary'])->name('production-reports.index');
    Route::get('production-reports/detailed', [ProductionReportController::class, 'detailed'])->name('production-reports.detailed');
    Route::get('production-reports/fac-type', [ProductionReportController::class, 'facType'])->name('production-reports.fac_type');

    Route::get('production-reports/treaty-type', [ProductionReportController::class, 'facType'])->name('production-reports.treaty_type');
    Route::get('facultative-reports/summary', [ProductionReportController::class, 'facType'])->name('facultative-reports.summary');
    Route::get('facultative-reports/placement', [ProductionReportController::class, 'facType'])->name('facultative-reports.placement');
    Route::get('treaty-reports/proportional', [ProductionReportController::class, 'facType'])->name('treaty-reports.proportional');
    Route::get('treaty-reports/non-proportional', [ProductionReportController::class, 'facType'])->name('treaty-reports.non_proportional');
    Route::get('other-reports/exception-reports', [ProductionReportController::class, 'facType'])->name('other-reports.exception_reports');
    Route::get('other-reports/reports', [ProductionReportController::class, 'facType'])->name('other-reports.other_reports');


    Route::get('production-reports/debit-type/business', [ProductionReportController::class, 'getDebitTypeBusinessData'])->name('production-reports.debit-type.business');
    Route::get('production-reports/debit-type/financial', [ProductionReportController::class, 'getDebitTypeFinancialData'])->name('production-reports.debit-type.financial');
    Route::get('production-reports/export', [ProductionReportController::class, 'export'])->name('production-reports.export');


    Route::get('sales-reports', [PipelineController::class, 'sales_report'])->name('sales.report');
    Route::get('pipeline-reports', [PipelineController::class, 'pipeline_report'])->name('pipeline.report');
});
