<?php
error_reporting(E_ALL);

define('ROOT_DIR', str_replace('protected', '', __DIR__));
require_once ROOT_DIR . '/protected/config.php';
require_once CORE_DIR . 'autoload.php';
if(2 == $argc)    {
    staticBase::writeLog('notify', $argv[1]);
    $addresses = new addresses_service();
    $addresses->newTransaction($argv[1]);
}