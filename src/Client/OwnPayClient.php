<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Client;

use OwnPay\Laravel\Http\HttpClient;
use OwnPay\Laravel\Http\Response;
use OwnPay\Laravel\Resources\Customer;
use OwnPay\Laravel\Resources\Payment;
use OwnPay\Laravel\Resources\Refund;
use OwnPay\Laravel\Resources\Transaction;

/**
 * OwnPay API client.
 *
 * This is the main entry point for interacting with the OwnPay API.
 * It provides methods for all API operations including payments,
 * transactions, refunds, customers, and webhooks.
 */
final class OwnPayClient
{
    /**
     * Create a new OwnPayClient.
     *
     * @param  HttpClient  $http  The HTTP client for making API requests.
     */
    public function __construct(
        private readonly HttpClient $http,
    ) {
        //
    }

    // =========================================================================
    // Health Check
    // =========================================================================

    /**
     * Check the health of the OwnPay API.
     *
     * @return array{status: string, version: string, db: string, mobile: array, gateways: int, customers: int, time: string}
     */
    public function health(): array
    {
        $response = $this->http->get('/api/v1/health');

        return $response->getData() ?? [];
    }

    // =========================================================================
    // Payments
    // =========================================================================

    /**
     * Create a new payment intent.
     *
     * @param  array{
     *     amount: string|float,
     *     currency: string,
     *     description?: string,
     *     callback_url?: string,
     *     redirect_url?: string,
     *     cancel_url?: string,
     *     customer_name?: string,
     *     customer_mail?: string,
     *     customer_email?: string,
     *     customer_phone?: string,
     *     reference?: string,
     *     gateway?: string,
     *     metadata?: array<string, mixed>,
     * }  $data  The payment data.
     * @return Payment
     */
    public function createPayment(array $data): Payment
    {
        $response = $this->http->post('/api/v1/payments', $data);

        return Payment::fromResponse($response);
    }

    /**
     * Get a payment by ID.
     *
     * @param  string  $paymentId  The payment UUID.
     * @return Payment
     */
    public function getPayment(string $paymentId): Payment
    {
        $response = $this->http->get("/api/v1/payments/{$paymentId}");

        return Payment::fromResponse($response);
    }

    // =========================================================================
    // Transactions
    // =========================================================================

    /**
     * List transactions with optional filters.
     *
     * @param  array{
     *     page?: int,
     *     per_page?: int,
     *     status?: string,
     *     gateway?: string,
     *     from?: string,
     *     to?: string,
     * }  $params  Optional filters and pagination.
     * @return array{data: Transaction[], meta: array}
     */
    public function listTransactions(array $params = []): array
    {
        $response = $this->http->get('/api/v1/transactions', $params);

        $transactions = array_map(
            fn (array $item) => Transaction::fromArray($item),
            $response->getData() ?? [],
        );

        return [
            'data' => $transactions,
            'meta' => $response->getMeta() ?? [],
        ];
    }

    /**
     * Get a transaction by ID or gateway transaction ID.
     *
     * @param  string  $trxId  The transaction ID (OP-XXXXX) or gateway transaction ID.
     * @return Transaction
     */
    public function getTransaction(string $trxId): Transaction
    {
        $response = $this->http->get("/api/v1/transactions/{$trxId}");

        return Transaction::fromResponse($response);
    }

    // =========================================================================
    // Refunds
    // =========================================================================

    /**
     * Create a refund for a transaction.
     *
     * @param  array{
     *     trx_id?: string,
     *     transaction_id?: string|int,
     *     amount?: string|float,
     *     reason?: string,
     * }  $data  The refund data. Either trx_id or transaction_id is required.
     * @return Refund
     */
    public function createRefund(array $data): Refund
    {
        $response = $this->http->post('/api/v1/refunds', $data);

        return Refund::fromResponse($response);
    }

    /**
     * List refunds with optional filters.
     *
     * @param  array{
     *     page?: int,
     *     per_page?: int,
     *     status?: string,
     *     trx_id?: string,
     *     transaction_id?: string|int,
     *     from?: string,
     *     to?: string,
     * }  $params  Optional filters and pagination.
     * @return array{data: Refund[], meta: array}
     */
    public function listRefunds(array $params = []): array
    {
        $response = $this->http->get('/api/v1/refunds', $params);

        $refunds = array_map(
            fn (array $item) => Refund::fromArray($item),
            $response->getData() ?? [],
        );

        return [
            'data' => $refunds,
            'meta' => $response->getMeta() ?? [],
        ];
    }

    /**
     * Get a refund by transaction ID.
     *
     * @param  string  $trxId  The transaction ID.
     * @return Refund
     */
    public function getRefund(string $trxId): Refund
    {
        $response = $this->http->get("/api/v1/refunds/{$trxId}");

        return Refund::fromResponse($response);
    }

    // =========================================================================
    // Customers
    // =========================================================================

    /**
     * Create a new customer.
     *
     * @param  array{
     *     name: string,
     *     email?: string,
     *     phone?: string,
     * }  $data  The customer data.
     * @return Customer
     */
    public function createCustomer(array $data): Customer
    {
        $response = $this->http->post('/api/v1/customers', $data);

        return Customer::fromResponse($response);
    }

    /**
     * List customers with pagination.
     *
     * @param  array{
     *     page?: int,
     *     per_page?: int,
     * }  $params  Optional pagination.
     * @return array{data: Customer[], meta: array}
     */
    public function listCustomers(array $params = []): array
    {
        $response = $this->http->get('/api/v1/customers', $params);

        $customers = array_map(
            fn (array $item) => Customer::fromArray($item),
            $response->getData() ?? [],
        );

        return [
            'data' => $customers,
            'meta' => $response->getMeta() ?? [],
        ];
    }

    /**
     * Get a customer by identifier (email or phone).
     *
     * @param  string  $identifier  The customer email or phone.
     * @return Customer
     */
    public function getCustomer(string $identifier): Customer
    {
        $response = $this->http->get("/api/v1/customers/{$identifier}");

        return Customer::fromResponse($response);
    }

    // =========================================================================
    // Webhooks
    // =========================================================================

    /**
     * Test the webhook endpoint.
     *
     * @return array{status_code: int|null, response_time_ms: int|null}
     */
    public function testWebhook(): array
    {
        $response = $this->http->post('/api/v1/webhooks/tests');

        return $response->getData() ?? [];
    }

    /**
     * List recent webhook deliveries.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listWebhookDeliveries(): array
    {
        $response = $this->http->get('/api/v1/webhooks/deliveries');

        return $response->getData() ?? [];
    }

    // =========================================================================
    // API Keys
    // =========================================================================

    /**
     * List API keys for the merchant.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listApiKeys(): array
    {
        $response = $this->http->get('/api/v1/api-keys');

        return $response->getData() ?? [];
    }

    /**
     * Generate a new API key.
     *
     * @param  array{
     *     name?: string,
     *     scopes?: string[],
     * }  $params  Optional key configuration.
     * @return array{key: string, prefix: string, warning: string}
     */
    public function generateApiKey(array $params = []): array
    {
        $response = $this->http->post('/api/v1/api-keys', $params);

        return $response->getData() ?? [];
    }

    /**
     * Revoke an API key.
     *
     * @param  int  $keyId  The API key ID.
     * @return array{message: string}
     */
    public function revokeApiKey(int $keyId): array
    {
        $response = $this->http->delete("/api/v1/api-keys/{$keyId}");

        return $response->getData() ?? [];
    }
}
