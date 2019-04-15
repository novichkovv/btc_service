<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 15/04/2019
 * Time: 11:59
 */
class transaction_controller extends controller
{
    public function info()
    {
        $btc = new bitcoin_class();
        $tx = $btc->getTransaction($_GET['tx_id']);
        $this->success(['response' => $tx]);
    }
}