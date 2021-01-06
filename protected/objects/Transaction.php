<?php
declare(strict_types=1);
class Transaction
{
    const STATUS_LOCKED = 1;
    const STATUS_UNLOCKED = 0;
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    private array $transaction;
    private ?\FurqanSiddiqui\Ethereum\RPC\Models\Transaction $net_data = null;
    public function __construct(array $db_array = null)
    {
        if(null !== $db_array) {
            $this->transaction = $db_array;
        }
        $this->transaction['status_id'] = self::STATUS_UNLOCKED;
    }

    public function setSymbol(string $symbol)
    {
        $this->transaction['symbol'] = $symbol;
    }

    public function setFrom(string $address) : void
    {
        $this->transaction['address_from'] = $address;
    }

    public function setLockedAddress(Address $address)
    {
        $this->transaction['locked_address'] = $address->getId();
    }

    public function getLockedAddress() : ?Address
    {
        if(!empty($this->transaction['locked_address'])) {
            $address = new Address();
            $address->setById($this->transaction['locked_address']);
            return $address;
        }
        return null;
    }

    public function setNetData(\FurqanSiddiqui\Ethereum\RPC\Models\Transaction $transaction)
    {
        $this->net_data = $transaction;
    }

    public function setStatusLocked()
    {
        $this->transaction['status_id'] = self::STATUS_LOCKED;
    }

    public function unlock()
    {
        staticBase::model('transactions')->insert([
            'id' => $this->transaction['id'],
            'status_id' => self::STATUS_LOCKED
        ]);
    }

    public function setTo(string $address) : void
    {
        $this->transaction['address_to'] = $address;
    }

    public function setAmount(string $amount) : void
    {
        $this->transaction['amount'] = $amount;
    }

    public function setType(string $type) : void
    {
        if($type !== self::TYPE_IN && $type !== self::TYPE_OUT) {
            throw new Exception('Unexpected TX type');
        }
        $this->transaction['transaction_type'] = $type;
    }

    public function setTxId(string $tx_id) : void
    {
        $this->transaction['tx_id'] = $tx_id;
    }

    public function getSymbol()
    {
        return $this->transaction['symbol'];
    }

    public function save() : void
    {
        $this->transaction;
        $this->transaction['created_at'] = tools_class::gmDate();
        if($this->net_data !== null) {
            $this->transaction['gas'] = $this->net_data->gas;
            $this->transaction['gas_price'] = $this->net_data->gasPrice;
        }
        $this->transaction['id'] = staticBase::model('transactions')->insert($this->transaction);
    }
}
