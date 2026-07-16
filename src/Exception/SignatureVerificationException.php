<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when webhook signature verification fails.
 *
 * This includes invalid signatures, missing signatures,
 * and timestamp tolerance violations.
 */
class SignatureVerificationException extends ApiException
{
    //
}
