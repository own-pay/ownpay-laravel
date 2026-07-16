<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when a network connection error occurs.
 *
 * This includes DNS resolution failures, connection timeouts,
 * and other network-related issues.
 */
class ConnectionException extends ApiException
{
    //
}
