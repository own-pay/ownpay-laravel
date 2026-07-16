<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OwnPay\Laravel\Exception\SignatureVerificationException;
use OwnPay\Laravel\Webhook\WebhookVerifier;

/**
 * Middleware to verify webhook signatures.
 *
 * This middleware verifies the HMAC-SHA256 signature of incoming
 * webhook payloads from OwnPay.
 */
class VerifyWebhookSignature
{
    /**
     * Create a new VerifyWebhookSignature middleware.
     *
     * @param  WebhookVerifier  $verifier  The webhook verifier.
     */
    public function __construct(
        private readonly WebhookVerifier $verifier,
    ) {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming request.
     * @param  Closure(Request): Response  $next  The next middleware.
     * @return Response|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        try {
            $payload = $request->getContent();
            $headers = $request->headers->all();

            // Normalize headers to string values
            $normalizedHeaders = [];
            foreach ($headers as $key => $values) {
                $normalizedHeaders[$key] = implode(', ', (array) $values);
            }

            // Verify the webhook
            $this->verifier->verifyRequest($payload, $normalizedHeaders);

            // Parse and attach verified payload to request
            $decoded = json_decode($payload, true);
            if (is_array($decoded)) {
                $request->merge(['_verified_webhook' => $decoded]);
            }

            return $next($request);
        } catch (SignatureVerificationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $e->getErrorCode(),
            ], $e->getHttpStatusCode() ?? 400);
        }
    }
}
