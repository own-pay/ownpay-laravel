<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Resources;

use OwnPay\Laravel\Http\Response;
use OwnPay\Laravel\ValueObjects\RefundStatus;

/**
 * Refund resource.
 *
 * Represents a refund from the OwnPay API.
 */
final readonly class Refund
{
    /**
     * Create a new Refund instance.
     *
     * @param  int|null  $id  The refund ID.
     * @param  string|null  $uuid  The refund UUID.
     * @param  int|null  $transactionId  The transaction ID.
     * @param  string|null  $trxId  The OwnPay transaction ID.
     * @param  string|null  $gatewayTrxId  The gateway transaction ID.
     * @param  string|null  $amount  The refund amount.
     * @param  string|null  $reason  The refund reason.
     * @param  RefundStatus  $status  The refund status.
     * @param  string|null  $processedAt  The processing timestamp.
     * @param  string|null  $createdAt  The creation timestamp.
     */
    public function __construct(
        public ?int $id,
        public ?string $uuid,
        public ?int $transactionId,
        public ?string $trxId,
        public ?string $gatewayTrxId,
        public ?string $amount,
        public ?string $reason,
        public RefundStatus $status,
        public ?string $processedAt,
        public ?string $createdAt,
    ) {
        //
    }

    /**
     * Create a Refund from an API response.
     *
     * @param  Response  $response  The API response.
     */
    public static function fromResponse(Response $response): static
    {
        $data = $response->getData() ?? [];

        return self::fromArray($data);
    }

    /**
     * Create a Refund from an array.
     *
     * @param  array<string, mixed>  $data  The refund data.
     */
    public static function fromArray(array $data): static
    {
        /** @var mixed $idValue */
        $idValue = $data['id'] ?? null;
        /** @var mixed $uuid */
        $uuid = $data['uuid'] ?? null;
        /** @var mixed $txnIdValue */
        $txnIdValue = $data['transaction_id'] ?? null;
        /** @var mixed $trxId */
        $trxId = $data['trx_id'] ?? null;
        /** @var mixed $gatewayTrxId */
        $gatewayTrxId = $data['gateway_trx_id'] ?? null;
        /** @var mixed $amount */
        $amount = $data['amount'] ?? null;
        /** @var mixed $reason */
        $reason = $data['reason'] ?? null;
        /** @var mixed $status */
        $status = $data['status'] ?? 'pending';
        /** @var mixed $processedAt */
        $processedAt = $data['processed_at'] ?? null;
        /** @var mixed $createdAt */
        $createdAt = $data['created_at'] ?? null;

        return new static(
            id: is_numeric($idValue) ? (int) $idValue : null,
            uuid: is_scalar($uuid) ? (string) $uuid : null,
            transactionId: is_numeric($txnIdValue) ? (int) $txnIdValue : null,
            trxId: is_scalar($trxId) ? (string) $trxId : null,
            gatewayTrxId: is_scalar($gatewayTrxId) ? (string) $gatewayTrxId : null,
            amount: is_scalar($amount) ? (string) $amount : null,
            reason: is_scalar($reason) ? (string) $reason : null,
            status: RefundStatus::from(is_scalar($status) ? (string) $status : 'pending'),
            processedAt: is_scalar($processedAt) ? (string) $processedAt : null,
            createdAt: is_scalar($createdAt) ? (string) $createdAt : null,
        );
    }

    /**
     * Check if the refund was successful.
     */
    public function isSuccess(): bool
    {
        return $this->status->isSuccess();
    }

    /**
     * Check if the refund is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'uuid' => $this->uuid,
            'transaction_id' => $this->transactionId,
            'trx_id' => $this->trxId,
            'gateway_trx_id' => $this->gatewayTrxId,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'status' => $this->status->value,
            'processed_at' => $this->processedAt,
            'created_at' => $this->createdAt,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /**
     * Get the JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
