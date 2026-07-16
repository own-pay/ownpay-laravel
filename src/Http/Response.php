<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Http;

/**
 * OwnPay API response wrapper.
 *
 * Provides a convenient interface for working with API responses,
 * including JSON parsing, status checking, and error extraction.
 */
final readonly class Response
{
    /**
     * Create a new Response.
     *
     * @param  int  $statusCode  The HTTP status code.
     * @param  array<string, string>  $headers  The response headers.
     * @param  string  $body  The raw response body.
     * @param  array<string, mixed>|null  $data  The parsed JSON data.
     */
    public function __construct(
        public int $statusCode,
        public array $headers,
        public string $body,
        public ?array $data = null,
    ) {
        //
    }

    /**
     * Create a Response from an HTTP response.
     *
     * @param  int  $statusCode  The HTTP status code.
     * @param  array<string, string>  $headers  The response headers.
     * @param  string  $body  The raw response body.
     * @return static
     */
    public static function fromHttpResponse(int $statusCode, array $headers, string $body): static
    {
        $data = null;

        if ($body !== '') {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        return new static($statusCode, $headers, $body, $data);
    }

    /**
     * Check if the response was successful (2xx).
     */
    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if the response was a client error (4xx).
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if the response was a server error (5xx).
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    /**
     * Get the success flag from the response.
     */
    public function getSuccess(): bool
    {
        return $this->data['success'] ?? false;
    }

    /**
     * Get the data from the response.
     *
     * @return array<string, mixed>|null
     */
    public function getData(): ?array
    {
        return $this->data['data'] ?? null;
    }

    /**
     * Get the meta information from the response.
     *
     * @return array<string, mixed>|null
     */
    public function getMeta(): ?array
    {
        return $this->data['meta'] ?? null;
    }

    /**
     * Get the error message from the response.
     */
    public function getErrorMessage(): ?string
    {
        return $this->data['error'] ?? $this->data['message'] ?? null;
    }

    /**
     * Get the error code from the response.
     */
    public function getErrorCode(): ?string
    {
        $errors = $this->data['errors'] ?? [];

        if (is_array($errors) && !empty($errors)) {
            return $errors[0]['code'] ?? null;
        }

        return null;
    }

    /**
     * Get all errors from the response.
     *
     * @return array<int, array{code: string, message: string, field?: string}>
     */
    public function getErrors(): array
    {
        return $this->data['errors'] ?? [];
    }

    /**
     * Get the request ID from the response.
     */
    public function getRequestId(): ?string
    {
        return $this->data['request_id'] ?? $this->headers['x-request-id'] ?? null;
    }

    /**
     * Get a specific header value.
     */
    public function getHeader(string $name): ?string
    {
        $lowercase = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $lowercase) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Get the rate limit from the response headers.
     */
    public function getRateLimit(): ?int
    {
        $value = $this->getHeader('x-ratelimit-limit');

        return $value !== null ? (int) $value : null;
    }

    /**
     * Get the remaining rate limit from the response headers.
     */
    public function getRateLimitRemaining(): ?int
    {
        $value = $this->getHeader('x-ratelimit-remaining');

        return $value !== null ? (int) $value : null;
    }

    /**
     * Get the retry-after value from the response headers.
     */
    public function getRetryAfter(): ?int
    {
        $value = $this->getHeader('retry-after');

        return $value !== null ? (int) $value : null;
    }

    /**
     * Get the raw response body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Get the response as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data ?? [];
    }

    /**
     * Get the JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
