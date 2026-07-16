# OwnPay Laravel SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ownpay/ownpay-laravel.svg?style=flat-square)](https://packagist.org/packages/ownpay/ownpay-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/ownpay/ownpay-laravel.svg?style=flat-square)](https://packagist.org/packages/ownpay/ownpay-laravel)
[![License](https://img.shields.io/packagist/l/ownpay/ownpay-laravel.svg?style=flat-square)](https://github.com/own-pay/ownpay-laravel/blob/main/LICENSE) 

Official Laravel SDK for the [OwnPay](https://ownpay.org) payment gateway platform. This package provides a clean, fluent interface for integrating OwnPay payments into your Laravel application.

## Features

- 🔐 **Secure Authentication** - Bearer token authentication with SHA-256 hashing
- 💳 **Payment Management** - Create, retrieve, and manage payment intents
- 🔄 **Transaction Tracking** - Query and filter transactions with pagination
- 💰 **Refund Processing** - Create and track refunds
- 👥 **Customer Management** - Create and manage customer profiles
- 🔔 **Webhook Handling** - HMAC-SHA256 signature verification
- 🛡️ **Error Handling** - Comprehensive exception hierarchy
- 📊 **Type Safety** - PHP 8.3+ enums, readonly classes, and value objects
- 🧪 **Testing Ready** - Mock-friendly HTTP client integration
- 📝 **PSR Compliant** - PSR-4, PSR-12, and PSR-18 standards

## Requirements

- PHP 8.3+
- Laravel 11.x, 12.x, or 13.x

## Installation

Install the package via Composer:

```bash
composer require ownpay/ownpay-laravel
```

The package will automatically register its service provider and facade.

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=ownpay-config
```

This will create `config/ownpay.php` in your application.

### Publish Migrations (Optional)

If you want to log webhooks to a database table:

```bash
php artisan vendor:publish --tag=ownpay-migrations
php artisan migrate
```

## Configuration

Add the following environment variables to your `.env` file:

```env
OWNPAY_API_KEY=op_your_api_key_here
OWNPAY_WEBHOOK_SECRET=your_webhook_secret_here
OWNPAY_BASE_URL=https://pay.ownpay.org
OWNPAY_TIMEOUT=30
OWNPAY_RETRY_ATTEMPTS=3
OWNPAY_RETRY_DELAY=100
OWNPAY_VERIFY_SSL=true
```

### Configuration Options

| Option | Default | Description |
|--------|---------|-------------|
| `api_key` | `null` | Your OwnPay API key (starts with `op_`) |
| `webhook_secret` | `null` | Webhook signing secret for verification |
| `base_url` | `https://pay.ownpay.org` | Your OwnPay instance URL |
| `timeout` | `30` | Request timeout in seconds |
| `retry_attempts` | `3` | Number of retry attempts for failed requests |
| `retry_delay` | `100` | Base delay in milliseconds between retries |
| `verify_ssl` | `true` | Whether to verify SSL certificates |
| `log_channel` | `null` | Log channel for SDK logging |
| `cache_ttl` | `0` | Cache TTL in seconds (0 to disable) |

## Usage

### Using the Facade

```php
use OwnPay\Laravel\Facades\OwnPay;

// Create a payment
$payment = OwnPay::createPayment([
    'amount' => '1250.00',
    'currency' => 'BDT',
    'description' => 'Premium Subscription',
    'redirect_url' => 'https://example.com/success',
    'cancel_url' => 'https://example.com/cancel',
    'callback_url' => 'https://example.com/webhook',
    'customer_name' => 'John Doe',
    'customer_mail' => 'john@example.com',
]);

// Redirect customer to checkout
return redirect($payment->checkoutUrl);

// Get payment status
$payment = OwnPay::getPayment($payment->paymentId);
echo $payment->status->label(); // "Pending", "Completed", etc.

// List transactions
$result = OwnPay::listTransactions([
    'page' => 1,
    'per_page' => 25,
    'status' => 'completed',
]);

foreach ($result['data'] as $transaction) {
    echo $transaction->trxId; // "OP-XXXXX"
    echo $transaction->amount;
}

// Create a refund
$refund = OwnPay::createRefund([
    'trx_id' => 'OP-XXXXX',
    'amount' => '500.00',
    'reason' => 'Customer request',
]);

// Create a customer
$customer = OwnPay::createCustomer([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+8801700000000',
]);

// Test webhook endpoint
$result = OwnPay::testWebhook();
```

### Using Dependency Injection

```php
use OwnPay\Laravel\Client\OwnPayClient;

class PaymentController extends Controller
{
    public function __construct(
        private readonly OwnPayClient $ownpay,
    ) {}

    public function store(Request $request)
    {
        $payment = $this->ownpay->createPayment([
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
            'callback_url' => route('webhook.ownpay'),
        ]);

        return response()->json([
            'checkout_url' => $payment->checkoutUrl,
            'payment_id' => $payment->paymentId,
        ]);
    }
}
```

### Payment Flow

```php
use OwnPay\Laravel\Facades\OwnPay;
use OwnPay\Laravel\Exception\OwnPayExceptionInterface;

class CheckoutController extends Controller
{
    public function initiate(Request $request)
    {
        try {
            $payment = OwnPay::createPayment([
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency'),
                'description' => $request->input('description'),
                'redirect_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
                'callback_url' => route('webhook.ownpay'),
                'customer_name' => $request->input('customer_name'),
                'customer_mail' => $request->input('customer_email'),
                'metadata' => [
                    'order_id' => $request->input('order_id'),
                ],
            ]);

            // Store payment_id in your database
            // Redirect to checkout
            return redirect($payment->checkoutUrl);

        } catch (OwnPayExceptionInterface $e) {
            return back()->withErrors([
                'payment' => $e->getMessage(),
            ]);
        }
    }

    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = OwnPay::getPayment($paymentId);

        if ($payment->isSuccess()) {
            // Payment completed successfully
            return view('payment.success', ['payment' => $payment]);
        }

        return view('payment.pending', ['payment' => $payment]);
    }
}
```

### Webhook Handling

#### Setup Webhook Route

The package automatically registers a webhook route at `/webhooks/ownpay`. You can customize this in your routes:

```php
use OwnPay\Laravel\Laravel\Middleware\VerifyWebhookSignature;

Route::post('/webhooks/ownpay', [WebhookController::class, 'handle'])
    ->middleware(VerifyWebhookSignature::class);
```

#### Listen for Webhook Events

Create an event listener in your `EventServiceProvider`:

```php
protected $listen = [
    \OwnPay\Laravel\Laravel\Events\WebhookReceived::class => [
        \App\Listeners\HandleOwnPayWebhook::class,
    ],
];
```

#### Example Listener

```php
namespace App\Listeners;

use OwnPay\Laravel\Laravel\Events\WebhookReceived;

class HandleOwnPayWebhook
{
    public function handle(WebhookReceived $event): void
    {
        match ($event->event) {
            'payment.completed' => $this->handlePaymentCompleted($event),
            'payment.failed' => $this->handlePaymentFailed($event),
            'refund.completed' => $this->handleRefundCompleted($event),
            default => null,
        };
    }

    private function handlePaymentCompleted(WebhookReceived $event): void
    {
        $transactionId = $event->getTransactionId();
        $amount = $event->getAmount();
        $currency = $event->getCurrency();

        // Update your database
        // Send confirmation email
        // etc.
    }

    private function handlePaymentFailed(WebhookReceived $event): void
    {
        // Handle failed payment
    }

    private function handleRefundCompleted(WebhookReceived $event): void
    {
        // Handle completed refund
    }
}
```

### Transaction Management

```php
use OwnPay\Laravel\Facades\OwnPay;

// List all transactions
$transactions = OwnPay::listTransactions();

// List with filters
$transactions = OwnPay::listTransactions([
    'status' => 'completed',
    'gateway' => 'bkash',
    'from' => '2024-01-01',
    'to' => '2024-12-31',
    'page' => 1,
    'per_page' => 50,
]);

// Get specific transaction
$transaction = OwnPay::getTransaction('OP-XXXXX');

// Check if refundable
if ($transaction->isRefundable()) {
    // Can create refund
}
```

### Customer Management

```php
use OwnPay\Laravel\Facades\OwnPay;

// Create customer
$customer = OwnPay::createCustomer([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+8801700000000',
]);

// List customers
$customers = OwnPay::listCustomers(['page' => 1]);

// Get customer by email or phone
$customer = OwnPay::getCustomer('john@example.com');
```

### API Key Management

```php
use OwnPay\Laravel\Facades\OwnPay;

// List API keys
$keys = OwnPay::listApiKeys();

// Generate new key
$result = OwnPay::generateApiKey([
    'name' => 'Production Key',
    'scopes' => ['read', 'write'],
]);

echo $result['key']; // Show this to the user ONCE
echo $result['prefix'];

// Revoke key
OwnPay::revokeApiKey($keyId);
```

## Error Handling

The package provides a comprehensive exception hierarchy:

```php
use OwnPay\Laravel\Exception\OwnPayExceptionInterface;
use OwnPay\Laravel\Exception\AuthenticationException;
use OwnPay\Laravel\Exception\InvalidRequestException;
use OwnPay\Laravel\Exception\NotFoundException;
use OwnPay\Laravel\Exception\RateLimitException;
use OwnPay\Laravel\Exception\ConnectionException;
use OwnPay\Laravel\Exception\PaymentFailedException;

try {
    $payment = OwnPay::createPayment([...]);
} catch (AuthenticationException $e) {
    // Invalid API key or insufficient permissions
    Log::error('OwnPay auth error: ' . $e->getMessage());
} catch (InvalidRequestException $e) {
    // Validation error
    $errors = $e->getErrorDetails();
    // $errors is an array of {code, message, field}
} catch (NotFoundException $e) {
    // Resource not found
} catch (RateLimitException $e) {
    // Rate limit exceeded
    $retryAfter = $e->getRetryAfter();
} catch (ConnectionException $e) {
    // Network error
} catch (OwnPayExceptionInterface $e) {
    // Catch all OwnPay exceptions
}
```

## Value Objects

The package uses type-safe value objects:

### Money

```php
use OwnPay\Laravel\ValueObjects\Money;

$money = new Money('100.00', 'USD');
$money->amount; // "100.00"
$money->currency; // "USD"
$money->toFloat(); // 100.0
$money->toCents(); // 10000
$money->format(); // "USD 100.00"

// Arithmetic
$a = new Money('100.00', 'USD');
$b = new Money('50.00', 'USD');
$sum = $a->add($b); // Money('150.00', 'USD')
$diff = $a->subtract($b); // Money('50.00', 'USD')

// Comparison
$a->isGreaterThan($b); // true
$a->equals(new Money('100.00', 'USD')); // true
```

### Status Enums

```php
use OwnPay\Laravel\ValueObjects\PaymentStatus;
use OwnPay\Laravel\ValueObjects\TransactionStatus;
use OwnPay\Laravel\ValueObjects\RefundStatus;

// Payment status
$status = PaymentStatus::from('completed');
$status->isSuccess(); // true
$status->isTerminal(); // true
$status->isActive(); // false
$status->label(); // "Completed"

// Transaction status
$status = TransactionStatus::from('completed');
$status->isRefundable(); // true

// Refund status
$status = RefundStatus::from('completed');
$status->isSuccess(); // true
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run static analysis
composer analyse

# Format code
composer format
```

### Mocking HTTP Calls

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'test.ownpay.org/api/v1/payments' => Http::response([
        'success' => true,
        'data' => [
            'payment_id' => 'pay_123',
            'token' => 'tok_123',
            'checkout_url' => 'https://checkout.ownpay.org/pay_123',
            'status' => 'pending',
        ],
    ], 201),
]);

// Your test code here
$payment = OwnPay::createPayment([...]);
$this->assertSame('pay_123', $payment->paymentId);
```

### Verifying Webhooks in Tests

```php
use OwnPay\Laravel\Webhook\WebhookVerifier;

$verifier = new WebhookVerifier('test-secret');
$payload = '{"event":"payment.completed","transaction_id":"OP-12345"}';
$signature = $verifier->sign($payload);

$result = $verifier->verify($payload, $signature);
$this->assertSame('payment.completed', $result['event']);
```

## Artisan Commands

### Test Connection

```bash
php artisan ownpay:test
php artisan ownpay:test --json
```

### Verify Webhook

```bash
php artisan ownpay:verify-webhook \
    --payload='{"event":"payment.completed"}' \
    --signature='abc123...' \
    --timestamp=1234567890
```

## Security

### API Key Security

- API keys are stored securely using `#[\SensitiveParameter]` attribute
- Keys are never logged or exposed in error messages
- Use environment variables for sensitive configuration

### Webhook Verification

All incoming webhooks are verified using HMAC-SHA256 signatures:

- Signature: `hash_hmac('sha256', $payload, $secret)`
- Timing-safe comparison using `hash_equals()`
- Timestamp validation to prevent replay attacks

### Best Practices

1. **Never commit API keys** to version control
2. **Use HTTPS** for all API communication
3. **Verify webhook signatures** before processing
4. **Implement idempotency** for critical operations
5. **Handle rate limits** gracefully with retry logic
6. **Log all payment events** for audit trail

## API Reference

### Payments

| Method | Description |
|--------|-------------|
| `createPayment(array $data)` | Create a new payment intent |
| `getPayment(string $id)` | Get payment by ID |

### Transactions

| Method | Description |
|--------|-------------|
| `listTransactions(array $params)` | List transactions with filters |
| `getTransaction(string $id)` | Get transaction by ID |

### Refunds

| Method | Description |
|--------|-------------|
| `createRefund(array $data)` | Create a refund |
| `listRefunds(array $params)` | List refunds with filters |
| `getRefund(string $id)` | Get refund by transaction ID |

### Customers

| Method | Description |
|--------|-------------|
| `createCustomer(array $data)` | Create a customer |
| `listCustomers(array $params)` | List customers |
| `getCustomer(string $id)` | Get customer by email or phone |

### Webhooks

| Method | Description |
|--------|-------------|
| `testWebhook()` | Test webhook endpoint |
| `listWebhookDeliveries()` | List webhook deliveries |

### API Keys

| Method | Description |
|--------|-------------|
| `listApiKeys()` | List API keys |
| `generateApiKey(array $params)` | Generate new API key |
| `revokeApiKey(int $id)` | Revoke API key |

### Health

| Method | Description |
|--------|-------------|
| `health()` | Check API health status |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover a security vulnerability, please email security@ownpay.org. All security vulnerabilities will be promptly addressed.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
