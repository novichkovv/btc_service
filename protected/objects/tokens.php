<?php
class tokens extends DataList
{
    public function __construct()
    {
        parent::__construct();
        foreach (staticBase::model('tokens')->getByField('is_active', 1, true) as $item) {
            $token = new Token();
            $token->setFromDbArray($item);
            $this->add($token, 'symbol');
        }
        $this->add(new TokenEthereum(), 'symbol');
    }
}