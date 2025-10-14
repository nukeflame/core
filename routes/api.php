<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\MicrosoftWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//
Route::post('/budgets', [BudgetController::class, 'budgets']);

// Route::match(
//     ['get', 'post'],
//     '/webhooks/graph/notifications',
//     [MicrosoftWebhookController::class, 'handleNotification']
// )
//     ->name('graph.webhook');
// GET for validation
Route::get(
    '/webhooks/graph/notifications',
    [MicrosoftWebhookController::class, 'handleNotification']
);

// POST for notifications
Route::post(
    '/webhooks/graph/notifications',
    [MicrosoftWebhookController::class, 'handleNotification']
);
