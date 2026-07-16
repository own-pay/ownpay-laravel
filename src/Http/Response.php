<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Http;

/**
 * OwnPay API response wrapper.
 *
 * Provides a convenient interface for working with API responses,
 * including JSON parsing, status checking, and error extraction.
 */
final readonly class Response implements \JsonSerializable
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
     */
    public static function fromHttpResponse(int $statusCode, array $headers, string $body): static
    {
        $data = null;

        if ($body !== '') {
            /** @var mixed $decoded */
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            if (is_array($decoded)) {
                /** @var array<string, mixed> $data */
                $data = $decoded;
            }
        }

        return new self($statusCode, $headers, $body, $data);
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
        if ($this->data === null) {
            return false;
        }

        /** @var mixed $success */
        $success = $this->data['success'] ?? false;

        return (bool) $success;
    }

    /**
     * Get the data from the response.
     *
     * @return array<string, mixed>|null
     */
    public function getData(): ?array
    {
        if ($this->data === null) {
            return null;
        }

        /** @var mixed $data */
        $data = $this->data['data'] ?? null;

        if (! is_array($data)) {
            return null;
        }

        /** @var array<string, mixed> $data */
        return $data;
    }

    /**
     * Get the meta information from the response.
     *
     * @return array<string, mixed>|null
     */
    public function getMeta(): ?array
    {
        if ($this->data === null) {
            return null;
        }

        /** @var mixed $meta */
        $meta = $this->data['meta'] ?? null;

        if (! is_array($meta)) {
            return null;
        }

        /** @var array<string, mixed> $meta */
        return $meta;
    }

    /**
     * Get the error message from the response.
     */
    public function getErrorMessage(): ?string
    {
        if ($this->data === null) {
            return null;
        }

        /** @var mixed $error */
        $error = $this->data['error'] ?? $this->data['message'] ?? null;

        if (is_string($error)) {
            return $error;
        }

        return null;
    }

    /**
     * Get the error code from the response.
     */
    public function getErrorCode(): ?string
    {
        if ($this->data === null) {
            return null;
        }

        /** @var mixed $errors */
        $errors = $this->data['errors'] ?? [];

        if (! is_array($errors) || empty($errors)) {
            return null;
        }

        /** @var mixed $firstError */
        $firstError = $errors[0] ?? null;

        if (! is_array($firstError)) {
            return null;
        }

        /** @var mixed $code */
        $code = $firstError['code'] ?? null;

        return is_string($code) ? $code : null;
    }

    /**
     * Get all errors from the response.
     *
     * @return list<array{code: string, message: string, field?: string}>
     */
    public function getErrors(): array
    {
        if ($this->data === null) {
            return [];
        }

        /** @var mixed $errors */
        $errors = $this->data['errors'] ?? [];

        if (! is_array($errors)) {
            return [];
        }

        /** @var list<array{code: string, message: string, field?: string}> $errors */
        return $errors;
    }

    /**
     * Get the request ID from the response.
     */
    public function getRequestId(): ?string
    {
        if ($this->data !== null) {
            /** @var mixed $requestId */
            $requestId = $this->data['request_id'] ?? null;

            if (is_string($requestId)) {
                return $requestId;
            }
        }

        /** @var mixed $headerValue */
        $headerValue = $this->headers['x-request-id'] ?? null;

        return is_string($headerValue) ? $headerValue : null;
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
