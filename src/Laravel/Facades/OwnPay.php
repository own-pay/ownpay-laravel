<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use OwnPay\Laravel\Client\OwnPayClient;

/**
 * @method static array health()
 * @method static \OwnPay\Laravel\Resources\Payment createPayment(array $data)
 * @method static \OwnPay\Laravel\Resources\Payment getPayment(string $paymentId)
 * @method static array{data: \OwnPay\Laravel\Resources\Transaction[], meta: array} listTransactions(array $params = [])
 * @method static \OwnPay\Laravel\Resources\Transaction getTransaction(string $trxId)
 * @method static \OwnPay\Laravel\Resources\Refund createRefund(array $data)
 * @method static array{data: \OwnPay\Laravel\Resources\Refund[], meta: array} listRefunds(array $params = [])
 * @method static \OwnPay\Laravel\Resources\Refund getRefund(string $trxId)
 * @method static \OwnPay\Laravel\Resources\Customer createCustomer(array $data)
 * @method static array{data: \OwnPay\Laravel\Resources\Customer[], meta: array} listCustomers(array $params = [])
 * @method static \OwnPay\Laravel\Resources\Customer getCustomer(string $identifier)
 * @method static array testWebhook()
 * @method static array listWebhookDeliveries()
 * @method static array listApiKeys()
 * @method static array generateApiKey(array $params = [])
 * @method static array revokeApiKey(int $keyId)
 *
 * @see \OwnPay\Laravel\Client\OwnPayClient
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
