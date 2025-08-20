<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Route::group(
//     ['middleware' => ['auth', 'check.first.login']],
//     function () {
//         Route::prefix('/auth/outlook')->group(function () {
//             Route::get('callback', [OutlookOAuthController::class, 'callback'])
//                 ->name('admin.outlook.callback');
//             Route::post('connect', [OutlookOAuthController::class, 'connect'])
//                 ->name('admin.outlook.connect');
//             Route::get('/status', [OutlookOAuthController::class, 'status'])->name('admin.outlook.status');
//             Route::delete('/disconnect', [OutlookOAuthController::class, 'disconnect'])->name('admin.outlook.disconnect');
//             // Route::post('refresh', [OutlookOAuthController::class, 'refreshToken'])
//             //     ->name('outlook.refresh);
//             // Route::post('revoke', [OutlookOAuthController::class, 'revoke'])
//             //     ->name('outlook.revoke');
//         });
//         Route::prefix('/auth/google')->group(function () {
//             Route::get('callback', [GoogleOAuthController::class, 'callback'])
//                 ->name('oauth.google.callback');

//             Route::post('refresh', [GoogleOAuthController::class, 'refreshToken'])
//                 ->name('oauth.google.refresh');

//             Route::post('revoke', [GoogleOAuthController::class, 'revoke'])
//                 ->name('oauth.google.revoke');
//         });
//     }
// );

// Route::group(['prefix' => 'emails', 'middleware' => ['auth', 'check.first.login']], function () {
//     // Route::post('/fetch', [EmailController::class, 'fetchEmails'])->name('admin.emails.fetch');
//     // Route::post('/claims/send-reinsurer-email', [EmailController::class, 'sendClaimReinsurerEmail'])->name('emails.send_claim_reinsurer_email');
// });

Route::group(
    ['prefix' => 'mail', 'middleware' => ['auth', 'check.first.login']],
    function () {
        Route::get('/', [EmailController::class, 'index'])->name('mail.index');
        Route::get('/folder/{folder}', [EmailController::class, 'folder'])->name('mail.folder');
        Route::get('/email/{id}', [EmailController::class, 'show'])->name('mail.show');
        Route::post('/send', [EmailController::class, 'send'])->name('mail.send');
        Route::post('/reply/{id}', [EmailController::class, 'reply'])->name('mail.reply');

        Route::post('/outlook/connect', [OutlookOAuthController::class, 'connect'])->name('mail.outlook.connect');
        Route::get('/outlook/callback', [OutlookOAuthController::class, 'callback'])->name('mail.outlook.callback');
        Route::post('/outlook/sync', [OutlookOAuthController::class, 'sync'])->name('mail.outlook.sync');

        // Route::get('mail/settings', [EmailController::class, 'settings'])->name('admin.email.settings');

        // Route::get('mail/folder/{folder}', [EmailController::class, 'getFolder'])->name('admin.folder');
        // Route::get('mail/inbox/id/{messageId}', [EmailController::class, 'show'])
        //     ->name('mail.inbox.show')
        //     ->where('messageId', '.*');
    }
);
