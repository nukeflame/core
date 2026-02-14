<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'emails', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::post('/fetch_emails', [MailController::class, 'fetchEmails'])->name('admin.emails.fetch');
    Route::post('/claims/send-reinsurer-email', [MailController::class, 'sendClaimReinsurerEmail'])->name('emails.send_claim_reinsurer_email');
});

Route::group(
    ['prefix' => 'mail'],
    function () {
        Route::get('/', [MailController::class, 'index'])->name('mail.index');
        Route::get('/folder/{folder}', [MailController::class, 'folder'])->name('mail.folder');

        Route::get('/message/{id}/detail', [MailController::class, 'messageDetail'])->name('mail.message.detail');
        Route::get('/email/{id}', [MailController::class, 'show'])->name('mail.show');
        Route::post('/send', [MailController::class, 'send'])->name('mail.send');
        Route::post('/reply/{id}', [MailController::class, 'reply'])->name('mail.reply');

        Route::post('/star/{id}', [MailController::class, 'star'])->name('star');
        // Route::delete('/delete/{id}', [MailController::class, 'delete'])->name('delete');
        // Route::post('/archive/{id}', [MailController::class, 'archive'])->name('archive');
        // Route::post('/spam/{id}', [MailController::class, 'spam'])->name('spam');
        // Route::post('/read/{id}', [MailController::class, 'markRead'])->name('read');
        // Route::post('/unread/{id}', [MailController::class, 'markUnread'])->name('unread');

        Route::get('/check-new', [MailController::class, 'checkNew'])->name('mail.check-new');
        Route::get('/current-month', [MailController::class, 'currentMonthEmails'])->name('mail.current-month');
        Route::post('/sync/trigger', [MailController::class, 'triggerSync'])->name('mail.sync.trigger');
        Route::get('/inbox/id/{messageId}', [MailController::class, 'showInbox'])
            ->name('mail.inbox.show')
            ->where('messageId', '.*');
        Route::get('/inbox/{messageId}', [MailController::class, 'showInbox'])
            ->name('mail.inbox.professional')
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

Route::group(['prefix' => 'mailbox'], function () {
    Route::get('/', [MailController::class, 'index'])->name('mailbox.index');
    Route::get('/folder/{folder}', [MailController::class, 'folder'])->name('mailbox.folder');
    Route::get('/email/{id}', [MailController::class, 'show'])->name('mailbox.show');
    Route::get('/inbox/{messageId}', [MailController::class, 'showInbox'])
        ->name('mailbox.inbox.show')
        ->where('messageId', '.*');
});



Route::get('/outlook/images/{messageId}', [MailController::class, 'getInlineImages'])->name('outlook.images');
