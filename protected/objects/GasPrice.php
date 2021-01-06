<?php
declare(strict_types=1);
class GasPrice
{
    private array $prices;
    public function __construct(array $defiprices)
    {
        $this->prices = $defiprices;
    }

    public function getLowSafe() : string
    {
        return $this->convertDefiToEth($this->prices['lowSafe']);
    }

    public function getAverage() : string
    {
        return $this->convertDefiToEth($this->prices['average']);
    }

    private function convertDefiToEth(int $defi_value) : string
    {
        $gwei = number_format($defi_value/10, 1);
        return maths_class::divide($gwei, maths_class::pow('10', '9'), 18);
    }

    public function countGas(string $gwei_price, Token $token) : string
    {
        return maths_class::multiply($gwei_price, (string) $token->getGasLimit());
    }
}