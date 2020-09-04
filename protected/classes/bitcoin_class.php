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
        require_once PROTECTED_DIR . 'vendor/autoload.php';
        $port = TEST_MODE === false ? 8332 : 18332;
        $dsn = 'http://' . RPC_USER . ':' . RPC_PASSWORD . '@' . NODE_IP . ':' . $port;
        $this->client = new \Nbobtc\Http\Client($dsn);
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
        $res = $this->command('getwalletinfo');
        if(isset($res['result'])) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }

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

    public function send($to, $amount, $tx_fee = 0.00001)
    {
        $this->command('settxfee', $tx_fee);
        $res = $this->command('sendtoaddress', [$to, $amount]);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }
    }

    public function getTransaction($tx_id)
    {
        $res = $this->command('gettransaction', $tx_id);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
            $this->error($res['error']);
            return false;
        }
    }

    public function getBlockChainInfo()
    {
        $res = $this->command('getblockchaininfo');
        if(isset($res['result'])) {
            return $res['result'];
        } else {
//            print_r($res['error']);
            return false;
        }
    }

    public function getRawTransaction($tx_id)
    {
        $res = $this->command('getrawtransaction', $tx_id);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
//            print_r($res['error']);
            return false;
        }
    }

    public function decodeTransaction($raw)
    {
        $res = $this->command('decoderawtransaction', $raw);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
//            print_r($res['error']);
            return false;
        }
    }

    public function getBlockHash($block)
    {
        $res = $this->command('getblockhash', $block);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
//            print_r($res['error']);
            return false;
        }
    }

    public function getBlockInfo($hash)
    {
        $res = $this->command('getblock', $hash);
        if(isset($res['result'])) {
            return $res['result'];
        } else {
//            print_r($res['error']);
            return false;
        }
    }

    public function sendFrom($from, $to, $amount, $tx_fee = 0.00001)
    {
        $this->command('settxfee', $tx_fee);
        $res = $this->command('listunspent');
        $txs = [];
        foreach ($res['result'] as $item) {
            if($item['confirmations'] < self::MIN_CONFIRMATIONS) {
                continue;
            }
            if($item['address'] === $from) {
                if($item['amount'] >= $amount + $tx_fee) {
                    $txs[] = [
                        'amount' => $amount,
                        'id' => $item['txid'],
                    ];
                    $amount = 0;
                    break;
                } else {
                    $txs[] = [
                        'amount' => $item['amount'] - $tx_fee,
                        'id' => $item['txid'],
                    ];
                    $amount -= ($item['amount'] - $tx_fee);
                }
            }
        }
        if($txs) {
            foreach ($txs as $tx) {

            }
            $res = $this->command('createrawtransaction', [['txid', $tx['id']]]);
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