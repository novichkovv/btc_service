<?php
error_reporting(E_ALL);
define('ROOT_DIR', str_replace('protected' . DIRECTORY_SEPARATOR, '', __DIR__) . DIRECTORY_SEPARATOR);
require_once ROOT_DIR . '/protected/config.php';
require_once CORE_DIR . 'autoload.php';
staticBase::writeLog('notify', file_get_contents('php://input'));
if(2 == $argc)    {
    staticBase::writeLog('notify', $argv[1]);
}