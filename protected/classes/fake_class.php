<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 28/05/2019
 * Time: 17:33
 */
class fake_class extends base
{
    const SUMS = [
        0.001,
        0.00375,
        0.002,
        0.0075,
        0.004,
        0.015
    ];
    public function proceed($last_checked_block)
    {
        $bitcoin = new bitcoin_class();
        $blockchain = $bitcoin->getBlockChainInfo();
        $last_block = $blockchain['blocks'] - 1;
        $res = [];
        if($last_block > $last_checked_block) {
            $hash = $bitcoin->getBlockHash($last_block);
            $block_info = $bitcoin->getBlockInfo($hash);
            if($block_info['tx']) {
                foreach ($block_info['tx'] as $i => $tx) {
                    $raw = $bitcoin->getRawTransaction($tx);
                    $decoded = $bitcoin->decodeTransaction($raw);
                    if(count($decoded['vout']) === 1) {
                        if(in_array($decoded['vout'][0]['value'], self::SUMS)) {
                            $res[] = [
                                'tx' => $tx,
                                'value' => $decoded['vout'][0]['value']
                            ];
                        }
                    }
                    if($i == 100) {
                        break;
                    }
                }
            }
        }
        return $res;

    }
}