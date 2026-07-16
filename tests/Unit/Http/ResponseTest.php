<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests\Unit\Http;

use OwnPay\Laravel\Http\Response;
use OwnPay\Laravel\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function test_can_create_from_http_response(): void
    {
        $body = json_encode(['success' => true, 'data' => ['id' => 1]]);
        $response = Response::fromHttpResponse(200, ['content-type' => 'application/json'], $body);

        $this->assertTrue($response->isSuccess());
        $this->assertTrue($response->getSuccess());
        $this->assertSame(['id' => 1], $response->getData());
    }

    public function test_can_check_client_error(): void
    {
        $body = json_encode(['success' => false, 'error' => 'Not found']);
        $response = Response::fromHttpResponse(404, [], $body);

        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isClientError());
        $this->assertFalse($response->isServerError());
    }

    public function test_can_check_server_error(): void
    {
        $body = json_encode(['success' => false, 'error' => 'Server error']);
        $response = Response::fromHttpResponse(500, [], $body);

        $this->assertFalse($response->isSuccess());
        $this->assertFalse($response->isClientError());
        $this->assertTrue($response->isServerError());
    }

    public function test_can_get_error_message(): void
    {
        $body = json_encode(['success' => false, 'error' => 'Invalid amount']);
        $response = Response::fromHttpResponse(422, [], $body);

        $this->assertSame('Invalid amount', $response->getErrorMessage());
    }

    public function test_can_get_error_code(): void
    {
        $body = json_encode([
            'success' => false,
            'errors' => [['code' => 'INVALID_AMOUNT', 'message' => 'Invalid amount']],
        ]);
        $response = Response::fromHttpResponse(422, [], $body);

        $this->assertSame('INVALID_AMOUNT', $response->getErrorCode());
    }

    public function test_can_get_meta(): void
    {
        $body = json_encode([
            'success' => true,
            'data' => [],
            'meta' => ['page' => 1, 'per_page' => 25],
        ]);
        $response = Response::fromHttpResponse(200, [], $body);

        $this->assertSame(['page' => 1, 'per_page' => 25], $response->getMeta());
    }

    public function test_can_get_rate_limit_headers(): void
    {
        $headers = [
            'x-ratelimit-limit' => '60',
            'x-ratelimit-remaining' => '59',
        ];
        $response = Response::fromHttpResponse(200, $headers, '{}');

        $this->assertSame(60, $response->getRateLimit());
        $this->assertSame(59, $response->getRateLimitRemaining());
    }

    public function test_can_get_retry_after(): void
    {
        $headers = ['retry-after' => '60'];
        $response = Response::fromHttpResponse(429, $headers, '{}');

        $this->assertSame(60, $response->getRetryAfter());
    }
}
