<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 06.03.15
 * Time: 19:34
 */
class model extends base
{
    private $client;
    function __construct()
    {
        require_once PROTECTED_DIR . 'vendor/autoload.php';
        $this->client = $client = new Predis\Client();
    }

    /**
     * @param $key
     * @param $val
     * @param int $expire
     */

    public function set($key, $val, $expire = 60)
    {
        $this->client->set($key, $val, 'EX', $expire);
    }

    /**
     * @param $key
     * @return string
     */

    public function get($key)
    {
        return $this->client->get($key);
    }
}
