<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 28/05/2019
 * Time: 17:33
 */
class fake_class extends base
{
    public function proceed($last_checked_block)
    {

        $bitcoin = new bitcoin_class();
        $blockchain = $bitcoin->getBlockChainInfo();
        $last_block = $blockchain['blocks'] - 1;
        if($last_block > $last_checked_block) {
            $hash = $bitcoin->getBlockHash($last_block);
            $block_info = $bitcoin->getBlockInfo($hash);
            print_r($block_info);
        }

    }
}