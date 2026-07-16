<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Events;

/**
 * Event dispatched when a webhook is received from OwnPay.
 *
 * Listeners can subscribe to this event to process webhook
 * payloads in their application.
 */
class WebhookReceived
{
    /**
     * Create a new event instance.
     *
     * @param  string  $event  The webhook event type (e.g., 'payment.completed').
     * @param  array<string, mixed>  $payload  The full webhook payload.
     */
    public function __construct(
        public readonly string $event,
        public readonly array $payload,
    ) {
        //
    }

    /**
     * Get the transaction ID from the payload.
     */
    public function getTransactionId(): ?string
    {
        return $this->payload['transaction_id'] ?? null;
    }

    /**
     * Get the gateway transaction ID from the payload.
     */
    public function getGatewayTransactionId(): ?string
    {
        return $this->payload['gateway_trx_id'] ?? null;
    }

    /**
     * Get the payment status from the payload.
     */
    public function getStatus(): ?string
    {
        return $this->payload['status'] ?? null;
    }

    /**
     * Get the amount from the payload.
     */
    public function getAmount(): ?string
    {
        return $this->payload['amount'] ?? null;
    }

    /**
     * Get the currency from the payload.
     */
    public function getCurrency(): ?string
    {
        return $this->payload['currency'] ?? null;
    }

    /**
     * Get the gateway from the payload.
     */
    public function getGateway(): ?string
    {
        return $this->payload['gateway'] ?? null;
    }

    /**
     * Get the customer info from the payload.
     */
    public function getCustomer(): ?array
    {
        return $this->payload['customer'] ?? null;
    }

    /**
     * Get the metadata from the payload.
     */
    public function getMetadata(): ?array
    {
        return $this->payload['metadata'] ?? null;
    }

    /**
     * Check if this is a payment completed event.
     */
    public function isPaymentCompleted(): bool
    {
        return $this->event === 'payment.completed';
    }

    /**
     * Check if this is a payment failed event.
     */
    public function isPaymentFailed(): bool
    {
        return $this->event === 'payment.failed';
    }

    /**
     * Check if this is a refund completed event.
     */
    public function isRefundCompleted(): bool
    {
        return $this->event === 'refund.completed';
    }
}
