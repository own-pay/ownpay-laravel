<?php

declare(strict_types=1);

namespace OwnPay\Laravel\ValueObjects;

/**
 * Money value object.
 *
 * Represents a monetary amount with currency. Uses string representation
 * for amounts to avoid floating-point precision issues.
 */
final readonly class Money
{
    /**
     * Create a new Money instance.
     *
     * @param  string  $amount  The monetary amount as a string (e.g., "100.00").
     * @param  string  $currency  The ISO 4217 currency code (e.g., "USD", "BDT").
     */
    public function __construct(
        public string $amount,
        public string $currency,
    ) {
        // Validate amount format
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
            throw new \InvalidArgumentException(
                "Invalid amount format: '{$amount}'. Must be a positive number with up to 2 decimal places."
            );
        }

        // Validate currency code
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new \InvalidArgumentException(
                "Invalid currency code: '{$currency}'. Must be a 3-letter uppercase ISO 4217 code."
            );
        }
    }

    /**
     * Create a Money instance from a float amount.
     *
     * @param  float  $amount  The monetary amount.
     * @param  string  $currency  The ISO 4217 currency code.
     * @return static
     */
    public static function fromFloat(float $amount, string $currency): static
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Amount must be non-negative, got: {$amount}");
        }

        return new static(number_format($amount, 2, '.', ''), strtoupper($currency));
    }

    /**
     * Create a Money instance from cents/smallest unit.
     *
     * @param  int  $cents  The amount in cents/smallest currency unit.
     * @param  string  $currency  The ISO 4217 currency code.
     * @param  int  $decimalPlaces  The number of decimal places for the currency (default: 2).
     * @return static
     */
    public static function fromCents(int $cents, string $currency, int $decimalPlaces = 2): static
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException("Cents must be non-negative, got: {$cents}");
        }

        $divisor = pow(10, $decimalPlaces);

        return new static(number_format($cents / $divisor, $decimalPlaces, '.', ''), strtoupper($currency));
    }

    /**
     * Get the amount as a float.
     */
    public function toFloat(): float
    {
        return (float) $this->amount;
    }

    /**
     * Get the amount in cents/smallest currency unit.
     *
     * @param  int  $decimalPlaces  The number of decimal places for the currency (default: 2).
     */
    public function toCents(int $decimalPlaces = 2): int
    {
        return (int) bcmul($this->amount, (string) pow(10, $decimalPlaces));
    }

    /**
     * Check if the amount is zero.
     */
    public function isZero(): bool
    {
        return bccomp($this->amount, '0', 2) === 0;
    }

    /**
     * Check if the amount is positive.
     */
    public function isPositive(): bool
    {
        return bccomp($this->amount, '0', 2) > 0;
    }

    /**
     * Add another Money instance.
     *
     * @param  Money  $other  The Money to add.
     * @return static
     */
    public function add(Money $other): static
    {
        $this->assertSameCurrency($other);

        return new static(bcadd($this->amount, $other->amount, 2), $this->currency);
    }

    /**
     * Subtract another Money instance.
     *
     * @param  Money  $other  The Money to subtract.
     * @return static
     */
    public function subtract(Money $other): static
    {
        $this->assertSameCurrency($other);

        $result = bcsub($this->amount, $other->amount, 2);

        if (bccomp($result, '0', 2) < 0) {
            throw new \InvalidArgumentException('Subtraction would result in a negative amount.');
        }

        return new static($result, $this->currency);
    }

    /**
     * Compare with another Money instance.
     *
     * @param  Money  $other  The Money to compare with.
     * @return int  -1 if less, 0 if equal, 1 if greater.
     */
    public function compare(Money $other): int
    {
        $this->assertSameCurrency($other);

        return bccomp($this->amount, $other->amount, 2);
    }

    /**
     * Check if equal to another Money instance.
     */
    public function equals(Money $other): bool
    {
        return $this->compare($other) === 0;
    }

    /**
     * Check if greater than another Money instance.
     */
    public function isGreaterThan(Money $other): bool
    {
        return $this->compare($other) > 0;
    }

    /**
     * Check if less than another Money instance.
     */
    public function isLessThan(Money $other): bool
    {
        return $this->compare($other) < 0;
    }

    /**
     * Get the formatted amount with currency symbol.
     */
    public function format(): string
    {
        return "{$this->currency} {$this->amount}";
    }

    /**
     * Convert to array representation.
     *
     * @return array{amount: string, currency: string}
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    /**
     * Get the JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the string representation.
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Assert that another Money instance has the same currency.
     *
     * @throws \InvalidArgumentException
     */
    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(
                "Currency mismatch: cannot operate on {$this->currency} and {$other->currency}."
            );
        }
    }
}
