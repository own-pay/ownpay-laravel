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

        return isset($details['retry_after']) ? (int) $details['retry_after'] : null;
    }
}
