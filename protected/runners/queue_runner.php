<?php
set_time_limit(0);
error_reporting(E_ALL);
define('ROOT_DIR', str_replace('protected' . DIRECTORY_SEPARATOR . 'runners', '', __DIR__) . DIRECTORY_SEPARATOR);
require_once ROOT_DIR . '/protected/config.php';
require_once CORE_DIR . 'autoload.php';
require_once PROTECTED_DIR . 'vendor/autoload.php';
try {
    $service = new queue_service();
    $service->run();
} catch (Exception $e) {
    staticBase::writeLog('queue_runner', $e->getMessage());
    staticBase::writeLog('queue_runner', $e->getCode());
    staticBase::writeLog('queue_runner', $e->getTraceAsString());
}
sleep(10);