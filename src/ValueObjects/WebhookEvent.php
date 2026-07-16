<?php

declare(strict_types=1);

namespace OwnPay\Laravel\ValueObjects;

/**
 * Webhook event type enumeration.
 *
 * Represents the possible webhook event types dispatched by OwnPay.
 * These values match the event names used in WebhookDispatcher.
 */
enum WebhookEvent: string
{
    case PaymentCompleted = 'payment.completed';
    case PaymentFailed = 'payment.failed';
    case PaymentCancelled = 'payment.canceled';
    case RefundCompleted = 'refund.completed';
    case DisputeCreated = 'dispute.created';

    /**
     * Get a human-readable label for the event.
     */
    public function label(): string
    {
        return match ($this) {
            self::PaymentCompleted => 'Payment Completed',
            self::PaymentFailed => 'Payment Failed',
            self::PaymentCancelled => 'Payment Cancelled',
            self::RefundCompleted => 'Refund Completed',
            self::DisputeCreated => 'Dispute Created',
        };
    }

    /**
     * Check if this is a payment-related event.
     */
    public function isPaymentEvent(): bool
    {
        return in_array($this, [
            self::PaymentCompleted,
            self::PaymentFailed,
            self::PaymentCancelled,
        ], true);
    }

    /**
     * Check if this is a refund-related event.
     */
    public function isRefundEvent(): bool
    {
        return $this === self::RefundCompleted;
    }

    /**
     * Check if this is a dispute-related event.
     */
    public function isDisputeEvent(): bool
    {
        return $this === self::DisputeCreated;
    }
}
