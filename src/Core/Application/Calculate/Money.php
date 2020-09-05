<?php

namespace Core\Application\Calculate;

final class Money
{
    public static function array_sum(array $money): float
    {
        $total = 0;
        array_walk($money, function($coin) use (&$total) {
            $total = bcadd($total, $coin, 2);
        });
        return (float) $total;
    }

    public static function add(float $a, float $b): float
    {
        return (float) bcadd($a, $b, 2);
    }

    public static function sub(float $a, float $b): float
    {
        return (float) bcsub($a, $b, 2);
    }

}
