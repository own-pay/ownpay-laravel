<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use OwnPay\Laravel\Laravel\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| OwnPay Webhook Routes
|--------------------------------------------------------------------------
|
| These routes handle incoming webhooks from OwnPay. The webhook
| signature verification middleware ensures the payload is authentic.
|
| Note: This route does NOT use the 'web' middleware group to avoid
| CSRF protection and session overhead on server-to-server webhook calls.
|
*/

Route::post('/webhooks/ownpay', [WebhookController::class, 'handle'])
    ->middleware('ownpay.webhook')
    ->name('ownpay.webhook');
