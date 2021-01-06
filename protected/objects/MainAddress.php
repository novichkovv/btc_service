<?php
declare(strict_types=1);
class MainAddress extends Address
{
    private $address;
    private $password;
    public function __construct()
    {
        parent::__construct();
        $this->password = new Password(MAIN_ADDRESS_PASSWORD);
    }

    public function create(string $address, Password $password) : void
    {

    }

    public function setByAddress(string $address) : void
    {

    }

    public function getBalance() : string
    {
        return '0';
    }

    public function getPassword() : string
    {
        return $this->password->getDecoded();
    }

    public function getAddress() : string
    {
        return MAIN_ADDRESS;
    }

    public function setBalance(string $new_balance) : void
    {

    }
}