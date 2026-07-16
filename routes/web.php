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
*/

Route::post('/webhooks/ownpay', [WebhookController::class, 'handle'])
    ->middleware(['web', 'ownpay.webhook'])
    ->name('ownpay.webhook');
