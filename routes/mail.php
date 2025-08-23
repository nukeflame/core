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
//     // Route::post('/fetch', [MailController::class, 'fetchEmails'])->name('admin.emails.fetch');
//     // Route::post('/claims/send-reinsurer-email', [MailController::class, 'sendClaimReinsurerEmail'])->name('emails.send_claim_reinsurer_email');
// });

Route::group(
    ['prefix' => 'mail', 'middleware' => ['auth', 'check.first.login']],
    function () {
        Route::get('/', [MailController::class, 'index'])->name('mail.index');
        Route::get('/folder/{folder}', [MailController::class, 'folder'])->name('mail.folder');

        Route::get('/email/{id}', [MailController::class, 'show'])->name('mail.show');
        Route::post('/send', [MailController::class, 'send'])->name('mail.send');
        Route::post('/reply/{id}', [MailController::class, 'reply'])->name('mail.reply');

        Route::post('/star/{id}', [MailController::class, 'star'])->name('star');
        // Route::delete('/delete/{id}', [MailController::class, 'delete'])->name('delete');
        // Route::post('/archive/{id}', [MailController::class, 'archive'])->name('archive');
        // Route::post('/spam/{id}', [MailController::class, 'spam'])->name('spam');
        // Route::post('/read/{id}', [MailController::class, 'markRead'])->name('read');
        // Route::post('/unread/{id}', [MailController::class, 'markUnread'])->name('unread');

        Route::get('/check-new', [MailController::class, 'checkNew'])->name('check-new');
        Route::get('/inbox/id/{messageId}', [MailController::class, 'showInbox'])
            ->name('mail.inbox.show')
            ->where('messageId', '.*');

        Route::get('/attachment/{emailId}/{attachmentId}/download', [MailController::class, 'downloadAttachment'])
            ->name('attachment.download');
        Route::get('/email/{emailId}/attachments/download', [MailController::class, 'downloadAllAttachments'])
            ->name('attachments.download-all');

        Route::post('/outlook/connect', [OutlookOAuthController::class, 'connect'])->name('mail.outlook.connect');
        Route::get('/outlook/callback', [OutlookOAuthController::class, 'callback'])->name('mail.outlook.callback');
        Route::post('/outlook/sync', [OutlookOAuthController::class, 'sync'])->name('mail.outlook.sync');
        Route::post('/outlook/disconnect', [OutlookOAuthController::class, 'disconnect'])->name('disconnect');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::post('/save', [MailController::class, 'saveSettings'])->name('save');
        });

        Route::get('/contacts/get', [MailController::class, 'getContacts'])->name('contacts.get');


        // Route::get('mail/settings', [MailController::class, 'settings'])->name('admin.email.settings');

        // Route::get('mail/folder/{folder}', [MailController::class, 'getFolder'])->name('admin.folder');

    }
);
