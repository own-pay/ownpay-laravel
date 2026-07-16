<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when the API rate limit is exceeded.
 *
 * The exception includes the number of seconds to wait before
 * retrying the request.
 */
class RateLimitException extends ApiException
{
    /**
     * Get the number of seconds to wait before retrying.
     */
    public function getRetryAfter(): ?int
    {
        $details = $this->getErrorDetails();

        if ($details === null) {
            return null;
        }

        /** @var mixed $retryAfter */
        $retryAfter = $details['retry_after'] ?? null;

        if (is_int($retryAfter)) {
            return $retryAfter;
        }

        if (is_string($retryAfter) && is_numeric($retryAfter)) {
            return (int) $retryAfter;
        }

        return null;
    }
}
