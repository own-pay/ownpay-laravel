<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Auth;

/**
 * Interface for request authenticators.
 *
 * Authenticators are responsible for adding authentication credentials
 * to HTTP requests before they are sent to the OwnPay API.
 */
interface AuthenticatorInterface
{
    /**
     * Authenticate an HTTP request by adding the required headers.
     *
     * @param  array<string, string>  $headers  The existing headers.
     * @return array<string, string>  The headers with authentication added.
     */
    public function authenticate(array $headers): array;
}
