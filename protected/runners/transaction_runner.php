<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 16.05.18
 * Time: 10:17
 */
set_time_limit(0);
error_reporting(E_ALL);
define('ROOT_DIR', str_replace('protected' . DIRECTORY_SEPARATOR . 'runners', '', __DIR__) . DIRECTORY_SEPARATOR);
require_once ROOT_DIR . '/protected/config.php';
require_once CORE_DIR . 'autoload.php';
require_once PROTECTED_DIR . 'vendor/autoload.php';
$addresses = new addresses_service();
$addresses->checkTransaction();
sleep(10);