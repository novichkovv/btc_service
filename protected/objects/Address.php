<?php
declare(strict_types=1);
class Address
{
    const STATUS_UNLOCKED = 0;
    const STATUS_LOCKED = 1;

    private ?token $token;
    private array $address;
    private Password $password;
    public function __construct(token $token = null)
    {
        $this->token = $token;
    }

    public function getSymbol() : string
    {
        return $this->token !== null ? $this->token->getSymbol() : 'eth';
    }

    public function getId() : int
    {
        return (int) $this->address['id'];
    }

    public function create(string $address, Password $password) : void
    {
        $this->password = $password;
        $id = staticBase::model('addresses')->insert([
            'address' => $address,
            'address_password' => $password->getEncoded(),
            'symbol' => $this->getSymbol(),
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
        $this->password = new Password($this->address['address_password']);
    }

    public function setById($id) : void
    {
        if(!$this->address = staticBase::model('addresses')->getById($id)) {
            throw new Exception('Address does not exist');
        }
        $this->password = new Password($this->address['address_password']);
    }

    public function setFromDb(array $db_array) : void
    {
        $this->address = $db_array;
        $this->password = new Password($this->address['address_password']);
    }

    public function getBalance() : string
    {
        return $this->address['balance'];
    }

    public function getPassword() : string
    {
        return $this->password->getDecoded();
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
            'has_balance' => 0
        ];
        if(maths_class::moreThan($new_balance, '0', 12)) {
            $row['has_balance'] = 1;
        }
        staticBase::model('addresses')->insert($row);
    }
}