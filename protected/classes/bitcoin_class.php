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
        $this->client = new \Nbobtc\Http\Client('http://' . RPC_USER . ':' . RPC_PASSWORD . '@127.0.0.1:18332');
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
        $res = $this->command('getnewaddress');
        if($res['result']) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }
    }

    public function getWalletInfo()
    {
        return $this->command('getwalletinfo');
    }

    public function getAddressList()
    {
        $res = $this->command('listaddressgroupings');
        if(isset($res['result'])) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }
    }

    public function validateAddress($address)
    {
        $res = $this->command('getaddressinfo', $address);
        if(!empty($res['result']['address'])) {
            return true;
        }
        return false;
    }

    public function getReceivedByAddress($address, $min_confirmations = 2)
    {
        $res = $this->command('getreceivedbyaddress', [$address, $min_confirmations]);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }

    }

    public function sendFrom($from, $to, $amount)
    {
        $res = $this->command('listunspent');
        print_r($res);

        $res = $this->command('sendfrom', [$from, $to, $amount]);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }
    }

    protected function error($error = 'Unexpected Error!')
    {
        $response = new response();
        $response->withStatus(500);
        $response->withContentType('application/json');
        $response->withJson(['status' => 'error', 'error' => $error]);
        $response->respond();
    }
}