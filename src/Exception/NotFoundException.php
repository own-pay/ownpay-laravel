<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Exception;

/**
 * Exception thrown when a requested resource is not found.
 *
 * This includes missing payments, transactions, refunds,
 * and customers.
 */
class NotFoundException extends ApiException
{
    //
}
