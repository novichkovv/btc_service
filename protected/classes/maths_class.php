<?php
declare(strict_types=1);
class maths_class
{
    public static function plus(string $a, string $b, int $scale = 0) : string
    {
        return bcadd($a, $b, $scale);
    }

    public static function minus(string $a, string $b, int $scale = 0) : string
    {
        return bcadd($a, '-' . $b, $scale);
    }

    public static function divide(string $a, string $b, int $scale = 0) : string
    {
        return bcdiv($a, $b, $scale);
    }

    public static function multiply(string $a, string $b, int $scale = 0) : string
    {
        return bcmul($a, $b, $scale);
    }

    public static function moreThan(string $a, string $b, int $scale = 0) : bool
    {
        return bccomp($a, $b, $scale) === 1;
    }

    public static function moreOrEqual(string $a, string $b, int $scale = 0) : bool
    {
        $bccomp = bccomp($a, $b , $scale);
        return $bccomp === 1 || $bccomp === 0;
    }

    public static function lessThan(string $a, string $b, int $scale = 0) : bool
    {
        return bccomp($a, $b, $scale) === -1;
    }

    public static function lessOrEqual(string $a, string $b, int $scale = 0) : bool
    {
        $bccomp = bccomp($a, $b, $scale);
        return $bccomp === -1 || $bccomp === 0;
    }

    public static function pow(string $a, string $exponent, int $scale = 0) : string
    {
        return bcpow($a, $exponent, $scale);
    }
}