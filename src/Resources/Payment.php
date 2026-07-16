<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Resources;

use OwnPay\Laravel\Http\Response;
use OwnPay\Laravel\ValueObjects\PaymentStatus;

/**
 * Payment resource.
 *
 * Represents a payment intent from the OwnPay API.
 */
final readonly class Payment
{
    /**
     * Create a new Payment instance.
     *
     * @param  string  $paymentId  The payment UUID.
     * @param  string  $token  The payment token.
     * @param  string  $checkoutUrl  The checkout URL for the customer.
     * @param  PaymentStatus  $status  The payment status.
     * @param  string|null  $trxId  The transaction ID (if linked).
     * @param  string|null  $gatewayTrxId  The gateway transaction ID.
     * @param  string|null  $amount  The payment amount.
     * @param  string|null  $currency  The payment currency.
     * @param  string|null  $fee  The transaction fee.
     * @param  string|null  $gateway  The gateway slug.
     * @param  string|null  $method  The payment method.
     * @param  string|null  $reference  The merchant reference.
     * @param  string|null  $createdAt  The creation timestamp.
     * @param  string|null  $completedAt  The completion timestamp.
     * @param  array{name?: string, email?: string}|null  $customer  The customer info.
     */
    public function __construct(
        public string $paymentId,
        public string $token,
        public string $checkoutUrl,
        public PaymentStatus $status,
        public ?string $trxId = null,
        public ?string $gatewayTrxId = null,
        public ?string $amount = null,
        public ?string $currency = null,
        public ?string $fee = null,
        public ?string $gateway = null,
        public ?string $method = null,
        public ?string $reference = null,
        public ?string $createdAt = null,
        public ?string $completedAt = null,
        public ?array $customer = null,
    ) {
        //
    }

    /**
     * Create a Payment from an API response.
     *
     * @param  Response  $response  The API response.
     * @return static
     */
    public static function fromResponse(Response $response): static
    {
        $data = $response->getData() ?? [];

        // Handle create response (minimal fields)
        if (isset($data['payment_id'])) {
            return new static(
                paymentId: $data['payment_id'],
                token: $data['token'] ?? '',
                checkoutUrl: $data['checkout_url'] ?? '',
                status: PaymentStatus::from($data['status'] ?? 'pending'),
            );
        }

        // Handle full payment response
        return static::fromArray($data);
    }

    /**
     * Create a Payment from an array.
     *
     * @param  array<string, mixed>  $data  The payment data.
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            paymentId: $data['payment_id'] ?? $data['uuid'] ?? '',
            token: $data['token'] ?? '',
            checkoutUrl: $data['checkout_url'] ?? '',
            status: PaymentStatus::from($data['status'] ?? 'pending'),
            trxId: $data['trx_id'] ?? null,
            gatewayTrxId: $data['gateway_trx_id'] ?? null,
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            fee: $data['fee'] ?? null,
            gateway: $data['gateway'] ?? null,
            method: $data['method'] ?? null,
            reference: $data['reference'] ?? null,
            createdAt: $data['created_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            customer: $data['customer'] ?? null,
        );
    }

    /**
     * Check if the payment was successful.
     */
    public function isSuccess(): bool
    {
        return $this->status->isSuccess();
    }

    /**
     * Check if the payment is still active.
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if the payment is in a terminal state.
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
            'payment_id' => $this->paymentId,
            'token' => $this->token,
            'checkout_url' => $this->checkoutUrl,
            'status' => $this->status->value,
            'trx_id' => $this->trxId,
            'gateway_trx_id' => $this->gatewayTrxId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'fee' => $this->fee,
            'gateway' => $this->gateway,
            'method' => $this->method,
            'reference' => $this->reference,
            'created_at' => $this->createdAt,
            'completed_at' => $this->completedAt,
            'customer' => $this->customer,
        ], fn (mixed $value) => $value !== null);
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
