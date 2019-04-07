<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 31.03.18
 * Time: 15:58
 */
session_start();
require_once '../protected/config.php';
require_once CORE_DIR . 'autoload.php';
new router();
