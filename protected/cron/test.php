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
$addresses = new addresses_service();
$addresses->newTransaction('da77079d3375fbf1459b9b64815c689c4a2300bfb2534383ac0bacee85664d81');
