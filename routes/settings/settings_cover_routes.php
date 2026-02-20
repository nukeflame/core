<?php

use App\Http\Controllers\SettingsCoverController;
use Illuminate\Support\Facades\Route;

/*group route for cover settings*/

Route::group(['prefix' => 'cover', 'middleware' => ['auth', 'passexpiry']], function () {
    Route::get('/branch', [SettingsCoverController::class, 'BranchInfo'])->name('branch.info');
    Route::get('/branch-data', [SettingsCoverController::class, 'BranchData'])->name('branch.data');
    Route::post('/branch-store', [SettingsCoverController::class, 'BranchAddData'])->name('branch.store');
    Route::post('/branch-edit', [SettingsCoverController::class, 'BranchEditData'])->name('branch.edit');
    Route::post('/branch-delete', [SettingsCoverController::class, 'BranchDeleteData'])->name('branch.delete');

    Route::redirect('/classGroup', '/settings/cover/class-groups', 301)->name('classGroup.info');
    Route::redirect('/stage-documents', '/settings/cover/class-groups', 301)->name('cover.stage-documents.info');
    Route::get('/class-groups', [SettingsCoverController::class, 'ClassGroupInfo'])->name('cover.class-groups.info');
    Route::get('/classGroup-data', [SettingsCoverController::class, 'ClassGroupData'])->name('classGroup.data');
    Route::post('/classGroup-store', [SettingsCoverController::class, 'ClassGroupAddData'])->name('classGroup.store');
    Route::post('/classGroup-edit', [SettingsCoverController::class, 'ClassGroupEditData'])->name('classGroup.edit');
    Route::post('/classGroup-delete', [SettingsCoverController::class, 'ClassGroupDeleteData'])->name('classGroup.delete');

    Route::redirect('/class', '/settings/cover/classes', 301);
    Route::get('/classes', [SettingsCoverController::class, 'ClassInfo'])->name('class.info');
    Route::get('/class-data', [SettingsCoverController::class, 'ClassData'])->name('class.data');
    Route::post('/class-store', [SettingsCoverController::class, 'ClassAddData'])->name('class.store');
    Route::post('/class-edit', [SettingsCoverController::class, 'ClassEditData'])->name('class.edit');
    Route::post('/class-delete', [SettingsCoverController::class, 'ClassDeleteData'])->name('class.delete');

    Route::get('/classClauses', [SettingsCoverController::class, 'classClausesInfo'])->name('clauseparam.info');
    Route::get('/classClauses-data', [SettingsCoverController::class, 'classClausesData'])->name('clauseparam.data');
    Route::post('/classClauses-store', [SettingsCoverController::class, 'classClausesAddData'])->name('clauseparam.store');
    Route::post('/classClauses-edit', [SettingsCoverController::class, 'classClausesEditData'])->name('clauseparam.edit');
    Route::post('/classClauses-delete', [SettingsCoverController::class, 'classClausesDeleteData'])->name('clauseparam.delete');

    Route::redirect('/customerType', '/settings/cover/customer-types', 301);
    Route::get('/customer-types', [SettingsCoverController::class, 'CustomerTypeInfo'])->name('customerType.info');
    Route::get('/customerType-data', [SettingsCoverController::class, 'CustomerTypeData'])->name('customerType.data');
    Route::post('/customerType-store', [SettingsCoverController::class, 'CustomerTypeAddData'])->name('customerType.store');
    Route::post('/customerType-edit', [SettingsCoverController::class, 'CustomerTypeEditData'])->name('customerType.edit');
    Route::post('/customerType-delete', [SettingsCoverController::class, 'CustomerTypeDeleteData'])->name('customerType.delete');

    Route::redirect('/country', '/settings/cover/countries', 301);
    Route::get('/countries', [SettingsCoverController::class, 'CountryInfo'])->name('country.info');
    Route::get('/country-data', [SettingsCoverController::class, 'CountryData'])->name('country.data');
    Route::post('/country-store', [SettingsCoverController::class, 'CountryAddData'])->name('country.store');
    Route::post('/country-edit', [SettingsCoverController::class, 'CountryEditData'])->name('country.edit');
    Route::post('/country-delete', [SettingsCoverController::class, 'CountryDeleteData'])->name('country.delete');

    Route::redirect('/businessType', '/settings/cover/business-types', 301);
    Route::get('/business-types', [SettingsCoverController::class, 'BusinessTypeInfo'])->name('businessType.info');
    Route::get('/businessType-data', [SettingsCoverController::class, 'BusinessTypeData'])->name('businessType.data');
    Route::post('/businessType-store', [SettingsCoverController::class, 'BusinessTypeAddData'])->name('businessType.store');
    Route::post('/businessType-edit', [SettingsCoverController::class, 'BusinessTypeEditData'])->name('businessType.edit');
    Route::post('/businessType-delete', [SettingsCoverController::class, 'BusinessTypeDeleteData'])->name('businessType.delete');

    Route::get('/binder', [SettingsCoverController::class, 'BinderInfo'])->name('binder.info');
    Route::get('/binder-data', [SettingsCoverController::class, 'BinderData'])->name('binder.data');
    Route::post('/binder-store', [SettingsCoverController::class, 'BinderAddData'])->name('binder.store');
    Route::post('/binder-edit', [SettingsCoverController::class, 'BinderEditData'])->name('binder.edit');
    Route::post('/binder-delete', [SettingsCoverController::class, 'BinderDeleteData'])->name('binder.delete');

    Route::redirect('/payMethod', '/settings/cover/pay-methods', 301);
    Route::get('/pay-methods', [SettingsCoverController::class, 'PayMethodInfo'])->name('payMethod.info');
    Route::get('/payMethod-data', [SettingsCoverController::class, 'PayMethodData'])->name('payMethod.data');
    Route::post('/payMethod-store', [SettingsCoverController::class, 'PayMethodAddData'])->name('payMethod.store');
    Route::post('/payMethod-edit', [SettingsCoverController::class, 'PayMethodEditData'])->name('payMethod.edit');
    Route::post('/payMethod-delete', [SettingsCoverController::class, 'PayMethodDeleteData'])->name('payMethod.delete');

    Route::redirect('/sumInsType', '/settings/cover/sum-insured-types', 301);
    Route::get('/sum-insured-types', [SettingsCoverController::class, 'SumInsTypeInfo'])->name('sumInsType.info');
    Route::get('/sumInsType-data', [SettingsCoverController::class, 'SumInsTypeData'])->name('sumInsType.data');
    Route::post('/sumInsType-store', [SettingsCoverController::class, 'SumInsTypeAddData'])->name('sumInsType.store');
    Route::post('/sumInsType-edit', [SettingsCoverController::class, 'SumInsTypeEditData'])->name('sumInsType.edit');
    Route::post('/sumInsType-delete', [SettingsCoverController::class, 'SumInsTypeDeleteData'])->name('sumInsType.delete');

    Route::redirect('/reinsDivision', '/settings/cover/reins-divisions', 301);
    Route::get('/reins-divisions', [SettingsCoverController::class, 'ReinsDivisionInfo'])->name('reinsDivision.info');
    Route::get('/reinsDivision-data', [SettingsCoverController::class, 'ReinsDivisionData'])->name('reinsDivision.data');
    Route::post('/reinsDivision-store', [SettingsCoverController::class, 'ReinsDivisionAddData'])->name('reinsDivision.store');
    Route::post('/reinsDivision-edit', [SettingsCoverController::class, 'ReinsDivisionEditData'])->name('reinsDivision.edit');
    Route::post('/reinsDivision-delete', [SettingsCoverController::class, 'ReinsDivisionDeleteData'])->name('reinsDivision.delete');

    Route::redirect('/reinsClass', '/settings/cover/reins-classes', 301);
    Route::get('/reins-classes', [SettingsCoverController::class, 'ReinsClassInfo'])->name('reinsClass.info');
    Route::get('/reinsClass-data', [SettingsCoverController::class, 'ReinsClassData'])->name('reinsClass.data');
    Route::post('/reinsClass-store', [SettingsCoverController::class, 'ReinsClassAddData'])->name('reinsClass.store');
    Route::post('/reinsClass-edit', [SettingsCoverController::class, 'ReinsClassEditData'])->name('reinsClass.edit');
    Route::post('/reinsClass-delete', [SettingsCoverController::class, 'ReinsClassDeleteData'])->name('reinsClass.delete');

    Route::redirect('/reinsClassPremtypes', '/settings/cover/reins-class-premtypes', 301);
    Route::get('/reins-class-premtypes', [SettingsCoverController::class, 'reinsClassPremtypesInfo'])->name('reinsClassPremtypes.info');
    Route::get('/reinsClassPremtypes-data', [SettingsCoverController::class, 'reinsClassPremtypesData'])->name('reinsClassPremtypes.data');
    Route::post('/reinsClassPremtypes-store', [SettingsCoverController::class, 'reinsClassPremtypesAddData'])->name('reinsClassPremtypes.store');
    Route::post('/reinsClassPremtypes-edit', [SettingsCoverController::class, 'reinsClassPremtypesEditData'])->name('reinsClassPremtypes.edit');
    Route::post('/reinsClassPremtypes-delete', [SettingsCoverController::class, 'reinsClassPremtypesDeleteData'])->name('reinsClassPremtypes.delete');

    Route::redirect('/treatyType', '/settings/cover/treaty-types', 301);
    Route::get('/treaty-types', [SettingsCoverController::class, 'treatyTypeInfo'])->name('treatyType.info');
    Route::get('/treatyType-data', [SettingsCoverController::class, 'treatyTypeData'])->name('treatyType.data');
    Route::post('/treatyType-store', [SettingsCoverController::class, 'treatyTypeAddData'])->name('treatyType.store');
    Route::post('/treatyType-edit', [SettingsCoverController::class, 'treatyTypeEditData'])->name('treatyType.edit');
    Route::post('/treatyType-delete', [SettingsCoverController::class, 'treatyTypeDeleteData'])->name('treatyType.delete');

    Route::get('/whtRate', [SettingsCoverController::class, 'whtRate'])->name('settings.whtRate');
    Route::post('/saveWhtRate', [SettingsCoverController::class, 'saveWhtRate'])->name('settings.saveWhtRate');
    Route::post('/editWhtRate', [SettingsCoverController::class, 'editWhtRate'])->name('settings.editWhtRate');
    Route::post('/deleteWhtRate', [SettingsCoverController::class, 'deleteWhtRate'])->name('settings.deleteWhtRate');

    Route::get('/reins-class', [SettingsCoverController::class, 'ReinsClass'])->name('settings.reins.class');
});
