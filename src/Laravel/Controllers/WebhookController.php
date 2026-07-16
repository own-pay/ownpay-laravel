<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Webhook controller for handling OwnPay webhook events.
 *
 * This controller receives webhook payloads from OwnPay and
 * dispatches them to the application's event handlers.
 */
class WebhookController extends Controller
{
    /**
     * Handle an incoming webhook.
     *
     * @param  Request  $request  The incoming request with verified webhook payload.
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->input('_verified_webhook');

        if (! is_array($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook payload.',
            ], 400);
        }

        // Dispatch webhook event
        $event = $payload['event'] ?? 'unknown';

        // Fire Laravel event for the webhook
        event(new \OwnPay\Laravel\Laravel\Events\WebhookReceived($event, $payload));

        return response()->json([
            'success' => true,
            'message' => 'Webhook received.',
        ]);
    }
}
