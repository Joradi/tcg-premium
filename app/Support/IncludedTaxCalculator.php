<?php

namespace App\Support;

use InvalidArgumentException;

final class IncludedTaxCalculator
{
    public const DEFAULT_TAX_RATE = 1900;

    public function calculate(
        int $grossAmount,
        int $taxRate = self::DEFAULT_TAX_RATE,
    ): int {
        if ($grossAmount < 0) {
            throw new InvalidArgumentException(
                'el monto bruto no puede ser negativo'
            );
        }

        if ($taxRate < 0) {
            throw new InvalidArgumentException(
                'la tasa de impuesto no puede ser negativa'
            );
        }

        $denominator = 10000 + $taxRate;

        return intdiv(
            ($grossAmount * $taxRate) + intdiv($denominator, 2),
            $denominator
        );
    }
}
