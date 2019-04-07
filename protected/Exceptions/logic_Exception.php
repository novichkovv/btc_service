<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 03.06.18
 * Time: 23:00
 */
class logic_Exception extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Logic Exception: ' . $message, $code, $previous);
    }
}