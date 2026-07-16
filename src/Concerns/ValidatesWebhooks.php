<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Concerns;

/**
 * Trait for validating webhook payloads.
 *
 * This trait provides methods for validating the structure and
 * content of webhook payloads.
 */
trait ValidatesWebhooks
{
    /**
     * Validate a webhook payload has required fields.
     *
     * @param  array<string, mixed>  $payload  The webhook payload.
     * @return bool
     */
    protected function isValidWebhookPayload(array $payload): bool
    {
        return isset($payload['event']) && is_string($payload['event']);
    }

    /**
     * Get the event type from a webhook payload.
     *
     * @param  array<string, mixed>  $payload  The webhook payload.
     * @return string|null
     */
    protected function getWebhookEvent(array $payload): ?string
    {
        return $payload['event'] ?? null;
    }

    /**
     * Get the transaction ID from a webhook payload.
     *
     * @param  array<string, mixed>  $payload  The webhook payload.
     * @return string|null
     */
    protected function getWebhookTransactionId(array $payload): ?string
    {
        return $payload['transaction_id'] ?? null;
    }

    /**
     * Get the status from a webhook payload.
     *
     * @param  array<string, mixed>  $payload  The webhook payload.
     * @return string|null
     */
    protected function getWebhookStatus(array $payload): ?string
    {
        return $payload['status'] ?? null;
    }
}
