<?php

use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'/access','middleware'=>['auth','passexpiry']],function(){
    Route::resource('roles', RoleController::class)->middleware('can:manage roles');
    Route::resource('permissions', PermissionController::class)->middleware('can:manage permissions');
    Route::get('permissions_datatable', [PermissionController::class,'permissions_datatable'])->name('settings.permissions_datatable');
    Route::get('roles_datatable', [RoleController::class,'roles_datatable'])->name('settings.roles_datatable');
});