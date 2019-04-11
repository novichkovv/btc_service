<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 07/04/2019
 * Time: 20:01
 */
class bitcoin_class extends base
{
    private $client;
    public function __construct()
    {
        $this->client  = new \Nbobtc\Http\Client('http://' . RPC_USER . ':' . RPC_PASSWORD . '@127.0.0.1:18332');
    }

    private function command($method, $param = null)
    {
        $command = new \Nbobtc\Command\Command($method, $param);
        $response = $this->client->sendCommand($command);
        $output   = json_decode($response->getBody()->getContents(), true);
        return $output;
    }

    public function generateAddress()
    {
        return $this->command('getnewaddress');
    }

    public function getWalletInfo()
    {
        return $this->command('getwalletinfo');
    }

    public function getAddressList()
    {
        return $this->command('listaddressgroupings');
    }

    public function validateAddress($address)
    {
        return $this->command('getaddressinfo', $address);
    }
}