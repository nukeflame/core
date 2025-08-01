<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'notifications', 'middleware' => ['auth', 'check.first.login']], function () {
    Route::get('/index', [NotificationController::class, 'fetchNotifications'])->name('notifications.index');
    Route::get('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name(
        'notifications.markAsRead'
    );
    Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('show/{id}', [NotificationController::class, 'show'])->name('notifications.show');
});
