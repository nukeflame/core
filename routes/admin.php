<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\IntergrationController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SystemActionController;
use App\Http\Controllers\Settings\SystemProcessController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::get('users', [UserController::class, 'index'])->name('admin.users');
    Route::get('users/data', [UserController::class, 'getUserData'])->name('admin.users.data');
    Route::post('users/store', [UserController::class, 'store'])->name('admin.users.store');
    // ->middleware('permission:manage-users');
    Route::delete('changePassword', [ProfileController::class, 'destroy'])->name('changePassword');
    Route::post('user/destroy', [UserController::class, 'destroy'])->name('admin.user.destroy');

    Route::post('register-user', [ProfileController::class, 'store'])->name('store.user');
    Route::get('myprofile-user', [ProfileController::class, 'myProfile'])->name('user.myprofile');
    Route::get('passwdpg', [ProfileController::class, 'getChangepwd'])->name('user.changepwd');
    Route::post('myprofile/changePassword', [ProfileController::class, 'changeMyPass'])->name('profile.chgpwd');

    Route::post('username/check', [UserController::class, 'checkUsername'])->name('admin.username.check');
    Route::post('email/domain/check', [UserController::class, 'checkEmailDomain'])->name('admin.email.domain.check');
    Route::get('roles/by-department', [UserController::class, 'getRolesByDepartment'])->name('admin.roles.by.department');

    Route::get('roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::post('roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::post('assign/permissions', [RoleController::class, 'assignRolePermission'])->name('admin.permissions.assign');
    Route::post('assign/department', [DepartmentController::class, 'assign'])->name('admin.departments.assign');
    Route::post('roles/destroy', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions');
    Route::get('get-permissions/{category}', [PermissionController::class, 'getPermissionsByCategory'])->name('admin.perms.by.category');
    Route::post('assign/roles', [PermissionController::class, 'assignPermissionRole'])->name('admin.roles.assign');

    Route::get('system-process', [SystemProcessController::class, 'systemProcess'])->name('admin.system-processes');
    Route::post('system/store', [SystemProcessController::class, 'store'])->name('admin.system_processes.store');
    Route::get('system_processes_datatable', [SystemProcessController::class, 'systemProcessesDatatable'])->name('settings.system_processes_datatable');

    Route::get('system-actions', [SystemActionController::class, 'index'])->name('admin.system-actions');
    Route::post('store-action', [SystemActionController::class, 'store'])->name('admin.system_actions.store');
    Route::get('system_process_action_datatable', [SystemActionController::class, 'systemProcessActionDatatable'])->name('settings.system_process_action_datatable');

    // Route::resource('system_processes', SystemProcessController::class);
    // Route::post('store-action', [SystemProcessController::class, 'storeAction'])->name('system_processes.storeAction');
    // Route::post('delete-action', [SystemProcessController::class, 'deleteAction'])->name('system_processes.deleteAction');

    Route::get('general-configuration', [UserController::class, 'index'])->name('admin.general-configuration');

    Route::get('integrations-api', [IntergrationController::class, 'processes'])->name('admin.integrations_api');
    Route::post('processes/stop', [IntergrationController::class, 'processStop'])->name('admin.processes.stop');
    Route::post('processes/restart', [IntergrationController::class, 'processRestart'])->name('admin.processes.restart');

    Route::get('processes/data', [IntergrationController::class, 'processDatatable'])->name('admin.processes.data');

    Route::get('budget-allocation', [BudgetController::class, 'index'])->name('admin.budget_allocation');
    Route::get('budget-allocation/form', [BudgetController::class, 'budgetForm'])->name('admin.budget_allocation.form');
    Route::get('budget-allocation/data', [BudgetController::class, 'getBudgetAllocationData'])->name('admin.budget_allocation.data');
    Route::delete('budget-allocation', [BudgetController::class, 'destroyBudgetAllocation'])->name('admin.budget_allocation.destroy');

    Route::get('budget-allocation/incomes/create', [BudgetController::class, 'createIncomeBudget'])->name('admin.budget_allocation.incomes_create');
    Route::post('budget-allocation/incomes/store', [BudgetController::class, 'storeIncomeBudget'])->name('admin.budget_allocation.incomes_store');

    Route::get('budget-allocation/expenses/create', [BudgetController::class, 'createExpenseBudget'])->name('admin.budget_allocation.expenses_create');
    Route::post('budget-allocation/expenses/store', [BudgetController::class, 'storeExpenseBudget'])->name('admin.budget_allocation.expenses_store');

    Route::get('budget-allocation/view', [BudgetController::class, 'viewImport'])->name('admin.budget_allocation.view');
    Route::get('budget-allocation/import', [BudgetController::class, 'showImportForm'])->name('admin.budget_allocation.import');

    Route::get('budget-allocation/download-template', [BudgetController::class, 'downloadTemplate'])->name('admin.budget_allocation.download_template');
    Route::post('budget-incomes/validate', [BudgetController::class, 'validateImport'])->name('admin.budget_allocation.validate');
    Route::post('budget-allocation/import', [BudgetController::class, 'startImport'])->name('admin.budget_allocation.start_import');
    Route::get('budget-allocation/import-progress/{jobId}', [BudgetController::class, 'getImportProgress']);
    Route::post('budget-allocation/cancel-import/{jobId}', [BudgetController::class, 'cancelImport']);
    Route::post('budget-allocation/resume-import/{jobId}', [BudgetController::class, 'resumeImport']);
    // Route::post('budget-allocation/process-import', [BudgetController::class, 'processImport'])->name('budget-incomes.process-import');

    Route::get('staff-budget-allocation/data', [BudgetController::class, 'getStaffData'])->name('admin.staff_budget_allocation.data');

    Route::post('performance-records', [BudgetController::class, 'storePerformanceRecords'])->name('admin.performance-records.store');
    Route::put('performance-records/{id}', [BudgetController::class, 'updatePerformanceRecords'])->name('admin.performance-records.update');
    Route::get('performance-get-staff', [BudgetController::class, 'getStaff'])->name('admin.performance-get.staff');
    Route::get('fiscal-years', [BudgetController::class, 'getFiscalYears'])->name('admin.fiscal-years');

    Route::get('staff-notices', [StaffNoticeController::class, 'index'])->name('admin.staff_notices');
    // Route::get('staff-notices/create', [StaffNoticeController::class, 'create'])->name('admin.staff-notices.create');
    Route::post('staff-notices', [StaffNoticeController::class, 'store'])->name('admin.staff-notices.store');
    Route::get('staff-notices/{storeNotice}/edit', [StaffNoticeController::class, 'edit'])->name('admin.staff-notices.edit');
    Route::put('staff-notices/{storeNotice}', [StaffNoticeController::class, 'update'])->name('admin.staff-notices.update');
    Route::delete('staff-notices/{storeNotice}', [StaffNoticeController::class, 'destroy'])->name('admin.staff-notices.destroy');
    Route::get('staff-notices/get-data', [StaffNoticeController::class, 'getData'])->name('admin.store-notices.getData');
    Route::get('staff-notices/get-notices', [StaffNoticeController::class, 'getNotices'])->name('admin-notices.getNotices');

    Route::get('mail/index', [EmailController::class, 'index'])->name('admin.email');
    Route::get('mail/settings', [EmailController::class, 'settings'])->name('admin.email.settings');

    Route::get('mail/folder/{folder}', [EmailController::class, 'getFolder'])->name('admin.folder');
    Route::get('mail/inbox/id/{messageId}', [EmailController::class, 'show'])
        ->name('mail.inbox.show')
        ->where('messageId', '.*');
});

Route::group(
    ['middleware' => ['auth', 'check.first.login']],
    function () {
        Route::prefix('/auth/outlook')->group(function () {
            Route::get('callback', [OutlookOAuthController::class, 'callback'])
                ->name('admin.outlook.callback');
            Route::post('connect', [OutlookOAuthController::class, 'connect'])
                ->name('admin.outlook.connect');
            Route::get('/status', [OutlookOAuthController::class, 'status'])->name('admin.outlook.status');
            Route::delete('/disconnect', [OutlookOAuthController::class, 'disconnect'])->name('admin.outlook.disconnect');
            // Route::post('refresh', [OutlookOAuthController::class, 'refreshToken'])
            //     ->name('outlook.refresh);
            // Route::post('revoke', [OutlookOAuthController::class, 'revoke'])
            //     ->name('outlook.revoke');
        });
        Route::prefix('/auth/google')->group(function () {
            Route::get('callback', [GoogleOAuthController::class, 'callback'])
                ->name('oauth.google.callback');

            Route::post('refresh', [GoogleOAuthController::class, 'refreshToken'])
                ->name('oauth.google.refresh');

            Route::post('revoke', [GoogleOAuthController::class, 'revoke'])
                ->name('oauth.google.revoke');
        });
    }
);

Route::group(['prefix' => 'emails', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::post('/fetch', [EmailController::class, 'fetchEmails'])->name('admin.emails.fetch');
    // Route::get('/emails/compose', [EmailController::class, 'compose'])->name('emails.compose');
    // Route::post('/emails/send', [EmailController::class, 'send'])->name('emails.send');
    // Route::get('/emails/{id}', [EmailController::class, 'show'])->name('emails.show');
    // Route::post('/emails/{id}/reply', [EmailController::class, 'reply'])->name('emails.reply');
    // Route::post('/emails/{id}/star', [EmailController::class, 'star'])->name('emails.star');
    // Route::post('/emails/{id}/mark-read', [EmailController::class, 'markAsRead'])->name('emails.mark-read');
    // Route::get('/emails/folder/{folder}', [EmailController::class, 'folder'])->name('emails.folder');
    // Route::get('/emails/{id}/attachment/{index}', [EmailController::class, 'downloadAttachment'])->name('emails.attachment');
    // Route::get('/', [EmailController::class, 'index']);
    // Route::get('/thread/{threadId}', [EmailController::class, 'thread']);
    // Route::post('/send', [EmailController::class, 'send']);
    // Route::post('/reply/{messageId}', [EmailController::class, 'reply']);

    // // Microsoft Graph webhook endpoints
    // Route::post('/webhook/microsoft', [EmailController::class, 'microsoftWebhook']);
    // Route::post('/setup-subscription', [EmailController::class, 'setupSubscription']);
    // Route::post('/renew-subscription', [EmailController::class, 'renewSubscription']);
});
