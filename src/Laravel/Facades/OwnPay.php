<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use OwnPay\Laravel\Client\OwnPayClient;

/**
 * @method static array{status: string, version: string, db: string, mobile: array{connected: bool, active_devices: int}, gateways: int, customers: int, time: string} health()
 * @method static \OwnPay\Laravel\Resources\Payment createPayment(array{amount: string|float, currency: string, description?: string, callback_url?: string, redirect_url?: string, cancel_url?: string, customer_name?: string, customer_mail?: string, customer_email?: string, customer_phone?: string, reference?: string, gateway?: string, metadata?: array<string, mixed>} $data)
 * @method static \OwnPay\Laravel\Resources\Payment getPayment(string $paymentId)
 * @method static array{data: list<\OwnPay\Laravel\Resources\Transaction>, meta: array<string, mixed>} listTransactions(array{page?: int, per_page?: int, status?: string, gateway?: string, from?: string, to?: string} $params = [])
 * @method static \OwnPay\Laravel\Resources\Transaction getTransaction(string $trxId)
 * @method static \OwnPay\Laravel\Resources\Refund createRefund(array{trx_id?: string, transaction_id?: string|int, amount?: string|float, reason?: string} $data)
 * @method static array{data: list<\OwnPay\Laravel\Resources\Refund>, meta: array<string, mixed>} listRefunds(array{page?: int, per_page?: int, status?: string, trx_id?: string, transaction_id?: string|int, from?: string, to?: string} $params = [])
 * @method static \OwnPay\Laravel\Resources\Refund getRefund(string $trxId)
 * @method static \OwnPay\Laravel\Resources\Customer createCustomer(array{name: string, email?: string, phone?: string} $data)
 * @method static array{data: list<\OwnPay\Laravel\Resources\Customer>, meta: array<string, mixed>} listCustomers(array{page?: int, per_page?: int} $params = [])
 * @method static \OwnPay\Laravel\Resources\Customer getCustomer(string $identifier)
 * @method static array{status_code: int|null, response_time_ms: int|null} testWebhook()
 * @method static list<array<string, mixed>> listWebhookDeliveries()
 * @method static list<array<string, mixed>> listApiKeys()
 * @method static array{key: string, prefix: string, warning: string} generateApiKey(array{name?: string, scopes?: list<string>} $params = [])
 * @method static array{message: string} revokeApiKey(int $keyId)
 *
 * @see OwnPayClient
 */
class OwnPay extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return OwnPayClient::class;
    }
}
