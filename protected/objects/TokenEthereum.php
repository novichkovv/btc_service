<?php
class TokenEthereum extends Token
{
    public function setFromDbArray(array $db_array) : void
    {
    }

    public function setBySymbol(string $symbol) : void
    {

    }

    public function getContract() : string
    {
        return '';
    }

    public function getSymbol() : string
    {
        return 'eth';
    }

    public function getGasLimit() : int
    {
        return 21000;
    }
}