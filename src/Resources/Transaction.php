<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Resources;

use OwnPay\Laravel\Http\Response;
use OwnPay\Laravel\ValueObjects\TransactionStatus;

/**
 * Transaction resource.
 *
 * Represents a transaction from the OwnPay API.
 */
final readonly class Transaction
{
    /**
     * Create a new Transaction instance.
     *
     * @param  int  $id  The transaction ID.
     * @param  string  $trxId  The OwnPay transaction ID (OP-XXXXX).
     * @param  string|null  $gatewayTrxId  The gateway transaction ID.
     * @param  string  $amount  The transaction amount.
     * @param  string  $currency  The transaction currency.
     * @param  string  $fee  The transaction fee.
     * @param  string|null  $netAmount  The net amount after fees.
     * @param  TransactionStatus  $status  The transaction status.
     * @param  string|null  $gateway  The gateway slug.
     * @param  string|null  $method  The payment method.
     * @param  string|null  $reference  The merchant reference.
     * @param  string  $createdAt  The creation timestamp.
     * @param  string|null  $updatedAt  The last update timestamp.
     */
    public function __construct(
        public int $id,
        public string $trxId,
        public ?string $gatewayTrxId,
        public string $amount,
        public string $currency,
        public string $fee,
        public ?string $netAmount,
        public TransactionStatus $status,
        public ?string $gateway,
        public ?string $method,
        public ?string $reference,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
        //
    }

    /**
     * Create a Transaction from an API response.
     *
     * @param  Response  $response  The API response.
     */
    public static function fromResponse(Response $response): static
    {
        $data = $response->getData() ?? [];

        return self::fromArray($data);
    }

    /**
     * Create a Transaction from an array.
     *
     * @param  array<string, mixed>  $data  The transaction data.
     */
    public static function fromArray(array $data): static
    {
        /** @var mixed $idValue */
        $idValue = $data['id'] ?? 0;
        /** @var mixed $trxId */
        $trxId = $data['trx_id'] ?? '';
        /** @var mixed $gatewayTrxId */
        $gatewayTrxId = $data['gateway_trx_id'] ?? null;
        /** @var mixed $amount */
        $amount = $data['amount'] ?? '0.00';
        /** @var mixed $currency */
        $currency = $data['currency'] ?? '';
        /** @var mixed $fee */
        $fee = $data['fee'] ?? '0.00';
        /** @var mixed $netAmount */
        $netAmount = $data['net_amount'] ?? null;
        /** @var mixed $status */
        $status = $data['status'] ?? 'pending';
        /** @var mixed $gateway */
        $gateway = $data['gateway'] ?? null;
        /** @var mixed $method */
        $method = $data['method'] ?? null;
        /** @var mixed $reference */
        $reference = $data['reference'] ?? null;
        /** @var mixed $createdAt */
        $createdAt = $data['created_at'] ?? '';
        /** @var mixed $updatedAt */
        $updatedAt = $data['updated_at'] ?? null;

        return new static(
            id: is_numeric($idValue) ? (int) $idValue : 0,
            trxId: is_scalar($trxId) ? (string) $trxId : '',
            gatewayTrxId: is_scalar($gatewayTrxId) ? (string) $gatewayTrxId : null,
            amount: is_scalar($amount) ? (string) $amount : '0.00',
            currency: is_scalar($currency) ? (string) $currency : '',
            fee: is_scalar($fee) ? (string) $fee : '0.00',
            netAmount: is_scalar($netAmount) ? (string) $netAmount : null,
            status: TransactionStatus::from(is_scalar($status) ? (string) $status : 'pending'),
            gateway: is_scalar($gateway) ? (string) $gateway : null,
            method: is_scalar($method) ? (string) $method : null,
            reference: is_scalar($reference) ? (string) $reference : null,
            createdAt: is_scalar($createdAt) ? (string) $createdAt : '',
            updatedAt: is_scalar($updatedAt) ? (string) $updatedAt : null,
        );
    }

    /**
     * Check if the transaction was successful.
     */
    public function isSuccess(): bool
    {
        return $this->status->isSuccess();
    }

    /**
     * Check if the transaction is still active.
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if the transaction is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * Check if the transaction can be refunded.
     */
    public function isRefundable(): bool
    {
        return $this->status->isRefundable();
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trx_id' => $this->trxId,
            'gateway_trx_id' => $this->gatewayTrxId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'fee' => $this->fee,
            'net_amount' => $this->netAmount,
            'status' => $this->status->value,
            'gateway' => $this->gateway,
            'method' => $this->method,
            'reference' => $this->reference,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
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
