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
            if($block_info['tx']) {
                foreach ($block_info['tx'] as $i => $tx) {
                    $raw = $bitcoin->getRawTransaction($tx);
                    $decoded = $bitcoin->decodeTransaction($raw);
                    if(count($decoded['vout']) === 1) {
                        echo '111111' . "\n";
                        echo $decoded['vout'][0]['value'] . "\n";
                        if(in_array($decoded['vout'][0]['value'], [0.014])) {
                            echo '222222';
                        }
                    }
                    print_r($decoded);
                    if($i == 10) {
                        break;
                    }
                }
            }
        }
        exit;

    }
}