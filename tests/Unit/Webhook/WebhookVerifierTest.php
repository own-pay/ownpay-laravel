<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests\Unit\Webhook;

use OwnPay\Laravel\Exception\SignatureVerificationException;
use OwnPay\Laravel\Tests\TestCase;
use OwnPay\Laravel\Webhook\WebhookVerifier;

class WebhookVerifierTest extends TestCase
{
    private WebhookVerifier $verifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->verifier = new WebhookVerifier('test-secret-key');
    }

    public function test_can_sign_payload(): void
    {
        $payload = '{"event":"payment.completed"}';
        $signature = $this->verifier->sign($payload);

        $this->assertNotEmpty($signature);
        $this->assertSame(64, strlen($signature)); // SHA-256 hex is 64 chars
    }

    public function test_can_verify_valid_signature(): void
    {
        $payload = '{"event":"payment.completed","transaction_id":"OP-12345"}';
        $signature = $this->verifier->sign($payload);

        $result = $this->verifier->verify($payload, $signature);

        $this->assertSame('payment.completed', $result['event']);
        $this->assertSame('OP-12345', $result['transaction_id']);
    }

    public function test_rejects_invalid_signature(): void
    {
        $this->expectException(SignatureVerificationException::class);
        $this->expectExceptionCode(0);

        $payload = '{"event":"payment.completed"}';
        $this->verifier->verify($payload, 'invalid-signature');
    }

    public function test_can_handle_sha256_prefix(): void
    {
        $payload = '{"event":"payment.completed"}';
        $signature = 'sha256='.$this->verifier->sign($payload);

        $result = $this->verifier->verify($payload, $signature);

        $this->assertSame('payment.completed', $result['event']);
    }

    public function test_rejects_expired_timestamp(): void
    {
        $this->expectException(SignatureVerificationException::class);

        $payload = '{"event":"payment.completed"}';
        $signature = $this->verifier->sign($payload);
        $timestamp = time() - 600; // 10 minutes ago

        $this->verifier->verify($payload, $signature, $timestamp);
    }

    public function test_accepts_valid_timestamp(): void
    {
        $payload = '{"event":"payment.completed"}';
        $signature = $this->verifier->sign($payload);
        $timestamp = time();

        $result = $this->verifier->verify($payload, $signature, $timestamp);

        $this->assertSame('payment.completed', $result['event']);
    }

    public function test_can_verify_from_request_headers(): void
    {
        $payload = '{"event":"payment.completed"}';
        $signature = $this->verifier->sign($payload);

        $headers = [
            'x-op-signature' => $signature,
            'x-ownpay-timestamp' => (string) time(),
        ];

        $result = $this->verifier->verifyRequest($payload, $headers);

        $this->assertSame('payment.completed', $result['event']);
    }

    public function test_rejects_missing_signature_header(): void
    {
        $this->expectException(SignatureVerificationException::class);

        $payload = '{"event":"payment.completed"}';
        $headers = [];

        $this->verifier->verifyRequest($payload, $headers);
    }

    public function test_rejects_invalid_json_payload(): void
    {
        $this->expectException(SignatureVerificationException::class);

        $payload = 'not-json';
        $signature = $this->verifier->sign($payload);

        $this->verifier->verify($payload, $signature);
    }
}
