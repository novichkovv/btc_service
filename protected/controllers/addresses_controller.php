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
        $this->success();
    }
}