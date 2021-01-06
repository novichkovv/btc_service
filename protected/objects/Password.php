<?php
declare(strict_types=1);
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
class Password
{
    const LENGTH = 8;
    private string $password;
    public function __construct(string $encoded_password = null)
    {
        if($encoded_password !== null) {
            $this->password = $this->decode($encoded_password);
        }
    }

    public function generate()
    {
        $symbols = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890~`!@#$%^&*()-_+=[]|{};:,<.>?/';
        $string = '';
        for($i = 0; $i < self::LENGTH; $i ++) {
            $string .= $symbols[rand(0,strlen($symbols) - 1)];
        }
        $this->password = $string;
    }

    public function getDecoded()
    {
        return $this->password;
    }

    public function getEncoded()
    {
        return trim($this->encode($this->password));
    }

    private function decode(string $encoded_password) : string
    {
        return Crypto::decrypt($encoded_password, $this->getKey());
    }

    private function encode(string $password) : string
    {
        return Crypto::encrypt($password, $this->getKey());
    }

    private function getKey() : Key
    {
        return Key::loadFromAsciiSafeString(ENCRYPTION_KEY);
    }
}