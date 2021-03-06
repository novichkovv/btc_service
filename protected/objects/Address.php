<?php
declare(strict_types=1);
class Address
{
    const STATUS_UNLOCKED = 0;
    const STATUS_LOCKED = 1;

    private $address;
    public function __construct()
    {
    }

    public function getId() : int
    {
        return (int) $this->address['id'];
    }

    public function create(string $address) : void
    {
        $id = staticBase::model('addresses')->insert([
            'address' => $address,
            'generated_at' => tools_class::gmDate()
        ]);
        $this->address = staticBase::model('addresses')->getById($id);
        if(empty($this->address)) {
            throw new Exception('Could not create DB address');
        }
    }

    public function unlock() : void
    {
        staticBase::model('addresses')->insert([
            'id' => $this->address['id'],
            'status_id' => self::STATUS_UNLOCKED
        ]);
    }

    public function lock() : void
    {
        staticBase::model('addresses')->insert([
            'id' => $this->address['id'],
            'status_id' => self::STATUS_LOCKED
        ]);
    }

    public function setByAddress(string $address) : void
    {
        if(!$this->address = staticBase::model('addresses')->getByField('address', $address)) {
            throw new Exception('Address does not exist');
        }
    }

    public function setById($id) : void
    {
        if(!$this->address = staticBase::model('addresses')->getById($id)) {
            throw new Exception('Address does not exist');
        }
    }

    public function setFromDb(array $db_array) : void
    {
        $this->address = $db_array;
    }

    public function getBalance() : string
    {
        return $this->address['balance'];
    }

    public function getAddress() : string
    {
        return $this->address['address'];
    }

    public function setHasBalance() : void
    {
        staticBase::model('addresses')->insert([
            'id' => $this->address['id'],
            'has_balance' => 1
        ]);
    }

    public function setNotHaveBalance() : void
    {
        staticBase::model('addresses')->insert([
            'id' => $this->address['id'],
            'has_balance' => 0
        ]);
    }

    public function setBalance(string $new_balance) : void
    {
        $row = [
            'id' => $this->address['id'],
            'balance' => $new_balance,
        ];
        staticBase::model('addresses')->insert($row);
    }
}