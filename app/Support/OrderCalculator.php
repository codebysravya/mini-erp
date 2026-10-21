<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Pure money maths on integer cents, so 0.1 * 3 never becomes 0.30000000000000004.
 */
final class OrderCalculator
{
    public static function lineSubtotal(int $quantity, int|float|string $unitPrice): string
    {
        return self::format(self::lineCents($quantity, $unitPrice));
    }

    /**
     * @param  iterable<array{quantity:int, unit_price:int|float|string}>  $lines
     */
    public static function grandTotal(iterable $lines): string
    {
        $cents = 0;
        foreach ($lines as $line) {
            $cents += self::lineCents((int) $line['quantity'], $line['unit_price']);
        }

        return self::format($cents);
    }

    private static function lineCents(int $quantity, int|float|string $unitPrice): int
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be a positive integer.');
        }
        if (! is_numeric($unitPrice) || (float) $unitPrice < 0) {
            throw new InvalidArgumentException('Unit price must be a non-negative number.');
        }

        return $quantity * (int) round(((float) $unitPrice) * 100);
    }

    private static function format(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}