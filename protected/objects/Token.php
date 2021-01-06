<?php
class Token
{
    private $token;
    public function __construct()
    {

    }

    public function setFromDbArray(array $db_array) : void
    {
        $this->token = $db_array;
    }

    public function setBySymbol(string $symbol) : void
    {
        if(!$this->token = staticBase::model('tokens')->getByField('symbol', strtoupper($symbol))) {
            throw new Exception('Unexpexted token Symbol');
        }
    }

    public function getContract() : string
    {
        return $this->token['contract'];
    }

    public function getSymbol() : string
    {
        return strtolower($this->token['symbol']);
    }

    public function getGasLimit() : int
    {
        return (int) $this->token['gas_limit'];
    }
}