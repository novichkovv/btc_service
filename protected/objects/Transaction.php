<?php
declare(strict_types=1);
class Transaction
{
    const STATUS_LOCKED = 1;
    const STATUS_UNLOCKED = 0;
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    private $transaction;
    private $net_data;
    public function __construct(array $db_array = null)
    {
        if(null !== $db_array) {
            $this->transaction = $db_array;
        }
        $this->transaction['status_id'] = self::STATUS_UNLOCKED;
    }

    public function transactionExists($tx_id) : bool
    {
        if($t = staticBase::model('transactions')->getByField('tx_id', $tx_id)) {
            $this->transaction = $t;
            return true;
        }
        return false;
    }

    public function setFrom(string $address) : void
    {
        $this->transaction['address_from'] = $address;
    }

    public function setLockedAddress(Address $address)
    {
        $this->transaction['locked_address'] = $address->getId();
    }

    public function getAmount() : string
    {
        return $this->transaction['amount'];
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

    public function getAddressTo() : Address
    {
        $address = new Address();
        $address->setByAddress($this->transaction['address_to']);
        return $address;
    }

    public function updateLastCheck() :void
    {
        staticBase::model('transactions')->insert([
            'id' => $this->transaction['id'],
            'last_checked' => tools_class::gmDate()
        ]);
    }

    public function confirm() :void
    {
        staticBase::model('transactions')->insert([
            'id' => $this->transaction['id'],
            'confirmed' => 1
        ]);
    }

    public function setNetData($transaction)
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

    public function setAmount($amount) : void
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

    public function getTxId() : string
    {
        return $this->transaction['tx_id'];
    }

    public function save() : void
    {
        $this->transaction;
        $this->transaction['created_at'] = tools_class::gmDate();
        $this->transaction['confirmed'] = 0;
        $this->transaction['last_checked'] = tools_class::gmDate();
        $this->transaction['id'] = staticBase::model('transactions')->insert($this->transaction);
    }
}
