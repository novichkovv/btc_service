<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 03.06.18
 * Time: 23:40
 */
class fatal_Exception extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        self::writeLog('FATAL', $message);
        tbot_class::sendToRole(1, 'Fatal Exception: ' . $message);
    }

    public static function writeLog($file, $value, $mode = 'a+') {
        $f = fopen(PUBLIC_DIR . 'tmp' . DS . 'logs' . DS . $file . '.log', $mode);
        fwrite($f, date('Y-m-d H:i:s') . ' - ' .print_r($value, true) . "\n");
        fclose($f);
    }
}