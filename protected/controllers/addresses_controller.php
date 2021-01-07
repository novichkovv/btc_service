<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 07/04/2019
 * Time: 17:15
 */
class addresses_controller extends controller
{
    public function generate()
    {
        $btc = new bitcoin_class();
        $address = $btc->generateAddress();
        $Address = new Address();
        $Address->create($address);
        $this->success(['data' => [
            'address' => $address
        ]]);
        $this->success(['data' => ['address' => $address]]);
    }

    public function list()
    {
        $btc = new bitcoin_class();
        $address = $btc->getAddressList();
        $this->success(['response' => $address]);
    }

    public function validate()
    {
        $btc = new bitcoin_class();
        $address = $btc->validateAddress($_GET['address']);
        if($address) {
            $this->success(['data' => ['is_valid' => true]]);
        }
        $this->fail();
    }

    public function balance()
    {
        $btc = new bitcoin_class();
        $this->success(['response' => $btc->getReceivedByAddress($_GET['address'], (int) $_GET['min_confirmations'])]);
    }

    public function send()
    {
        $btc = new bitcoin_class();
        $this->success(['tx_id' => $btc->send($_GET['to'], $_GET['amount'], $_GET['tx_fee'])]);
    }
}