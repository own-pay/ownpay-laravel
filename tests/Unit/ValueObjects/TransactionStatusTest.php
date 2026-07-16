<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests\Unit\ValueObjects;

use OwnPay\Laravel\Tests\TestCase;
use OwnPay\Laravel\ValueObjects\TransactionStatus;

class TransactionStatusTest extends TestCase
{
    public function test_can_create_from_string(): void
    {
        $status = TransactionStatus::from('pending');

        $this->assertSame(TransactionStatus::Pending, $status);
    }

    public function test_can_check_terminal_states(): void
    {
        $this->assertTrue(TransactionStatus::Completed->isTerminal());
        $this->assertTrue(TransactionStatus::Failed->isTerminal());
        $this->assertTrue(TransactionStatus::Cancelled->isTerminal());
        $this->assertTrue(TransactionStatus::Expired->isTerminal());
        $this->assertTrue(TransactionStatus::Refunded->isTerminal());
        $this->assertFalse(TransactionStatus::Pending->isTerminal());
    }

    public function test_can_check_refundable(): void
    {
        $this->assertTrue(TransactionStatus::Completed->isRefundable());
        $this->assertTrue(TransactionStatus::Refunded->isRefundable());
        $this->assertFalse(TransactionStatus::Pending->isRefundable());
        $this->assertFalse(TransactionStatus::Failed->isRefundable());
    }
}
