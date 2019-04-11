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
//        require_once PROTECTED_DIR . 'easy.php';
//        $bitcoin = new Bitcoin(RPC_USER,RPC_PASSWORD,"localhost","18332");
//        $b = $bitcoin->getwalletinfo();
//        var_dump($b);exit;
////        require PROTECTED_DIR . '/vendor/autoload.php';
////
////        $this->client = new Denpa\Bitcoin\Client('http://' . RPC_USER . ':' . RPC_PASSWORD . '@localhost:8332/');
////        $block = $this->client->request('getwalletinfo');
////        var_dump($block);exit;
        $driver = new \Nbobtc\Http\Driver\CurlDriver();
        $driver
            ->addCurlOption(CURLOPT_VERBOSE, true)
            ->addCurlOption(CURLOPT_STDERR, '/var/logs/curl.err');
        $this->client  = new \Nbobtc\Http\Client('http://127.0.0.1:18332');
        $this->client->withDriver($driver);
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
        return $this->command('getwalletinfo');
    }
}