<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Concerns;

/**
 * Trait for making HTTP requests.
 *
 * This trait provides convenience methods for making HTTP requests
 * with common headers and options.
 */
trait MakesHttpRequests
{
    /**
     * Get common headers for API requests.
     *
     * @return array<string, string>
     */
    protected function getCommonHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'OwnPay-Laravel-SDK/1.0',
        ];
    }

    /**
     * Merge headers with common headers.
     *
     * @param  array<string, string>  $headers  Additional headers.
     * @return array<string, string>
     */
    protected function mergeHeaders(array $headers = []): array
    {
        return array_merge($this->getCommonHeaders(), $headers);
    }
}
