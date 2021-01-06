<?php
class inner_transfer_service
{
    public function __construct()
    {

    }

    private function checkIfGasPriceLow() :? GasPrice
    {
        $defipulse = new defipulse_api();
        $gas_price = new GasPrice($defipulse->getGasPrice());
        return $gas_price;
    }

    public function checkAddresses()
    {

        $token = new TokenEthereum();
        $addresses = new TokenAddresses($token, TokenAddresses::HAS_BALANCE);
        $main_address = new MainAddress();
        $ethereum = new etherium_class();
        $gas_price = $this->checkIfGasPriceLow();
        if(null !== $gas_price) {
            $gas = $gas_price->countGas($gas_price->getLowSafe(), $token);
            foreach ($addresses->getList() as $address) {
                $address->lock();
                $current_balance = $ethereum->getAddressBalance($address->getAddress());
                $amount = maths_class::minus($current_balance, $gas);
                $tx_id = $ethereum->sendRowTransaction($address, $main_address->getAddress(), $amount, $gas_price->getLowSafe());
                $transaction = new Transaction();
                $transaction->setSymbol($token->getSymbol());
                $transaction->setAmount($amount);
                $transaction->setType('out');
                $transaction->setFrom($address->getAddress());
                $transaction->setTo($main_address->getAddress());
                $transaction->setTxId($tx_id);
                $transaction->setStatusLocked();
                $transaction->setLockedAddress($address);
                $transaction->save();
            }
        }
    }

    public function checkLockedTransactions()
    {
        foreach(staticBase::model('transactions')->getByField('status_id', Transaction::STATUS_LOCKED, true) as $item) {
            $transaction = new Transaction($item);
            if($transaction->getSymbol() === 'eth') {
                $ethereum = new etherium_class();
                try {
                    $eth_transaction = $ethereum->getTransaction($transaction);
                    $transaction->setNetData($eth_transaction);
                    $eth_transaction->gasPrice;
                    $eth_transaction->gas;
                    $transaction->unlock();
                    $transaction->getLockedAddress()->unlock();
                } catch (TypeError $exception) {

                }
            }
        }
    }
}