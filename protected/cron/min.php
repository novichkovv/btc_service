<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 16.05.18
 * Time: 10:17
 */
define('ROOT_DIR', str_replace('protected' . DIRECTORY_SEPARATOR . 'cron', '', __DIR__));
define('PROJECT', 'bot');
require_once ROOT_DIR . '/protected/config.php';
require_once CORE_DIR . 'autoload.php';
investment_service::checkPayments();
queue_service::send();
bitcoin_service::checkTransactions();
