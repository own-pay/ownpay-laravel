<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Base exception for API-related errors.
 *
 * This abstract class provides a common structure for all API error
 * exceptions, including HTTP status codes, error codes, and error details.
 */
abstract class ApiException extends \RuntimeException implements OwnPayExceptionInterface
{
    /**
     * Create a new API exception.
     *
     * @param  string  $message  The error message.
     * @param  int|null  $httpStatusCode  The HTTP status code from the API response.
     * @param  string|null  $errorCode  The machine-readable error code from the API.
     * @param  array<string, mixed>|null  $errorDetails  Additional error details from the API.
     * @param  \Throwable|null  $previous  The previous exception for chaining.
     */
    public function __construct(
        string $message,
        private readonly ?int $httpStatusCode = null,
        private readonly ?string $errorCode = null,
        private readonly ?array $errorDetails = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    /**
     * Get the machine-readable error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Get additional error details.
     *
     * @return array<string, mixed>|null
     */
    public function getErrorDetails(): ?array
    {
        return $this->errorDetails;
    }
}
