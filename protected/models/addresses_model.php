<?php
class addresses_model extends model
{
    public function create(string $address)
    {
        $this->insert([
            'address' => $address,
            'generated_at' => tools_class::gmDate()
        ]);
    }
}