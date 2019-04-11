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
    const MIN_CONFIRMATIONS = 2;
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

    public function sendFrom($from, $to, $amount, $tx_fee = 0.000001)
    {
//        $res = $this->command('settxfee');
        $res = $this->command('listunspent');
        $txs = [];
        foreach ($res['result'] as $item) {
            if($item['confirmations'] < self::MIN_CONFIRMATIONS) {
                continue;
            }
            if($item['address'] === $from) {
                if($item['amount'] + $tx_fee <= $amount) {
                    $txs[] = [
                        'amount' => $amount,
                        'tx_id' => $item['txid'],
                    ];
                    $amount = 0;
                    break;
                } else {
                    $txs[] = [
                        'amount' => $item['amount'],
                        'tx_id' => $item['txid'],
                    ];
                    $amount -= ($item['amount'] + $tx_fee);
                }
            }
        }
        if($txs) {
            print_r($txs);
        }
//        print_r($res);

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