<?php

declare(strict_types=1);

namespace OwnPay\Laravel\ValueObjects;

/**
 * Transaction status enumeration.
 *
 * Represents the possible states of a transaction in the OwnPay system.
 * These values match the database enum values in op_transactions.status.
 */
enum TransactionStatus: string
{
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
            self::Refunded,
        ], true);
    }

    /**
     * Check if the transaction was successful.
     */
    public function isSuccess(): bool
    {
        return $this === self::Completed;
    }

    /**
     * Check if the transaction is still active (checkout-eligible).
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Created,
        ], true);
    }

    /**
     * Check if the transaction can be refunded.
     */
    public function isRefundable(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Refunded, // Partial refund possible
        ], true);
    }

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Created => 'Created',
            self::AwaitingVerification => 'Awaiting Verification',
            self::PendingReview => 'Pending Review',
            self::Processing => 'Processing',
            self::CallbackProcessing => 'Callback Processing',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
            self::Refunded => 'Refunded',
        };
    }
    case Pending = 'pending';
    case Created = 'created';
    case AwaitingVerification = 'awaiting_verification';
    case PendingReview = 'pending_review';
    case Processing = 'processing';
    case CallbackProcessing = 'callback_processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Refunded = 'refunded';
}
