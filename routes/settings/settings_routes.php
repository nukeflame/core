<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\SystemProcessController;
use App\Http\Controllers\Settings\SettingsController;
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

   

    //settings cover
    require_once('settings_cover_routes.php');
    require_once('settings_claim_routes.php');
    // permissions
    require_once('system_access.php');
});
