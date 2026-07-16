<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when webhook processing fails.
 *
 * This includes invalid payloads, missing fields,
 * and other webhook-related issues.
 */
class WebhookException extends ApiException
{
    //
}
