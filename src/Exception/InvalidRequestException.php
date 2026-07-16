<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when the API request is invalid.
 *
 * This includes validation errors (422), bad requests (400),
 * and other client-side errors.
 */
class InvalidRequestException extends ApiException
{
    //
}
