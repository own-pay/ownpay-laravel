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
        /** @var mixed $value */
        $value = $this->payload['transaction_id'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the gateway transaction ID from the payload.
     */
    public function getGatewayTransactionId(): ?string
    {
        /** @var mixed $value */
        $value = $this->payload['gateway_trx_id'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the payment status from the payload.
     */
    public function getStatus(): ?string
    {
        /** @var mixed $value */
        $value = $this->payload['status'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the amount from the payload.
     */
    public function getAmount(): ?string
    {
        /** @var mixed $value */
        $value = $this->payload['amount'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the currency from the payload.
     */
    public function getCurrency(): ?string
    {
        /** @var mixed $value */
        $value = $this->payload['currency'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the gateway from the payload.
     */
    public function getGateway(): ?string
    {
        /** @var mixed $value */
        $value = $this->payload['gateway'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the customer info from the payload.
     *
     * @return array{name: string, email: string, phone: string}|null
     */
    public function getCustomer(): ?array
    {
        /** @var mixed $customer */
        $customer = $this->payload['customer'] ?? null;

        if (! is_array($customer)) {
            return null;
        }

        /** @var array{name: string, email: string, phone: string} $customer */
        return $customer;
    }

    /**
     * Get the metadata from the payload.
     *
     * @return array<string, mixed>|null
     */
    public function getMetadata(): ?array
    {
        /** @var mixed $metadata */
        $metadata = $this->payload['metadata'] ?? null;

        if (! is_array($metadata)) {
            return null;
        }

        /** @var array<string, mixed> $metadata */
        return $metadata;
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
