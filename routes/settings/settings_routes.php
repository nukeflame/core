<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\SystemProcessController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\BudgetSetupController;
use App\Http\Controllers\SettingsFinanceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'settings', 'middleware' => ['auth']], function () {
    Route::get('profile', [ProfileController::class, 'index'])->name('settings.profile');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('general-config', [SettingsController::class, 'generalConfig'])->name('settings.general_config');

    Route::get('departments', [SettingsController::class, 'departments'])->name('settings.departments');
    Route::get('departments/data', [SettingsController::class, 'departmentsDatatable'])->name('settings.departments.data');
    Route::post('department-save', [SettingsController::class, 'storeDepartment'])->name('settings.department.store');

    Route::get('branches', [BranchController::class, 'branches'])->name('settings.branches');
    Route::get('branches/data', [BranchController::class, 'branchesDatatable'])->name('settings.branch.data');

    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::any('company-info', [SettingsController::class, 'companyInfo'])->name('settings.company_info');
    Route::post('editcompanydetails', [SettingsController::class, 'CompanyEdit'])->name('settings.editcompanydetails');

    Route::get('/settingsMenus', [GeneralSettingsController::class, 'settingsMenus'])->name('settings.settingsMenus');
    Route::post('/saveSettingsMenus', [GeneralSettingsController::class, 'saveSettingsMenus'])->name('settings.saveSettingsMenus');
    Route::post('/editSettingsMenus', [GeneralSettingsController::class, 'editSettingsMenus'])->name('settings.editSettingsMenus');
    Route::post('/deleteSettingsMenus', [GeneralSettingsController::class, 'deleteSettingsMenus'])->name('settings.deleteSettingsMenus');

    // Route::post('/cDepartments-store', [GeneralSettingsController::class, 'cDepartmentsAddData'])->name('cDepartments.store');
    Route::post('/cDepartments-edit', [GeneralSettingsController::class, 'cDepartmentsEditData'])->name('cDepartments.edit');
    Route::post('/cDepartments-delete', [GeneralSettingsController::class, 'cDepartmentsDeleteData'])->name('cDepartments.delete');

    // Tax Groups
    Route::get('finance/taxGroup', [SettingsFinanceController::class, 'taxGroupInfo'])->name('taxGroup.index');
    Route::get('finance/taxGroup/data', [SettingsFinanceController::class, 'taxGroupData'])->name('taxGroup.data');
    Route::post('finance/taxGroup-store', [SettingsFinanceController::class, 'taxGroupAddData'])->name('taxGroup.store');
    Route::post('finance/taxGroup-edit', [SettingsFinanceController::class, 'taxGroupEditData'])->name('taxGroup.edit');

    // Tax Types
    Route::get('finance/taxType', [SettingsFinanceController::class, 'taxTypeInfo'])->name('taxType.index');
    Route::get('finance/taxType/data', [SettingsFinanceController::class, 'taxTypeData'])->name('taxType.data');
    Route::post('finance/taxType-store', [SettingsFinanceController::class, 'taxTypeAddData'])->name('taxType.store');
    Route::post('finance/taxType-edit', [SettingsFinanceController::class, 'taxTypeEditData'])->name('taxType.edit');

    // Tax Rates
    Route::get('finance/taxRate', [SettingsFinanceController::class, 'taxRateInfo'])->name('taxRate.index');
    Route::get('finance/taxRate/data', [SettingsFinanceController::class, 'taxRateData'])->name('taxRate.data');
    Route::post('finance/taxRate-store', [SettingsFinanceController::class, 'taxRateAddData'])->name('taxRate.store');
    Route::post('finance/taxRate-edit', [SettingsFinanceController::class, 'taxRateEditData'])->name('taxRate.edit');



    // Budget Setup
    Route::get('budget-setup', [BudgetSetupController::class, 'index'])->name('settings.budgetSetup.index');
    Route::get('budget-setup/data', [BudgetSetupController::class, 'data'])->name('settings.budgetSetup.data');
    Route::post('budget-setup/store', [BudgetSetupController::class, 'store'])->name('settings.budgetSetup.store');
    Route::post('budget-setup/update', [BudgetSetupController::class, 'update'])->name('settings.budgetSetup.update');
    Route::post('budget-setup/destroy', [BudgetSetupController::class, 'destroy'])->name('settings.budgetSetup.destroy');
    Route::post('budget-setup/copy', [BudgetSetupController::class, 'copyFromFiscalYear'])->name('settings.budgetSetup.copy');

    // User Budget Setup
    Route::get('budget-setup/users', [BudgetSetupController::class, 'userSetupIndex'])->name('settings.budgetSetup.users');
    Route::get('budget-setup/users/data', [BudgetSetupController::class, 'userSetupData'])->name('settings.budgetSetup.users.data');
    Route::get('budget-setup/users/{id}/show', [BudgetSetupController::class, 'userSetupShow'])->name('settings.budgetSetup.users.show');
    Route::post('budget-setup/users/store', [BudgetSetupController::class, 'userSetupStore'])->name('settings.budgetSetup.users.store');
    Route::post('budget-setup/users/update', [BudgetSetupController::class, 'userSetupUpdate'])->name('settings.budgetSetup.users.update');

    //settings cover
    require_once('settings_cover_routes.php');
    require_once('settings_claim_routes.php');
    // permissions
    require_once('system_access.php');
});
