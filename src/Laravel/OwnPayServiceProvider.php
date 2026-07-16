<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use OwnPay\Laravel\Auth\BearerTokenAuthenticator;
use OwnPay\Laravel\Client\OwnPayClient;
use OwnPay\Laravel\Http\HttpClient;
use OwnPay\Laravel\Laravel\Console\Commands\TestConnectionCommand;
use OwnPay\Laravel\Laravel\Console\Commands\VerifyWebhookCommand;
use OwnPay\Laravel\Laravel\Middleware\VerifyWebhookSignature;
use OwnPay\Laravel\Webhook\WebhookVerifier;

/**
 * OwnPay service provider for Laravel.
 *
 * This service provider registers all OwnPay services with the Laravel
 * service container, publishes configuration, and registers commands.
 */
class OwnPayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/ownpay.php',
            'ownpay',
        );

        // Register authenticator
        $this->app->singleton(BearerTokenAuthenticator::class, function ($app) {
            /** @var array<string, mixed> $config */
            $config = $app['config']['ownpay'];
            /** @var string $apiKey */
            $apiKey = $config['api_key'] ?? '';

            return new BearerTokenAuthenticator(
                apiKey: $apiKey,
            );
        });

        // Register HTTP client
        $this->app->singleton(HttpClient::class, function ($app) {
            /** @var array<string, mixed> $config */
            $config = $app['config']['ownpay'];
            /** @var string $baseUrl */
            $baseUrl = $config['base_url'] ?? 'https://pay.ownpay.org';
            /** @var int $timeout */
            $timeout = $config['timeout'] ?? 30;
            /** @var bool $verifySsl */
            $verifySsl = $config['verify_ssl'] ?? true;

            return new HttpClient(
                authenticator: $app->make(BearerTokenAuthenticator::class),
                baseUrl: $baseUrl,
                timeout: $timeout,
                verifySsl: $verifySsl,
            );
        });

        // Register API client
        $this->app->singleton(OwnPayClient::class, function ($app) {
            return new OwnPayClient(
                http: $app->make(HttpClient::class),
            );
        });

        // Register webhook verifier
        $this->app->singleton(WebhookVerifier::class, function ($app) {
            /** @var array<string, mixed> $config */
            $config = $app['config']['ownpay'];
            /** @var string $webhookSecret */
            $webhookSecret = $config['webhook_secret'] ?? '';

            return new WebhookVerifier(
                webhookSecret: $webhookSecret,
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/ownpay.php' => config_path('ownpay.php'),
            ], 'ownpay-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'ownpay-migrations');

            // Register commands
            $this->commands([
                TestConnectionCommand::class,
                VerifyWebhookCommand::class,
            ]);
        }

        // Register middleware alias
        if ($this->app->bound(Kernel::class)) {
            /** @var Kernel $kernel */
            $kernel = $this->app->make(Kernel::class);

            if (method_exists($kernel, 'aliasMiddleware')) {
                $kernel->aliasMiddleware('ownpay.webhook', VerifyWebhookSignature::class);
            }
        }
    }
}
