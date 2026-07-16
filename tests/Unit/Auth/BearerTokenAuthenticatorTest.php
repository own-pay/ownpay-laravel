<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests\Unit\Auth;

use OwnPay\Laravel\Auth\BearerTokenAuthenticator;
use OwnPay\Laravel\Tests\TestCase;

class BearerTokenAuthenticatorTest extends TestCase
{
    public function test_can_authenticate_request(): void
    {
        $authenticator = new BearerTokenAuthenticator('op_test_key_1234567890');
        $headers = $authenticator->authenticate([]);

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertSame('Bearer op_test_key_1234567890', $headers['Authorization']);
        $this->assertArrayHasKey('Accept', $headers);
        $this->assertSame('application/json', $headers['Accept']);
    }

    public function test_can_get_masked_key(): void
    {
        $authenticator = new BearerTokenAuthenticator('op_test_key_1234567890');
        $masked = $authenticator->getMaskedKey();

        $this->assertSame('op_test****', $masked);
    }

    public function test_handles_short_key(): void
    {
        $authenticator = new BearerTokenAuthenticator('short');
        $masked = $authenticator->getMaskedKey();

        $this->assertSame('op_****', $masked);
    }
}
