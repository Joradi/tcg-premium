<?php

use App\Support\IncludedTaxCalculator;

it('extrae el iva incluido de un precio final', function () {
    $calculator = new IncludedTaxCalculator;

    expect($calculator->calculate(11900))->toBe(1900)
        ->and($calculator->calculate(23800))->toBe(3800);
});

it('mantiene el monto total sin agregar iva adicional', function () {
    $calculator = new IncludedTaxCalculator;

    $grossTotal = 14900;
    $taxTotal = $calculator->calculate($grossTotal);
    $netTotal = $grossTotal - $taxTotal;

    expect($grossTotal)->toBe(14900)
        ->and($taxTotal)->toBe(2379)
        ->and($netTotal + $taxTotal)->toBe($grossTotal);
});

it('rechaza montos brutos negativos', function () {
    $calculator = new IncludedTaxCalculator;

    expect(fn () => $calculator->calculate(-1))
        ->toThrow(InvalidArgumentException::class);
});

it('rechaza tasas de impuesto negativas', function () {
    $calculator = new IncludedTaxCalculator;

    expect(fn () => $calculator->calculate(11900, -1))
        ->toThrow(InvalidArgumentException::class);
});
