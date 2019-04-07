<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 06.07.18
 * Time: 0:23
 */
error_reporting(E_ALL);
define('ROOT_DIR', str_replace('protected' . DIRECTORY_SEPARATOR . 'cron', '', __DIR__) . DIRECTORY_SEPARATOR);
require_once ROOT_DIR . '/protected/config.php';
require_once CORE_DIR . 'autoload.php';
ob_start();
register_shutdown_function(function() {
    $output = ob_get_clean();
    logs_class::date('cron', '24 hour - finish - ' . $output);
});
logs_class::date('cron', '24 hour - start');
//start cron
logs_class::cleanLogs();
echo 'finished';