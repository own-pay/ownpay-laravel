<?php

declare(strict_types=1);

namespace OwnPay\Laravel\ValueObjects;

/**
 * Refund status enumeration.
 *
 * Represents the possible states of a refund in the OwnPay system.
 * These values match the database enum values in op_refunds.status.
 */
enum RefundStatus: string
{
    /**
     * Check if the status is a terminal state.
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
        ], true);
    }

    /**
     * Check if the refund was successful.
     */
    public function isSuccess(): bool
    {
        return $this === self::Completed;
    }

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
        };
    }
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
}
