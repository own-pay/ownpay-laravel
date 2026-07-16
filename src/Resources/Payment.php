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
     */
    public static function fromResponse(Response $response): static
    {
        $data = $response->getData() ?? [];

        // Handle create response (minimal fields)
        if (isset($data['payment_id'])) {
            return self::fromArray($data);
        }

        // Handle full payment response
        return self::fromArray($data);
    }

    /**
     * Create a Payment from an array.
     *
     * @param  array<string, mixed>  $data  The payment data.
     */
    public static function fromArray(array $data): static
    {
        /** @var mixed $customerData */
        $customerData = $data['customer'] ?? null;
        /** @var array{name?: string, email?: string}|null $customer */
        $customer = null;
        if (is_array($customerData)) {
            $customer = [];
            if (isset($customerData['name']) && is_string($customerData['name'])) {
                $customer['name'] = $customerData['name'];
            }
            if (isset($customerData['email']) && is_string($customerData['email'])) {
                $customer['email'] = $customerData['email'];
            }
        }

        /** @var mixed $paymentId */
        $paymentId = $data['payment_id'] ?? $data['uuid'] ?? '';
        /** @var mixed $token */
        $token = $data['token'] ?? '';
        /** @var mixed $checkoutUrl */
        $checkoutUrl = $data['checkout_url'] ?? '';
        /** @var mixed $status */
        $status = $data['status'] ?? 'pending';
        /** @var mixed $trxId */
        $trxId = $data['trx_id'] ?? null;
        /** @var mixed $gatewayTrxId */
        $gatewayTrxId = $data['gateway_trx_id'] ?? null;
        /** @var mixed $amount */
        $amount = $data['amount'] ?? null;
        /** @var mixed $currency */
        $currency = $data['currency'] ?? null;
        /** @var mixed $fee */
        $fee = $data['fee'] ?? null;
        /** @var mixed $gateway */
        $gateway = $data['gateway'] ?? null;
        /** @var mixed $method */
        $method = $data['method'] ?? null;
        /** @var mixed $reference */
        $reference = $data['reference'] ?? null;
        /** @var mixed $createdAt */
        $createdAt = $data['created_at'] ?? null;
        /** @var mixed $completedAt */
        $completedAt = $data['completed_at'] ?? null;

        return new static(
            paymentId: is_scalar($paymentId) ? (string) $paymentId : '',
            token: is_scalar($token) ? (string) $token : '',
            checkoutUrl: is_scalar($checkoutUrl) ? (string) $checkoutUrl : '',
            status: PaymentStatus::tryFrom(is_scalar($status) ? (string) $status : 'pending') ?? PaymentStatus::Pending,
            trxId: is_scalar($trxId) ? (string) $trxId : null,
            gatewayTrxId: is_scalar($gatewayTrxId) ? (string) $gatewayTrxId : null,
            amount: is_scalar($amount) ? (string) $amount : null,
            currency: is_scalar($currency) ? (string) $currency : null,
            fee: is_scalar($fee) ? (string) $fee : null,
            gateway: is_scalar($gateway) ? (string) $gateway : null,
            method: is_scalar($method) ? (string) $method : null,
            reference: is_scalar($reference) ? (string) $reference : null,
            createdAt: is_scalar($createdAt) ? (string) $createdAt : null,
            completedAt: is_scalar($completedAt) ? (string) $completedAt : null,
            customer: $customer,
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
