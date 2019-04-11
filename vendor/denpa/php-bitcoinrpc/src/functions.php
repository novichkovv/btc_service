<?php

declare(strict_types=1);

namespace Denpa\Bitcoin;

use Denpa\Bitcoin\Exceptions\Handler as ExceptionHandler;

if (!function_exists('to_bitcoin')) {
    /**
     * Converts from satoshi to bitcoin.
     *
     * @param int $satoshi
     *
     * @return string
     */
    function to_bitcoin(int $satoshi) : string
    {
        return bcdiv((string) $satoshi, (string) 1e8, 8);
    }
}

if (!function_exists('to_satoshi')) {
    /**
     * Converts from bitcoin to satoshi.
     *
     * @param string|float $bitcoin
     *
     * @return string
     */
    function to_satoshi($bitcoin) : string
    {
        return bcmul(to_fixed((float) $bitcoin, 8), (string) 1e8);
    }
}

if (!function_exists('to_ubtc')) {
    /**
     * Converts from bitcoin to ubtc/bits.
     *
     * @param string|float $bitcoin
     *
     * @return string
     */
    function to_ubtc($bitcoin) : string
    {
        return bcmul(to_fixed((float) $bitcoin, 8), (string) 1e6, 4);
    }
}

if (!function_exists('to_mbtc')) {
    /**
     * Converts from bitcoin to mbtc.
     *
     * @param string|float $bitcoin
     *
     * @return string
     */
    function to_mbtc($bitcoin) : string
    {
        return bcmul(to_fixed((float) $bitcoin, 8), (string) 1e3, 4);
    }
}

if (!function_exists('to_fixed')) {
    /**
     * Brings number to fixed precision without rounding.
     *
     * @param float $number
     * @param int   $precision
     *
     * @return string
     */
    function to_fixed(float $number, int $precision = 8) : string
    {
        $number = $number * pow(10, $precision);

        return bcdiv((string) $number, (string) pow(10, $precision), $precision);
    }
}

if (!function_exists('exception')) {
    /**
     * Gets exception handler instance.
     *
     * @return \Denpa\Bitcoin\Exceptions\Handler
     */
    function exception() : ExceptionHandler
    {
        return ExceptionHandler::getInstance();
    }
}

set_exception_handler([ExceptionHandler::getInstance(), 'handle']);
