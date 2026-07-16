<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Base interface for all OwnPay SDK exceptions.
 *
 * All exceptions thrown by the OwnPay SDK implement this interface,
 * allowing consumers to catch all SDK-specific exceptions with a
 * single catch block.
 */
interface OwnPayExceptionInterface extends \Throwable
{
    //
}
