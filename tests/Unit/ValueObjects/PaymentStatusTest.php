<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests\Unit\ValueObjects;

use OwnPay\Laravel\Tests\TestCase;
use OwnPay\Laravel\ValueObjects\PaymentStatus;

class PaymentStatusTest extends TestCase
{
    public function test_can_create_from_string(): void
    {
        $status = PaymentStatus::from('pending');

        $this->assertSame(PaymentStatus::Pending, $status);
    }

    public function test_can_check_terminal_states(): void
    {
        $this->assertTrue(PaymentStatus::Completed->isTerminal());
        $this->assertTrue(PaymentStatus::Failed->isTerminal());
        $this->assertTrue(PaymentStatus::Cancelled->isTerminal());
        $this->assertTrue(PaymentStatus::Expired->isTerminal());
        $this->assertFalse(PaymentStatus::Pending->isTerminal());
        $this->assertFalse(PaymentStatus::Processing->isTerminal());
    }

    public function test_can_check_success(): void
    {
        $this->assertTrue(PaymentStatus::Completed->isSuccess());
        $this->assertFalse(PaymentStatus::Failed->isSuccess());
    }

    public function test_can_check_active(): void
    {
        $this->assertTrue(PaymentStatus::Pending->isActive());
        $this->assertTrue(PaymentStatus::Processing->isActive());
        $this->assertFalse(PaymentStatus::Completed->isActive());
    }

    public function test_can_get_label(): void
    {
        $this->assertSame('Pending', PaymentStatus::Pending->label());
        $this->assertSame('Completed', PaymentStatus::Completed->label());
    }
}
