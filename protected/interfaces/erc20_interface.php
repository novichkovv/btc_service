<?php
interface erc20_interface
{
    const MIN_CONFIRMAIONS = 12;
    public function getAddressBalance(string $address) : string ;

    public function newAccount(Password $password) : string ;

    public function send(Address $address_from, string $address_to, string $value) : string ;

    public function getLatestBlock() : int;
}