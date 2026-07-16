<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Tests\Unit\ValueObjects;

use OwnPay\Laravel\Tests\TestCase;
use OwnPay\Laravel\ValueObjects\Money;

class MoneyTest extends TestCase
{
    public function test_can_create_money(): void
    {
        $money = new Money('100.00', 'USD');

        $this->assertSame('100.00', $money->amount);
        $this->assertSame('USD', $money->currency);
    }

    public function test_can_create_from_float(): void
    {
        $money = Money::fromFloat(100.5, 'BDT');

        $this->assertSame('100.50', $money->amount);
        $this->assertSame('BDT', $money->currency);
    }

    public function test_can_create_from_cents(): void
    {
        $money = Money::fromCents(10050, 'USD');

        $this->assertSame('100.50', $money->amount);
        $this->assertSame('USD', $money->currency);
    }

    public function test_can_convert_to_float(): void
    {
        $money = new Money('100.50', 'USD');

        $this->assertSame(100.5, $money->toFloat());
    }

    public function test_can_convert_to_cents(): void
    {
        $money = new Money('100.50', 'USD');

        $this->assertSame(10050, $money->toCents());
    }

    public function test_can_check_if_zero(): void
    {
        $zero = new Money('0.00', 'USD');
        $nonZero = new Money('100.00', 'USD');

        $this->assertTrue($zero->isZero());
        $this->assertFalse($nonZero->isZero());
    }

    public function test_can_check_if_positive(): void
    {
        $zero = new Money('0.00', 'USD');
        $positive = new Money('100.00', 'USD');

        $this->assertFalse($zero->isPositive());
        $this->assertTrue($positive->isPositive());
    }

    public function test_can_add_money(): void
    {
        $a = new Money('100.00', 'USD');
        $b = new Money('50.00', 'USD');
        $result = $a->add($b);

        $this->assertSame('150.00', $result->amount);
    }

    public function test_can_subtract_money(): void
    {
        $a = new Money('100.00', 'USD');
        $b = new Money('50.00', 'USD');
        $result = $a->subtract($b);

        $this->assertSame('50.00', $result->amount);
    }

    public function test_cannot_subtract_more_than_available(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $a = new Money('50.00', 'USD');
        $b = new Money('100.00', 'USD');
        $a->subtract($b);
    }

    public function test_cannot_operate_on_different_currencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $a = new Money('100.00', 'USD');
        $b = new Money('50.00', 'BDT');
        $a->add($b);
    }

    public function test_can_compare_money(): void
    {
        $a = new Money('100.00', 'USD');
        $b = new Money('50.00', 'USD');
        $c = new Money('100.00', 'USD');

        $this->assertTrue($a->isGreaterThan($b));
        $this->assertTrue($b->isLessThan($a));
        $this->assertTrue($a->equals($c));
    }

    public function test_can_format_money(): void
    {
        $money = new Money('100.00', 'USD');

        $this->assertSame('USD 100.00', $money->format());
        $this->assertSame('USD 100.00', (string) $money);
    }

    public function test_can_convert_to_array(): void
    {
        $money = new Money('100.00', 'USD');

        $this->assertSame([
            'amount' => '100.00',
            'currency' => 'USD',
        ], $money->toArray());
    }

    public function test_validates_amount_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money('invalid', 'USD');
    }

    public function test_validates_currency_code(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money('100.00', 'us');
    }

    public function test_validates_negative_amount_from_float(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::fromFloat(-100, 'USD');
    }
}
