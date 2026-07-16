<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel;

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
            $config = $app['config']['ownpay'];

            return new BearerTokenAuthenticator(
                apiKey: $config['api_key'] ?? '',
            );
        });

        // Register HTTP client
        $this->app->singleton(HttpClient::class, function ($app) {
            $config = $app['config']['ownpay'];

            return new HttpClient(
                authenticator: $app->make(BearerTokenAuthenticator::class),
                baseUrl: $config['base_url'] ?? 'https://pay.ownpay.org',
                timeout: $config['timeout'] ?? 30,
                verifySsl: $config['verify_ssl'] ?? true,
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
            $config = $app['config']['ownpay'];

            return new WebhookVerifier(
                webhookSecret: $config['webhook_secret'] ?? '',
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/ownpay.php' => config_path('ownpay.php'),
        ], 'ownpay-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'ownpay-migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestConnectionCommand::class,
                VerifyWebhookCommand::class,
            ]);
        }

        // Register middleware alias
        $this->app->booted(function () {
            $router = $this->app['router'];

            if (method_exists($router, 'aliasMiddleware')) {
                $router->aliasMiddleware('ownpay.webhook', VerifyWebhookSignature::class);
            }
        });
    }
}
