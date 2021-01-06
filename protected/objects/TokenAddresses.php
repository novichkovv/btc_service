<?php
class TokenAddresses extends DataList
{
    const HAS_BALANCE = 1;
    const NOT_HAVE_BALANCE = 0;
    public function __construct(Token $token, int $has_balance = self::NOT_HAVE_BALANCE)
    {
        parent::__construct();
        $arr = [
            'symbol' => $token->getSymbol(),
            'status_id' => Address::STATUS_UNLOCKED
        ];
        if($has_balance === self::HAS_BALANCE) {
            $arr['has_balance'] = self::HAS_BALANCE;
        }
        foreach (staticBase::model('addresses')->getByFields($arr, true) as $item) {
            $address = new Address($token);
            $address->setFromDb($item);
            $this->add($address);
        }
    }
}