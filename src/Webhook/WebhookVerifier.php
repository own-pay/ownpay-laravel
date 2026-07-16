<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Webhook;

use OwnPay\Laravel\Exception\SignatureVerificationException;

/**
 * Webhook signature verifier.
 *
 * Verifies the authenticity of incoming webhook payloads using
 * HMAC-SHA256 signatures. This matches the OwnPay webhook signing
 * implementation in WebhookDispatcher.
 */
final readonly class WebhookVerifier
{
    /**
     * The default timestamp tolerance in seconds (5 minutes).
     */
    private const int DEFAULT_TOLERANCE_SECONDS = 300;

    /**
     * Create a new WebhookVerifier.
     *
     * @param  string  $webhookSecret  The webhook signing secret.
     * @param  int  $toleranceSeconds  The maximum age of a webhook payload in seconds.
     */
    public function __construct(
        #[\SensitiveParameter]
        private string $webhookSecret,
        private int $toleranceSeconds = self::DEFAULT_TOLERANCE_SECONDS,
    ) {
        //
    }

    /**
     * Verify a webhook payload.
     *
     * @param  string  $payload  The raw webhook payload.
     * @param  string  $signatureHeader  The signature header value.
     * @param  int|null  $timestamp  The timestamp from the X-OwnPay-Timestamp header.
     * @return array<string, mixed> The verified and decoded payload.
     *
     * @throws SignatureVerificationException
     */
    public function verify(string $payload, string $signatureHeader, ?int $timestamp = null): array
    {
        // Verify timestamp if provided
        if ($timestamp !== null) {
            $this->verifyTimestamp($timestamp);
        }

        // Compute expected signature
        $expectedSignature = $this->sign($payload);

        // Extract signature from header
        $signature = $this->extractSignature($signatureHeader);

        // Timing-safe comparison
        if (! hash_equals($expectedSignature, $signature)) {
            throw new SignatureVerificationException(
                'Webhook signature verification failed.',
                400,
                'INVALID_SIGNATURE',
            );
        }

        // Decode and return payload
        try {
            /** @var mixed $decoded */
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($decoded)) {
                throw new \JsonException('Payload is not a JSON object.');
            }

            /** @var array<string, mixed> $decoded */
            return $decoded;
        } catch (\JsonException $e) {
            throw new SignatureVerificationException(
                "Invalid webhook payload: {$e->getMessage()}",
                400,
                'INVALID_PAYLOAD',
                null,
                $e,
            );
        }
    }

    /**
     * Verify a webhook from a PSR-7 request or Laravel request.
     *
     * @param  string  $payload  The raw request body.
     * @param  array<string, string>  $headers  The request headers.
     * @return array<string, mixed> The verified and decoded payload.
     *
     * @throws SignatureVerificationException
     */
    public function verifyRequest(string $payload, array $headers): array
    {
        // Extract signature header (support multiple header names)
        $signatureHeader = $this->findHeader($headers, [
            'x-op-signature',
            'x-signature',
            'x-ownpay-signature',
        ]);

        if ($signatureHeader === null) {
            throw new SignatureVerificationException(
                'Missing webhook signature header.',
                400,
                'MISSING_SIGNATURE',
            );
        }

        // Extract timestamp header
        $timestampHeader = $this->findHeader($headers, [
            'x-ownpay-timestamp',
            'x-timestamp',
        ]);

        $timestamp = $timestampHeader !== null ? (int) $timestampHeader : null;

        return $this->verify($payload, $signatureHeader, $timestamp);
    }

    /**
     * Sign a payload.
     *
     * This is the same signing algorithm used by OwnPay's WebhookDispatcher.
     *
     * @param  string  $payload  The payload to sign.
     * @return string The hex-encoded HMAC-SHA256 signature.
     */
    public function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->webhookSecret);
    }

    /**
     * Verify the timestamp is within tolerance.
     *
     * @param  int  $timestamp  The webhook timestamp.
     *
     * @throws SignatureVerificationException
     */
    private function verifyTimestamp(int $timestamp): void
    {
        $now = time();
        $diff = abs($now - $timestamp);

        if ($diff > $this->toleranceSeconds) {
            throw new SignatureVerificationException(
                "Webhook timestamp is outside the allowed tolerance of {$this->toleranceSeconds} seconds.",
                400,
                'TIMESTAMP_EXPIRED',
                ['tolerance_seconds' => $this->toleranceSeconds, 'age_seconds' => $diff],
            );
        }
    }

    /**
     * Extract the signature from the header value.
     *
     * Supports formats:
     * - raw hex signature
     * - sha256=<hex> prefix format
     *
     * @param  string  $header  The signature header value.
     * @return string The hex signature.
     */
    private function extractSignature(string $header): string
    {
        // Handle sha256= prefix
        if (str_starts_with($header, 'sha256=')) {
            return substr($header, 7);
        }

        return $header;
    }

    /**
     * Find a header value from multiple possible header names.
     *
     * @param  array<string, string>  $headers  The headers array.
     * @param  string[]  $names  The header names to search for.
     * @return string|null The header value or null.
     */
    private function findHeader(array $headers, array $names): ?string
    {
        // Normalize header keys to lowercase
        $normalized = [];
        foreach ($headers as $key => $value) {
            $normalized[strtolower($key)] = $value;
        }

        foreach ($names as $name) {
            if (isset($normalized[strtolower($name)])) {
                return $normalized[strtolower($name)];
            }
        }

        return null;
    }
}
