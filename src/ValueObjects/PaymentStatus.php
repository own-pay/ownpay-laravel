<?php

declare(strict_types=1);

namespace OwnPay\Laravel\ValueObjects;

/**
 * Payment status enumeration.
 *
 * Represents the possible states of a payment intent in the OwnPay system.
 * These values match the database enum values in op_payment_intents.status.
 */
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    /**
     * Check if the status is a terminal state.
     *
     * Terminal states cannot transition to other states.
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
            self::Cancelled,
            self::Expired,
        ], true);
    }

    /**
     * Check if the payment was successful.
     */
    public function isSuccess(): bool
    {
        return $this === self::Completed;
    }

    /**
     * Check if the payment is still in progress.
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Processing,
        ], true);
    }

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }
}
