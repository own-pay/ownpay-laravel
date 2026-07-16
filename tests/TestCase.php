<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use OwnPay\Laravel\Laravel\OwnPayServiceProvider;

/**
 * Base test case for OwnPay package tests.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            OwnPayServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('ownpay.api_key', 'op_test_12345678.abcdef1234567890abcdef12');
        $app['config']->set('ownpay.webhook_secret', 'test-webhook-secret-key');
        $app['config']->set('ownpay.base_url', 'https://test.ownpay.org');
        $app['config']->set('ownpay.timeout', 5);
        $app['config']->set('ownpay.retry_attempts', 1);
        $app['config']->set('ownpay.verify_ssl', false);
    }
}
