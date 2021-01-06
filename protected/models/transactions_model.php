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
            ORDER by last_checked
        ');
        return $this->get_row($stm);
    }
}