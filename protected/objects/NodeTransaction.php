<?php
class NodeTransaction
{
    private array $transaction;
    public function __construct(array $transaction)
    {
        $this->transaction = $transaction;
    }
}