<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

use Random\RandomException;

final class NumberHelper
{

    public static function format(
        float  $number,
        int    $decimals = 0,
        string $decPoint = ',',
        string $thousandsSep = ' '
    ): string
    {
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }

    public static function percent(int $valeur, int $total, int $precision = 2): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($valeur / $total) * 100, $precision);
    }

    /**
     * @throws RandomException
     */
    public static function generateIntegerCode(int $length = 6): int
    {
        if ($length <= 0) {
            return 0;
        }

        $min = 10 ** ($length - 1);
        $max = (10 ** $length) - 1;

        return random_int($min, $max);
    }

    public static function ceiling(float $number, int $placement = 1): float
    {
        $placement = max($placement, 1);
        $precision = 10 ** ($placement - 1);

        return (float)(ceil(round($number * $precision)) / $precision);
    }
}
