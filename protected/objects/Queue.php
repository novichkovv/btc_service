<?php
declare(strict_types=1);
class Queue
{
    const STATUS_NEW = 0;
    const STATUS_SENT = 1;
    private $item;
    public function __construct()
    {

    }

    public function add(Address $address, string $amount) : void
    {
        $this->item = [
            'address' => $address->getAddress(),
            'amount' => $amount,
            'status_id' => self::STATUS_NEW,
            'created_at' => tools_class::gmDate()
        ];
        staticBase::model('webhook_queue')->insert($this->item);
    }

    public function get() : bool
    {
        $this->item = staticBase::model('webhook_queue')->getByField('status_id', self::STATUS_NEW, false, 'created_at');
        return !empty($this->item);
    }

    public function getToken() : string
    {
        return 'btc';
    }

    public function getAddress() : string
    {
        return $this->item['address'];
    }

    public function getAmount() : string
    {
        return $this->item['amount'];
    }

    public function delete() : void
    {
        staticBase::model('webhook_queue')->insert([
            'id' => $this->item['id'],
            'status_id' => self::STATUS_SENT
        ]);
    }
}