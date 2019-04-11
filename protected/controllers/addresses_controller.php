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
        $this->success(['address' => $address]);
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
            $this->success();
        }
        $this->fail();
    }

    public function received()
    {
        $btc = new bitcoin_class();
        $this->success(['response' => $btc->getReceivedByAddress($_GET['address'], $_GET['min_confirmations'])]);
    }
}