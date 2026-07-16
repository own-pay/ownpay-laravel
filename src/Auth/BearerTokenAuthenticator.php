<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Auth;

/**
 * Bearer token authenticator for OwnPay API.
 *
 * This authenticator adds the Bearer token to the Authorization header.
 * The token should be an OwnPay API key starting with "op_" prefix.
 */
final readonly class BearerTokenAuthenticator implements AuthenticatorInterface
{
    /**
     * Create a new BearerTokenAuthenticator.
     *
     * @param  string  $apiKey  The OwnPay API key.
     */
    public function __construct(
        #[\SensitiveParameter]
        private string $apiKey,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(array $headers): array
    {
        $headers['Authorization'] = 'Bearer '.$this->apiKey;
        $headers['Accept'] = 'application/json';

        return $headers;
    }

    /**
     * Get the API key (masked for logging).
     */
    public function getMaskedKey(): string
    {
        if (strlen($this->apiKey) <= 8) {
            return 'op_****';
        }

        return substr($this->apiKey, 0, 7).'****';
    }
}
