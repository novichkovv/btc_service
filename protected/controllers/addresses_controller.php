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
        $this->success(['addresses' => $address]);
    }

    public function validate()
    {
        print_r($_GET);
        print_r($_POST);
        exit;
        $btc = new bitcoin_class();
        $address = $btc->validateAddress($_GET['address']);
        $this->success(['response' => $address]);
    }
}