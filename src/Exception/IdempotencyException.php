<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when an idempotency conflict occurs.
 *
 * This includes duplicate requests with different parameters
 * and requests that are still being processed.
 */
class IdempotencyException extends ApiException
{
    //
}
