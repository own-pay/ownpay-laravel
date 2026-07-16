<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when authentication fails.
 *
 * This includes invalid API keys, expired keys, revoked keys,
 * and insufficient scope permissions.
 */
class AuthenticationException extends ApiException
{
    //
}
