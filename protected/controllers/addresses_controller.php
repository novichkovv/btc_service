<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 07/04/2019
 * Time: 17:15
 */
class addresses_controller extends controller
{
    private $btc;

    protected function init()
    {
        $this->btc = new bitcoin_class();
    }

    public function generate()
    {
        $address = $this->btc->generateAddress();
        $this->success(['address' => $address]);
    }

    public function list()
    {
        $btc = new bitcoin_class();
        $address = $btc->getAddressList();
        $this->success(['addresses' => $address]);
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
}