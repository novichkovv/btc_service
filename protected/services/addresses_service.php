<?php
declare(strict_types=1);
class addresses_service
{
    public function newTransaction(string $tx_id)
    {
        $btc = new bitcoin_class();
        $transaction = $btc->getTransaction($tx_id);
        $to = $transaction['details'][0]['address'];
        $amount = $transaction['amount'];
        $address = new Address();
        try {
            $address->setByAddress($to);
            $transaction = new Transaction();
            if($transaction->transactionExists($tx_id)) {
                $this->checkTransaction($transaction);
                return;
            }
            $transaction->setTo($address->getAddress());
            $transaction->setAmount($amount);
            $transaction->setTxId($tx_id);
            $transaction->setType(Transaction::TYPE_IN);
            $transaction->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function checkTransaction(Transaction $transaction = null)
    {
        if(null === $transaction) {
            if($item = staticBase::model('transactions')->getUnconfirmed()) {
                $transaction = new Transaction($item);
            }
        }
        if(null !== $transaction) {
            $transaction = new Transaction($item);
            $btc = new bitcoin_class();
            $net = $btc->getTransaction($transaction->getTxId());
            if($net['confirmations'] >= 3) {
                $queue = new Queue();
                $queue->add($transaction->getAddressTo(), $transaction->getAmount());
                $transaction->confirm();
                $balance = maths_class::plus($transaction->getAmount(), $transaction->getAddressTo()->getBalance(), 8);
                $transaction->getAddressTo()->setBalance($balance);
                $transaction->getAddressTo()->setHasBalance();
            } else {
                $transaction->updateLastCheck();
            }
        }
    }
}