<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 07/04/2019
 * Time: 17:15
 */
class wallet_controller extends controller
{
    public function info()
    {
        $btc = new bitcoin_class();
        $address = $btc->getWalletInfo();
        $this->success(['response' => $address]);
    }
}