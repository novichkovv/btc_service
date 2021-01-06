<?php
declare(strict_types=1);
class queue_service
{
    public function __construct()
    {

    }

    public function run()
    {
        $queue = new Queue;
        if($queue->get()) {
            $api = new webhook_api();
            try {
                $api->balance($queue->getToken(), $queue->getAddress(), $queue->getAmount());
                $queue->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
}