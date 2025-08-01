<?php

use App\Http\Controllers\SettingsClaimController;
use Illuminate\Support\Facades\Route;

/*group route for claims settings*/

Route::group(['prefix' => 'claims','as' =>'settings.claims.', 'middleware' => ['auth', 'passexpiry']], function () {
    
    Route::get('/claimAckDoc', [SettingsClaimController::class, 'claimAckDoc'])->name('claimAckDoc');
    Route::post('/saveClaimAckDoc', [SettingsClaimController::class, 'saveClaimAckDoc'])->name('saveClaimAckDoc');
    Route::post('/editClaimAckDoc', [SettingsClaimController::class, 'editClaimAckDoc'])->name('editClaimAckDoc');
    Route::post('/deleteClaimAckDoc', [SettingsClaimController::class, 'deleteClaimAckDoc'])->name('deleteClaimAckDoc');
    
    Route::get('/claimStatus', [SettingsClaimController::class, 'claimStatus'])->name('claimStatus');
    Route::post('/saveClaimStatus', [SettingsClaimController::class, 'saveClaimStatus'])->name('saveClaimStatus');
    Route::post('/editClaimStatus', [SettingsClaimController::class, 'editClaimStatus'])->name('editClaimStatus');
    Route::post('/deleteClaimStatus', [SettingsClaimController::class, 'deleteClaimStatus'])->name('deleteClaimStatus');
});//->name('settings.claims.');