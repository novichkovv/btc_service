<?php
class transactions_model extends model
{
    public function getUnconfirmed()
    {
        $date = new Time();
        $date->minusMinutes(1);
        $stm = $this->pdo->prepare('
            SELECT
                *
            FROM
                transactions
            WHERE
                confirmed = 0 AND last_checked < "' . $date->getGMTDateTime() . '"
        ');
        return $this->get_all($stm);
    }
}