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
        echo $last_block . ' - ' . $last_checked_block;
        if($last_block > $last_checked_block) {
            $hash = $bitcoin->getBlockHash($last_block);
            $block_info = $bitcoin->getBlockInfo($hash);
            if($block_info['tx']) {
                foreach ($block_info['tx'] as $i => $tx) {
                    $raw = $bitcoin->getRawTransaction($tx);
                    var_dump($raw);exit;
                    $decoded = bitcoin_class::decodeTransaction($raw);
                    var_dump($decoded);
                    if($i == 10) {
                        break;
                    }
                }
            }
        }
        exit;

    }
}